<?php
session_start();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$conn = new mysqli("localhost", "root", "", "abas_db");

if ($_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
    $filename = 'uploads/' . time() . '_' . $_FILES['excel_file']['name'];
    move_uploaded_file($_FILES['excel_file']['tmp_name'], $filename);

    $spreadsheet = IOFactory::load($filename);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    array_shift($rows); // remove header

    $batchSize = 1000;
    $total = count($rows);
    $inserted = 0;
    $skipped = 0;

    for ($i = 0; $i < $total; $i += $batchSize) {
        $batch = array_slice($rows, $i, $batchSize);

        foreach ($batch as $row) {
            $vmatricno = $row[0];
            $check = $conn->query("SELECT 1 FROM r25092025 WHERE vmatricno = '$vmatricno'");
            if ($check->num_rows === 0) {
                $vlastname = $row[1];
                $vothernames = trim($row[2] . ' ' . $row[3]);
                $cgender = $row[5] ?? NULL;
                $vcityname = $row[7] ?? NULL;
                $vprogramme = $row[8] ?? NULL;
                $vPhoneno = $row[10] ?? NULL;

                $stmt = $conn->prepare("INSERT IGNORE INTO r25092025 (vmatricno, vlastname, vothernames, vprogramme, vcityname, cgender, vPhoneno, dateupluad, dateinserted) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), NOW())");
                $stmt->bind_param("sssssss", $vmatricno, $vlastname, $vothernames, $vprogramme, $vcityname, $cgender, $vPhoneno);
                $stmt->execute();
                $inserted++;
            } else {
                $skipped++;
            }
        }
    }

    echo "<h3>Upload Complete</h3>";
    echo "<p>Total records in file: $total</p>";
    echo "<p>Inserted: $inserted</p>";
    echo "<p>Skipped (already in DB): $skipped</p>";
    echo "<a href='index.php' class='btn btn-primary'>Return to Dashboard</a>";
} else {
    echo "Upload failed.";
}
?>
