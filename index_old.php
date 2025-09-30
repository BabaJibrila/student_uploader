<!DOCTYPE html>
<html>
<head>
  <title>Student Upload</title>
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> -->
  <!-- Try loading Bootstrap from CDN -->
<link id="bootstrap-cdn" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<!-- Fallback to local if CDN fails -->
<script>
  const testLink = document.createElement('link');
  testLink.rel = 'stylesheet';
  testLink.href = 'assets/bootstrap.min.css';

  const test = new Image();
  test.onerror = function () {
    // CDN failed, use local
    document.getElementById('bootstrap-cdn').remove();
    document.head.appendChild(testLink);
  };
  test.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';
</script>
</head>

<body class="container mt-5">
  <h2 class="mb-4">Upload Student Excel File</h2>
  <form action="upload.php" method="post" enctype="multipart/form-data" class="mb-3">
    <input type="file" name="excel_file" accept=".xlsx" class="form-control mb-3" required>
    <button type="submit" class="btn btn-primary">Upload & Preview</button>
  </form>
  <a href="dashboard.php" class="btn btn-secondary">View Upload Logs</a>
</body>
</html>
