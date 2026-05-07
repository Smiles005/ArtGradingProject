<?php
$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$file_id = $_GET['file_id'];

$sql = "SELECT file_name, mime_type, file_data FROM assignment_submission_file WHERE file_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($file_name, $mime_type, $file_data);
$stmt->fetch();

header("Content-Type: $mime_type");
header("Content-Disposition: inline; filename=\"$file_name\"");
echo $file_data;
?>