<?php
$conn = new mysqli("localhost", "root", "", "abas_db");
$page = $_GET['page'];

foreach ($_POST['records'] as $record) {
    list($vmatricno, $vlastname, $vothernames, $vprogramme, $vcityname, $cgender, $vPhoneno) = explode('|', $record);

    $stmt = $conn->prepare("INSERT INTO intermediary (vmatricno, vlastname, vothernames, vprogramme, vcityname, cgender, vPhoneno, dateupluad, dateinserted) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), NOW())");
    $stmt->bind_param("sssssss", $vmatricno, $vlastname, $vothernames, $vprogramme, $vcityname, $cgender, $vPhoneno);
    $stmt->execute();
}

echo "<h3>Batch $page inserted successfully.</h3>";
echo "<a href='preview.php?page=" . ($page + 1) . "'>Continue to Next Batch →</a>";
?>
