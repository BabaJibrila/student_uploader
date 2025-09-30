<?php
session_start();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$conn = new mysqli("localhost", "root", "", "abas_db");

$filename = $_SESSION['excel_file'];
$spreadsheet = IOFactory::load($filename);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

$header = array_shift($rows);
$batchSize = 1000;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $batchSize;

$newRecords = [];
foreach ($rows as $row) {
    $vmatricno = $row[0];
    $check = $conn->query("SELECT 1 FROM intermediary WHERE vmatricno = '$vmatricno'");
    if ($check->num_rows === 0) {
        $newRecords[] = $row;
    }
}

$totalPages = ceil(count($newRecords) / $batchSize);
$currentBatch = array_slice($newRecords, $start, $batchSize);

echo "<h2>Preview Batch $page</h2>";
echo "<form action='insert.php?page=$page' method='post'>";
echo "<table border='1'><tr><th>vmatricno</th><th>vlastname</th><th>vothernames</th><th>cgender</th><th>vPhoneno</th></tr>";

foreach ($currentBatch as $row) {
    $vmatricno = $row[0];
    $vlastname = $row[1];
    $vothernames = trim($row[2] . ' ' . $row[3]);
    $cgender = $row[5] ?? NULL;
    $vPhoneno = $row[10] ?? NULL;

    echo "<tr><td>$vmatricno</td><td>$vlastname</td><td>$vothernames</td><td>$cgender</td><td>$vPhoneno</td></tr>";
    echo "<input type='hidden' name='records[]' value='" . implode('|', [$vmatricno, $vlastname, $vothernames, $cgender, $vPhoneno]) . "'>";
}
echo "</table><br>";
echo "<button type='submit'>Insert This Batch</button>";
echo "</form>";

if ($page > 1) echo "<a href='preview.php?page=" . ($page - 1) . "'>← Back</a> ";
if ($page < $totalPages) echo "<a href='preview.php?page=" . ($page + 1) . "'>Next →</a>";
?>
