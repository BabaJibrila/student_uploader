<?php
$conn = new mysqli("localhost", "root", "", "abas_db");

$date = $_GET['date'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM intermediary WHERE DATE(dateinserted) = '$date'";
if ($search) {
    $searchEscaped = $conn->real_escape_string($search);
    $sql .= " AND (
        vmatricno LIKE '%$searchEscaped%' OR
        vlastname LIKE '%$searchEscaped%' OR
        vothernames LIKE '%$searchEscaped%' OR
        vPhoneno LIKE '%$searchEscaped%'
    )";
}
$result = $conn->query($sql);

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=students_' . $date . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['vmatricno', 'vlastname', 'vothernames', 'cgender', 'vPhoneno', 'dateupluad', 'dateinserted']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['vmatricno'],
        $row['vlastname'],
        $row['vothernames'],
        $row['cgender'],
        $row['vPhoneno'],
        $row['dateupluad'],
        $row['dateinserted']
    ]);
}
fclose($output);
?>
