<?php
// $conn = new mysqli("localhost", "root", "", "abas_db");

$host = '127.0.0.1';           // localhost works too
$db   = 'abas_db';          // your restored database
$user = 'root';
$pass = 'StrongPassword123';
$port = 3306;                   // default MySQL port

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date = $_GET['date'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';

// $sql = "SELECT * FROM intermediary WHERE DATE(dateinserted) = '$date'";
// if ($search) {
//     $searchEscaped = $conn->real_escape_string($search);
//     $sql .= " AND (
//         vmatricno LIKE '%$searchEscaped%' OR
//         vlastname LIKE '%$searchEscaped%' OR
//         vothernames LIKE '%$searchEscaped%' OR
//         vPhoneno LIKE '%$searchEscaped%'
//     )";
// }
// $result = $conn->query($sql);


$sql = "SELECT * FROM intermediary2 WHERE DATE(dateinserted) = '$date'";
if ($search) {
    $searchEscaped = $conn->real_escape_string($search);
    $sql .= " AND (
        vmatricno LIKE '%$searchEscaped%' OR
        vlastname LIKE '%$searchEscaped%' OR
        vothernames LIKE '%$searchEscaped%' OR
        vPhoneno LIKE '%$searchEscaped%'
    )";
}
$sql .= " ORDER BY dateinserted DESC LIMIT 25";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Upload Dashboard</title>
  <!-- Try loading Bootstrap from CDN -->
  <link id="bootstrap-cdn" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script>
    const testLink = document.createElement('link');
    testLink.rel = 'stylesheet';
    testLink.href = 'assets/bootstrap.min.css';

    const test = new Image();
    test.onerror = function () {
      document.getElementById('bootstrap-cdn')?.remove();
      document.head.appendChild(testLink);
    };
    test.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';
  </script>
</head>

<body class="container mt-5">
  <h2 class="mb-4">📊 Student Upload Dashboard</h2>
  <h2 class="mb-4">https://nounmisapp.org/dhs/admnouonline/noun_stcs.php</h2>

  <!-- Filter and Export -->
  <form method="get" class="row mb-3">
    <div class="col-md-3">
      <input type="date" name="date" value="<?= $date ?>" class="form-control">
    </div>
    <div class="col-md-5">
      <input type="text" name="search" value="<?= $search ?>" placeholder="Search matricno, name, phone" class="form-control">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary">Filter</button>
    </div>
    <div class="col-md-2">
      <a href="export.php?date=<?= $date ?>&search=<?= $search ?>" class="btn btn-success">Export to CSV</a>
    </div>
  </form>

  <!-- Upload Form -->
  <div class="card mb-4">
    <div class="card-body">
      <h4 class="card-title">⬆️ Upload New Excel File</h4>
      <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="excel_file" accept=".xlsx" class="form-control mb-3" required>
        <button type="submit" class="btn btn-primary">Upload & Preview</button>
      </form>
      <a href="clean_upload.php">Gyara</a>
      <a href="prepare_excel.php">Agyara</a>

    </div>
  </div>

  <!-- Data Table -->
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>vmatricno</th><th>vlastname</th><th>vothernames</th><th>cgender</th><th>vPhoneno</th><th>dateupluad</th><th>dateinserted</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['vmatricno'] ?></td>
          <td><?= $row['vlastname'] ?></td>
          <td><?= $row['vothernames'] ?></td>
          <td><?= $row['cgender'] ?></td>
          <td><?= $row['vPhoneno'] ?></td>
          <td><?= $row['dateupluad'] ?></td>
          <td><?= $row['dateinserted'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
