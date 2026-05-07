<?php
session_start();

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$student_id = $_GET['student_id'];
$class_id = $_GET['class_id'];

$message = "";

// Load student
$sql = "SELECT * FROM student WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $birthday = $_POST['birthday'];

    $sql = "UPDATE student SET first_name=?, last_name=?, birthday=? WHERE student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $first, $last, $birthday, $student_id);
    $stmt->execute();
    $stmt->close();

    header("Location: instructor_classes.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>
<style>
<?php include "style.css"; ?>
</style>
</head>
<body>

<div class="container">
    <h2>Edit Student</h2>

    <form method="POST">
        <input type="text" name="first_name" value="<?php echo $student['first_name']; ?>" required>
        <input type="text" name="last_name" value="<?php echo $student['last_name']; ?>" required>
        <input type="date" name="birthday" value="<?php echo $student['birthday']; ?>" required>

        <button type="submit">Save</button>
    </form>
</div>

</body>
</html>
