<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Direct PDO connection
    $pdo = new PDO('mysql:host=localhost;dbname=medtrack_ipsrs_db', 'root', '');

    echo "<h2>🔍 DATABASE DIAGNOSTIC</h2>";

    // Get all unique status_kondisi values
    $query = "SELECT DISTINCT status_kondisi FROM m_aset ORDER BY status_kondisi";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);

    echo "<h3>Unique Status Kondisi in Database:</h3>";
    echo "<pre>";
    foreach ($results as $row) {
        echo "- '" . $row->status_kondisi . "'\n";
    }
    echo "</pre>";

    // Count by each kondisi
    echo "<h3>Count by Kondisi:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Status Kondisi</th><th>Count</th></tr>";

    foreach ($results as $row) {
        $query = "SELECT COUNT(*) as cnt FROM m_aset WHERE status_kondisi = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$row->status_kondisi]);
        $count_result = $stmt->fetch(PDO::FETCH_OBJ);

        echo "<tr>";
        echo "<td><code>" . htmlspecialchars($row->status_kondisi) . "</code></td>";
        echo "<td>" . $count_result->cnt . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Show all assets
    echo "<h3>All Assets:</h3>";
    $query = "SELECT id_aset, kode_label, nama_alat, status_kondisi FROM m_aset LIMIT 20";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $assets = $stmt->fetchAll(PDO::FETCH_OBJ);

    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Kode</th><th>Nama</th><th>Status (exact)</th></tr>";
    foreach ($assets as $aset) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($aset->kode_label) . "</td>";
        echo "<td>" . htmlspecialchars($aset->nama_alat) . "</td>";
        echo "<td><code>" . htmlspecialchars($aset->status_kondisi) . "</code></td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "<h2>❌ ERROR</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
