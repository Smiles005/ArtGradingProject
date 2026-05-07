<?php
session_start();

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $class_name = trim($_POST['class_name']);
    $year = trim($_POST['year']);
    $semester = trim($_POST['semester']);
    $course_code = trim($_POST['course_code']);
    $section = trim($_POST['section']);
    $students_raw = trim($_POST['students']); // comma or newline separated

    // 1. Create the class
    $sql = "INSERT INTO class (class_name, year, semester, course_code, section)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $class_name, $year, $semester, $course_code, $section);
    $stmt->execute();
    $class_id = $stmt->insert_id;
    $stmt->close();

    // 2. Assign instructor to class
    $sql = "INSERT INTO instructor_class (instructor_id, class_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $instructor_id, $class_id);
    $stmt->execute();
    $stmt->close();

    // 3. Process student list
    $students = preg_split('/[\r\n,]+/', $students_raw);

    foreach ($students as $s) {
        $s = trim($s);
        if ($s === "") continue;

        // Check if student exists
        $sql = "SELECT student_id FROM student WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $s);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Student does not exist → create them with placeholder info
            $placeholder_bday = "2000-01-01";
            $placeholder_first = "Student";
            $placeholder_last = $s;

            $sql_insert = "INSERT INTO student (student_id, birthday, first_name, last_name, password_hash)
                           VALUES (?, ?, ?, ?, '')";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $s, $placeholder_bday, $placeholder_first, $placeholder_last);
            $stmt_insert->execute();
            $stmt_insert->close();
        }
        $stmt->close();

        // Enroll student in class
        $sql = "INSERT INTO student_class (student_id, class_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $s, $class_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to class page
    header("Location: instructor_classes.php");
    exit();

    $message = "Class created successfully!"; //this is only reachable if failed
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Class</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4b79a1, #283e51);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: white;
            padding: 40px;
            width: 450px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            text-align: center;
        }
        h2 {
            color: #283e51;
            margin-bottom: 20px;
        }
        input, textarea, select {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #aaa;
        }
        button {
            width: 95%;
            padding: 12px;
            background: #2e8b57;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #1f5e3c;
        }
        .msg {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Class</h2>

    <?php if ($message): ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="class_name" placeholder="Class Name" required>
        <input type="text" name="course_code" placeholder="Course Code (e.g., CSCI3100)" required>
        <input type="text" name="section" placeholder="Section (e.g., 01)" required>
        <input type="number" name="year" placeholder="Year (e.g., 2026)" required>

        <select name="semester" required>
            <option value="">Select Semester</option>
            <option value="Spring">Spring</option>
            <option value="Summer">Summer</option>
            <option value="Fall">Fall</option>
        </select>

        <textarea name="students" rows="6" placeholder="Enter student IDs (comma or newline separated)"></textarea>

        <button type="submit">Create Class</button>
    </form>
</div>

</body>
</html>
