<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Prepare MIS Excel</title>
  <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body class="container mt-5">
  <h2 class="mb-4">🧼 Clean MIS Excel File</h2>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
      if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
          echo "<div class='alert alert-danger'>Upload failed. Please select a valid Excel file.</div>";
      } else {
          $inputFile = $_FILES['excel_file']['tmp_name'];
          $spreadsheet = IOFactory::load($inputFile);
          $sheet = $spreadsheet->getActiveSheet();
          $rows = $sheet->toArray();


          // Step 1: Skip non-header rows until we find the actual header
                while ($rows) {
                    $possibleHeader = array_shift($rows);
                    if (is_array($possibleHeader) && in_array('Sno', $possibleHeader)) {
                        $rawHeaders = $possibleHeader;
                        break;
                    }
                }

          // Step 2: Map headers to new names
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

        //   echo "<pre>Raw Headers:\n";
        //     print_r($rawHeaders);
        //     echo "</pre>";
        // ///////////////////////////////////////////
        //     echo "<pre>Detected Header Row:\n";
        //     print_r($rawHeaders);
        //     echo "</pre>";
        /////////////////////////////////////
            // if (!$rawHeaders) {
            //     echo "<div class='alert alert-danger'>❌ Could not detect header row. Please check your Excel format.</div>";
            //     exit;
            // }

          // Step 3: Identify column positions
          $columnMap = [];
          foreach ($rawHeaders as $index => $header) {
                $cleanHeader = trim($header);
                if (isset($mappedHeaders[$cleanHeader])) {
                    $columnMap[$index] = $mappedHeaders[$cleanHeader];
                }
            }
          // Step 4: Prepare cleaned data
          $cleanedData = [];
          $cleanedHeaders = array_values($mappedHeaders);
          unset($cleanedHeaders[array_search('vfirstname', $cleanedHeaders)]); // remove vfirstname
          $cleanedData[] = $cleanedHeaders;

          foreach ($rows as $row) {
              $data = [];
              foreach ($columnMap as $index => $newName) {
                  $data[$newName] = $row[$index] ?? null;
              }

              // Merge firstname + othername into vothernames
              $data['vothernames'] = trim(($data['vfirstname'] ?? '') . ' ' . ($data['vothernames'] ?? ''));
              unset($data['vfirstname']);

              $cleanedData[] = [
                  $data['vmatricno'],
                  $data['vlastname'],
                  $data['vothernames'],
                  $data['cmarital'],
                  $data['cgender'],
                  $data['cdisability'],
                  $data['vcentre'],
                  $data['vprogramme'],
                  $data['clevel'],
                  $data['vPhoneno']
              ];
          }

          // Step 5: Save cleaned file
          $outputSpreadsheet = new Spreadsheet();
          $outputSheet = $outputSpreadsheet->getActiveSheet();
          $outputSheet->fromArray($cleanedData);

        //   $outputPath = 'uploads/cleaned_' . time() . '.xlsx';
          $outputPath = 'uploads/cleaned/cleaned_mis_' . date('Ymd_His') . '.xlsx';
          $writer = new Xlsx($outputSpreadsheet);
          $writer->save($outputPath);

          // Step 6: Show download link
          echo "<div class='alert alert-success'><strong>✅ Excel cleaned successfully.</strong></div>";
          echo "<p>Saved as: <code>$outputPath</code></p>";
          echo "<a href='$outputPath' download class='btn btn-success'>Download Cleaned Excel</a>";
      }
  }
  ?>

  <form action="prepare_excel.php" method="post" enctype="multipart/form-data" class="mt-4">
    <input type="file" name="excel_file" accept=".xlsx" class="form-control mb-3" required>
    <button type="submit" class="btn btn-primary">Clean & Download</button>
  </form>
</body>
</html>
