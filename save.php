<?php
//Connect to schema
$schema = "art_grading_project";
// Create connection
$conn = new mysqli("127.0.0.1", "webUser", "SuperSecurePasswordHere", $schema);
// Check connection
if ($conn->connect_error) {
echo $conn->connect_error;
die("Connection failed: " . $conn->connect_error);
}
//End connect to schema

file_put_contents("php_errors.log", print_r($_POST, true));
if (isset($_POST['jsonStrokes'])) {
    file_put_contents("php_errors.log", print_r($_POST, true));
    $stmt = $conn->prepare("INSERT INTO annotations (file_name, annotations_json, page_num)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE annotations_json = ?");

    $stmt->bind_param(
        "ssds",
        $_POST['fileName'],
        $_POST['jsonStrokes'],
        $_POST['filePage'],
        $_POST['jsonStrokes']
    );

    $stmt->execute();
    // $conn->query("INSERT INTO annotations (file_name, annotations_json, page_num)
    // values ('{$_POST['fileName']}', '{$_POST['jsonStrokes']}', {$_POST['filePage']})
    // on duplicate key update annotations_json = '{$_POST['jsonStrokes']}'");
}
?>