<?php
$conn = new mysqli("127.0.0.1", "webUser", "SuperSecurePasswordHere", "art_grading_project");

if ($conn->connect_error) {
    die("Connection failed");
}

$fileName = $_POST['fileName'];
$pageNum = $_POST['filePage'];

$stmt = $conn->prepare("SELECT annotations_json 
    FROM annotations WHERE file_name = ? AND page_num = ?");

$stmt->bind_param("si", $fileName, $pageNum);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo $row['annotations_json']; // raw JSON string
} else {
    echo json_encode([
        "strokes" => [],
        "textBoxes" => []
    ]);
}
?>