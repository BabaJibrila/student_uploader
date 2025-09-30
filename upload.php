<?php
session_start();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// $conn = new mysqli("localhost", "root", "", "abas_db"

// Docker MySQL connection
$host = '127.0.0.1';           // localhost works too
$db   = 'abas_db';          // your restored database
$user = 'root';
$pass = 'StrongPassword123';
$port = 3306;                   // default MySQL port

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
    $filename = 'uploads/' . time() . '_' . basename($_FILES['excel_file']['name']);
    move_uploaded_file($_FILES['excel_file']['tmp_name'], $filename);

    $spreadsheet = IOFactory::load($filename);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    array_shift($rows); // Remove title row: "List of Students"
    $header = array_shift($rows); // Remove and capture actual header row

    // Map header columns to their indices
    $headerMap = array_flip($header);

    $total = count($rows);
    $inserted = 0;
    $skipped = 0;

    foreach ($rows as $row) {
        $vmatricno = $row[$headerMap['MatricNo']] ?? null;
        if (!$vmatricno) {
            $skipped++;
            continue;
        }

        // Check for duplicates
        $checkStmt = $conn->prepare("SELECT 1 FROM sep262025 WHERE vmatricno = ?");
        $checkStmt->bind_param("s", $vmatricno);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            $vlastname = $row[$headerMap['LastName']] ?? null;
            $fistName = $row[$headerMap['FistName']] ?? '';
            $otherName = $row[$headerMap['OtherName']] ?? '';
            $vothernames = trim($fistName . ' ' . $otherName);

            $cgender = $row[$headerMap['Gender']] ?? null;
            $vcityname = $row[$headerMap['Centre']] ?? null;
            $vprogramme = $row[$headerMap['Programme']] ?? null;
            $vPhoneno = $row[$headerMap['Phone']] ?? null;

            $stmt = $conn->prepare("INSERT INTO sep262025 (vmatricno, vlastname, vothernames, vprogramme, vcityname, cgender, vPhoneno, dateupluad, dateinserted) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), NOW())");
            $stmt->bind_param("sssssss", $vmatricno, $vlastname, $vothernames, $vprogramme, $vcityname, $cgender, $vPhoneno);
            $stmt->execute();
            $inserted++;
        } else {
            $skipped++;
        }

        $checkStmt->close();
    }

    echo "<h3>Upload Complete</h3>";
    echo "<p>Total records in file: $total</p>";
    echo "<p>Inserted: $inserted</p>";
    echo "<p>Skipped (already in DB or invalid): $skipped</p>";
    echo "<a href='index.php' class='btn btn-primary'>Return to Dashboard</a>";
} else {
    echo "<h3>Upload Failed</h3>";
    echo "<p>Error Code: " . $_FILES['excel_file']['error'] . "</p>";
}
?>
