<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/Database.php';

$database = new Database();
$db = $database->connect();

echo "<h2>🔍 DATABASE DIAGNOSTIC</h2>";

// Get all unique status_kondisi values
$query = "SELECT DISTINCT status_kondisi FROM m_aset ORDER BY status_kondisi";
$stmt = $db->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_OBJ);

echo "<h3>Unique Status Kondisi in Database:</h3>";
echo "<ul>";
foreach ($results as $row) {
    echo "<li>'" . htmlspecialchars($row->status_kondisi) . "'</li>";
}
echo "</ul>";

// Count by each kondisi
echo "<h3>Count by Kondisi:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Status Kondisi</th><th>Count</th></tr>";

foreach ($results as $row) {
    $query = "SELECT COUNT(*) as cnt FROM m_aset WHERE status_kondisi = :kondisi";
    $stmt = $db->prepare($query);
    $stmt->execute([':kondisi' => $row->status_kondisi]);
    $count_result = $stmt->fetch(PDO::FETCH_OBJ);
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row->status_kondisi) . "</td>";
    echo "<td>" . $count_result->cnt . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show first 5 assets with their kondisi
echo "<h3>Sample Assets:</h3>";
$query = "SELECT id_aset, kode_label, nama_alat, status_kondisi FROM m_aset LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$assets = $stmt->fetchAll(PDO::FETCH_OBJ);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Kode</th><th>Nama</th><th>Status Kondisi (exact)</th></tr>";
foreach ($assets as $aset) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($aset->kode_label) . "</td>";
    echo "<td>" . htmlspecialchars($aset->nama_alat) . "</td>";
    echo "<td><code>" . htmlspecialchars($aset->status_kondisi) . "</code></td>";
    echo "</tr>";
}
echo "</table>";

?>
