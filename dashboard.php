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

echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>";
echo "<div class='container mt-5'>";
echo "<h2>Records Inserted on $date</h2>";

echo "<form method='get' class='row mb-3'>";
echo "<div class='col-md-3'><input type='date' name='date' value='$date' class='form-control'></div>";
echo "<div class='col-md-5'><input type='text' name='search' value='$search' placeholder='Search matricno, name, phone' class='form-control'></div>";
echo "<div class='col-md-2'><button type='submit' class='btn btn-primary'>Filter</button></div>";
echo "<div class='col-md-2'><a href='export.php?date=$date&search=$search' class='btn btn-success'>Export to CSV</a></div>";
echo "</form>";

echo "<table class='table table-bordered'><thead><tr>
<th>vmatricno</th><th>vlastname</th><th>vothernames</th><th>cgender</th><th>vPhoneno</th><th>dateupluad</th><th>dateinserted</th>
</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
    <td>{$row['vmatricno']}</td>
    <td>{$row['vlastname']}</td>
    <td>{$row['vothernames']}</td>
    <td>{$row['cgender']}</td>
    <td>{$row['vPhoneno']}</td>
    <td>{$row['dateupluad']}</td>
    <td>{$row['dateinserted']}</td>
    </tr>";
}
echo "</tbody></table></div>";
?>
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
