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

$class_id = $_GET['class_id'];
$assignment_id = $_GET['assignment_id'] ?? null;

$name = "";
$description = "";

// Load existing assignment
if ($assignment_id) {
    $sql = "SELECT * FROM assignment WHERE assignment_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $a = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $name = $a['name'];
    $description = $a['description'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];

    if ($assignment_id) {
        $sql = "UPDATE assignment SET name=?, description=? WHERE assignment_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $description, $assignment_id);
    } else {
        $sql = "INSERT INTO assignment (class_id, name, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $class_id, $name, $description);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: class_assignments.php?class_id=$class_id");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Assignment</title>
<style>
<?php include "style.css"; ?>
</style>
</head>
<body>

<div class="container">
    <h2><?php echo $assignment_id ? "Edit Assignment" : "Add Assignment"; ?></h2>

    <form method="POST">
        <input type="text" name="name" placeholder="Assignment Name" value="<?php echo $name; ?>" required>
        <textarea name="description" placeholder="Description" required><?php echo $description; ?></textarea>

        <button type="submit">Save</button>
    </form>
</div>

</body>
</html>
