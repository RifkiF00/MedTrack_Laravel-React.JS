<?php

class WorkOrder extends Controller
{
    private function guard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }
    }

    private function isIPSRS()
    {
        return (($_SESSION['role'] ?? '') === 'Staf_IPSRS' || ($_SESSION['role'] ?? '') === 'Staf_Logistik');
    }

    private function isUnit()
    {
        return (($_SESSION['role'] ?? '') === 'Unit_RS');
    }

    private function uploadFotoKerusakan()
    {
        if (!isset($_FILES['foto_kerusakan']) || $_FILES['foto_kerusakan']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['foto_kerusakan']['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $fileName = $_FILES['foto_kerusakan']['name'];
        $tmpName = $_FILES['foto_kerusakan']['tmp_name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            return false;
        }

        $uploadDir = '../public/uploads/troubleshoot/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newName = 'trouble_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $targetPath = $uploadDir . $newName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            return false;
        }

        return $newName;
    }

    public function index()
    {
        $this->guard();

        $workOrderModel = $this->model('WorkOrder_model');
        $userModel = $this->model('User_model');

        $data['judul'] = 'E-Work Order - MedTrack IPSRS';
        $data['page_heading'] = 'E-Work Order';
        $data['page_subheading'] = 'Daftar laporan kerusakan dan tindak lanjut.';
        $data['content_view'] = 'workorder/index';
        $data['flash'] = getFlashMessage();
        $data['teknisi_list'] = $this->isIPSRS() ? $userModel->getUsersByRole('Staf_IPSRS') : [];

        if ($this->isIPSRS()) {
            $data['workorders'] = $workOrderModel->getAllWorkOrders();
        } elseif ($this->isUnit()) {
            $data['workorders'] = $workOrderModel->getWorkOrdersByPelapor($_SESSION['user_id']);
        } else {
            http_response_code(403);
            die('Akses ditolak.');
        }

        $this->view('templates/dashboard_layout', $data);
    }

    public function create()
    {
        $this->guard();

        if (!$this->isUnit()) {
            http_response_code(403);
            die('Hanya Unit yang dapat membuat Work Order.');
        }

        $asetModel = $this->model('Aset_model');

        $data['judul'] = 'Buat Work Order - MedTrack IPSRS';
        $data['page_heading'] = 'Buat Work Order';
        $data['page_subheading'] = 'Laporkan kerusakan aset.';
        $data['content_view'] = 'workorder/create';
        $data['errors'] = [];
        $data['old'] = [];

        $idRuang = $_SESSION['id_ruang'] ?? null;
        $data['aset'] = $idRuang ? $asetModel->getAsetByRuangan($idRuang) : [];

        $this->view('templates/dashboard_layout', $data);
    }

    public function store()
    {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }

        if (!$this->isUnit()) {
            http_response_code(403);
            die('Akses ditolak.');
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $workOrderModel = $this->model('WorkOrder_model');
        $asetModel = $this->model('Aset_model');

        $fotoKerusakan = $this->uploadFotoKerusakan();

        $formData = [
            'id_aset' => (int)($_POST['id_aset'] ?? 0),
            'tingkat_urgensi' => sanitizeInput($_POST['tingkat_urgensi'] ?? 'Sedang'),
            'deskripsi_kerusakan' => sanitizeInput($_POST['deskripsi_kerusakan'] ?? ''),
            'foto_kerusakan' => $fotoKerusakan ?: null,
            'status_ticket' => 'Open',
            'id_user_pelapor' => $_SESSION['user_id'],
            'id_teknisi_penanggungjawab' => null
        ];

        $errors = [];

        if ($formData['id_aset'] <= 0) {
            $errors[] = 'Aset wajib dipilih.';
        }

        if ($formData['deskripsi_kerusakan'] === '') {
            $errors[] = 'Deskripsi kerusakan wajib diisi.';
        }

        if ($fotoKerusakan === false) {
            $errors[] = 'Upload foto gagal. Gunakan format jpg, jpeg, png, atau webp.';
        }

        if (!in_array($formData['tingkat_urgensi'], ['Rendah', 'Sedang', 'Tinggi', 'Darurat'])) {
            $formData['tingkat_urgensi'] = 'Sedang';
        }

        if (!empty($errors)) {
            $data['judul'] = 'Buat Work Order - MedTrack IPSRS';
            $data['page_heading'] = 'Buat Work Order';
            $data['page_subheading'] = 'Laporkan kerusakan aset.';
            $data['content_view'] = 'workorder/create';
            $data['errors'] = $errors;
            $data['old'] = $formData;

            $idRuang = $_SESSION['id_ruang'] ?? null;
            $data['aset'] = $idRuang ? $asetModel->getAsetByRuangan($idRuang) : [];

            $this->view('templates/dashboard_layout', $data);
            return;
        }

        $saved = $workOrderModel->createWorkOrder($formData);

        if ($saved) {
            redirectWithMessage(BASEURL . '/workorder', 'Work Order berhasil dibuat.', 'success');
        } else {
            redirectWithMessage(BASEURL . '/workorder/create', 'Gagal membuat Work Order.', 'danger');
        }
    }

    public function updateStatus($id_ticket = null)
    {
        $this->guard();

        if (!$this->isIPSRS()) {
            http_response_code(403);
            die('Hanya Staf IPSRS yang dapat mengubah status Work Order.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id_ticket) {
            header('Location: ' . BASEURL . '/workorder');
            exit;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $workOrderModel = $this->model('WorkOrder_model');
        $ticket = $workOrderModel->getWorkOrderById($id_ticket);

        if (!$ticket) {
            redirectWithMessage(BASEURL . '/workorder', 'Ticket tidak ditemukan.', 'danger');
        }

        $statusBaru = sanitizeInput($_POST['status_ticket'] ?? '');
        $catatan = sanitizeInput($_POST['catatan_status'] ?? '');
        $statusValid = ['Open', 'Pengecekan', 'Dikerjakan', 'Closed'];

        if (!in_array($statusBaru, $statusValid)) {
            redirectWithMessage(BASEURL . '/workorder', 'Status Work Order tidak valid.', 'danger');
        }

        $updated = $workOrderModel->updateStatusTicket($id_ticket, $statusBaru);

        if ($updated) {
            $workOrderModel->addStatusLog([
                'id_ticket' => $id_ticket,
                'status_lama' => $ticket['status_ticket'],
                'status_baru' => $statusBaru,
                'catatan' => $catatan,
                'diubah_oleh' => $_SESSION['user_id']
            ]);

            redirectWithMessage(BASEURL . '/workorder', 'Status Work Order berhasil diperbarui.', 'success');
        } else {
            redirectWithMessage(BASEURL . '/workorder', 'Gagal memperbarui status Work Order.', 'danger');
        }
    }

    public function assignTeknisi($id_ticket = null)
    {
        $this->guard();

        if (!$this->isIPSRS()) {
            http_response_code(403);
            die('Hanya Staf IPSRS yang dapat meng-assign teknisi.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id_ticket) {
            header('Location: ' . BASEURL . '/workorder');
            exit;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $idTeknisi = (int)($_POST['id_teknisi_penanggungjawab'] ?? 0);

        $workOrderModel = $this->model('WorkOrder_model');
        $updated = $workOrderModel->assignTeknisi($id_ticket, $idTeknisi);

        if ($updated) {
            redirectWithMessage(BASEURL . '/workorder', 'Teknisi berhasil ditetapkan.', 'success');
        } else {
            redirectWithMessage(BASEURL . '/workorder', 'Gagal menetapkan teknisi.', 'danger');
        }
    }
}