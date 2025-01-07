<?php
include('../dbconn.php');

// Get year range
$yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM agihan_category");
$yearRange = $yearRangeResult->fetch_assoc();
$minYear = $yearRange['min_year'];
$maxYear = $yearRange['max_year'];

// Prepare data for CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=overview_report.csv');

$output = fopen('php://output', 'w');

// Add a title for the first section
fputcsv($output, ["KATEGORI AGIHAN"]);
fputcsv($output, array_merge(['Category'], range($minYear, $maxYear)));

// Fetch and write data for KATEGORI AGIHAN
$stmt = $dbconn->prepare("SELECT DISTINCT c.category_name FROM agihan_category a JOIN category c ON a.category_id = c.category_id WHERE c.category_type = 'agihan'");
$stmt->execute();
$sumberResult = $stmt->get_result();

while ($sumber = $sumberResult->fetch_assoc()) {
    $row = [$sumber['category_name']];
    for ($year = $minYear; $year <= $maxYear; $year++) {
        $stmtAmount = $dbconn->prepare("SELECT COALESCE(SUM(a.amount), 0) AS amount FROM agihan_category a JOIN category c ON a.category_id = c.category_id WHERE a.years = ? AND c.category_name = ?");
        $stmtAmount->bind_param("is", $year, $sumber['category_name']);
        $stmtAmount->execute();
        $amountResult = $stmtAmount->get_result();
        $row[] = $amountResult->fetch_assoc()['amount'] ?? 0;
        $stmtAmount->close();
    }
    fputcsv($output, $row);
}

// Add a separator row
fputcsv($output, []);
fputcsv($output, ["KATEGORI ASNAF"]);
fputcsv($output, array_merge(['Category'], range($minYear, $maxYear)));

// Fetch and write data for KATEGORI ASNAF
$stmt = $dbconn->prepare("SELECT DISTINCT c.category_name FROM agihan_asnaf a JOIN category c ON a.category_id = c.category_id WHERE c.category_type = 'asnaf'");
$stmt->execute();
$jenisResult = $stmt->get_result();

while ($jenis = $jenisResult->fetch_assoc()) {
    $row = [$jenis['category_name']];
    for ($year = $minYear; $year <= $maxYear; $year++) {
        $stmtAmount = $dbconn->prepare("SELECT COALESCE(SUM(a.amount), 0) AS amount FROM agihan_asnaf a JOIN category c ON a.category_id = c.category_id WHERE a.years = ? AND c.category_name = ?");
        $stmtAmount->bind_param("is", $year, $jenis['category_name']);
        $stmtAmount->execute();
        $amountResult = $stmtAmount->get_result();
        $row[] = $amountResult->fetch_assoc()['amount'] ?? 0;
        $stmtAmount->close();
    }
    fputcsv($output, $row);
}

fclose($output);
exit;
