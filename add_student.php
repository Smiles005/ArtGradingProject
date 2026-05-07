<?php
$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = trim($_POST['student_id']);
    $birthday = trim($_POST['birthday']);
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);

    $sql = "INSERT INTO student (student_id, birthday, first_name, last_name)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $id, $birthday, $first, $last);
    $stmt->execute();
    $stmt->close();

    $message = "Student added successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
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
            width: 380px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            text-align: center;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #aaa;
        }
        button {
            width: 95%;
            padding: 12px;
            background: #4b79a1;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #35516a;
        }
        .msg {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Student</h2>

    <?php if ($message): ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="student_id" placeholder="Student ID" required>
        <input type="date" name="birthday" required>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>

        <button type="submit">Add Student</button>
    </form>
</div>

</body>
</html>
