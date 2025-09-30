<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$conn = new mysqli("localhost", "root", "", "abas_db");

// Load the uploaded Excel file
$filename = $_SESSION['excel_file']; // Make sure this is set from upload.php
$spreadsheet = IOFactory::load($filename);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

// Step 1: Remove the first row (e.g., "List of Students")
array_shift($rows);

// Step 2: Extract and rename headers
$rawHeaders = array_shift($rows);
$mappedHeaders = [
    'MatricNo'   => 'vmatricno',
    'LastName'   => 'vlastname',
    'FistName'   => 'vfirstname',
    'OtherName'  => 'vothernames',
    'Marital'    => 'cmarital',
    'Gender'     => 'cgender',
    'Disability' => 'cdisability',
    'Centre'     => 'vcentre',
    'Programme'  => 'vprogramme',
    'Level'      => 'clevel',
    'Phone'      => 'vPhoneno'
];

// Step 3: Identify column positions
$columnMap = [];
foreach ($rawHeaders as $index => $header) {
    if (isset($mappedHeaders[$header])) {
        $columnMap[$index] = $mappedHeaders[$header];
    }
}

// Step 4: Process rows
$inserted = 0;
$skipped = 0;
foreach ($rows as $row) {
    $data = [];
    foreach ($columnMap as $index => $newName) {
        $data[$newName] = $row[$index] ?? null;
    }

    // Merge firstname + othername into vothernames
    $data['vothernames'] = trim(($data['vfirstname'] ?? '') . ' ' . ($data['vothernames'] ?? ''));
    unset($data['vfirstname']); // Remove original vfirstname

    // Check for duplicates
    $vmatricno = $conn->real_escape_string($data['vmatricno']);
    $check = $conn->query("SELECT 1 FROM intermediary WHERE vmatricno = '$vmatricno'");
    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO intermediary (
            vmatricno, vlastname, vothernames, cmarital, cgender, cdisability,
            vcentre, vprogramme, clevel, vPhoneno, dateupluad, dateinserted
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), NOW())");

        $stmt->bind_param("ssssssssss",
            $data['vmatricno'], $data['vlastname'], $data['vothernames'],
            $data['cmarital'], $data['cgender'], $data['cdisability'],
            $data['vcentre'], $data['vprogramme'], $data['clevel'], $data['vPhoneno']
        );
        $stmt->execute();
        $inserted++;
    } else {
        $skipped++;
    }
}

echo "<h3>Upload Complete</h3>";
echo "<p>Inserted: $inserted</p>";
echo "<p>Skipped (duplicates): $skipped</p>";
echo "<a href='index.php' class='btn btn-primary'>Return to Dashboard</a>";
?>
