<?php
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'public/libs/phpqrcode/qrlib.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

// Create QR directory
$qrDir = 'public/uploads/qr/';
if (!is_dir($qrDir)) {
    mkdir($qrDir, 0777, true);
}

// Get all aset
$result = $db->query('SELECT id_aset FROM m_aset ORDER BY id_aset');

if (!$result) {
    die("Query error: " . $db->error);
}

$count = 0;
while ($row = $result->fetch_assoc()) {
    $id = $row['id_aset'];
    $fileName = 'aset_' . $id . '.png';
    $filePath = $qrDir . $fileName;

    // Generate QR with full URL
    $url = BASEURL . '/aset/detail/' . $id;

    try {
        QRcode::png($url, $filePath, QR_ECLEVEL_L, 5);
        echo "✓ Generated QR for aset ID $id: $url" . PHP_EOL;
        $count++;
    } catch (Exception $e) {
        echo "✗ Error generating QR for ID $id: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "Total QR codes regenerated: $count" . PHP_EOL;
$db->close();
?>
