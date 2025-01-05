<?php
require 'db.php';
session_start();

if (!isset($_SESSION['uid']) || !isset($_SESSION['type'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['type'] != 'recruiter') {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];
$type = $_SESSION['type'];
$job_id = $_GET['job_id'];

$stmt1 = $conn->prepare("SELECT * FROM jobpost WHERE JobID = :job_id");
$stmt1->execute(['job_id' => $job_id]);
$data = $stmt1->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_id = $_GET['job_id'];
    $conn->beginTransaction();
    $stmt2 = $conn->prepare(
    "UPDATE jobpost SET Title=:title, JobType= :jobtype, salary=:salary, Description=:description, Skills=:skills WHERE JobID=:job_id");
    $stmt2->execute([
            'title' => $_POST['title'],
            'jobtype' => $_POST['jobtype'],
            'description' => $_POST['description'],
            'salary' => $_POST['salary'],
            'skills' => $_POST['skills'],
            'job_id' => $job_id
    ]);
    $conn->commit();
    header("Location: job_posts.php");
    exit();

}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update JobPost</title>
    <link rel="stylesheet" href="./css/index.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color:#bce1fa;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
            gap: 15px;
        }

        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar .nav-links a:hover {
            background-color: #2980b9;
        }

        .container {
            text-align: center;
            padding: 20px;
            margin: 50px auto;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 50%;
        }

        .container h1 {
            font-size: 24px;
            color: #333;
        }
        .contaner a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Nav Bar -->
    <div class="navbar">
        <div class="logo">A<i>JOB</i> Connect</div>
        <div class="nav-links">
            <a href="dashboard.php">Home</a>    
            <a href="profile.php">View profile</a>    
            <?php if ($type == 'job_seeker'): ?>
                <a href="jobs_applied.php">Jobs Applied</a>
            <?php elseif ($type == 'recruiter'): ?>
                <a href="job_posts.php">My Jobs</a>
                <a href="addPost.php">Add Posts</a>
            <?php endif; ?>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
        <h1>Edit Job Post:</h1>

    <form method="post" action="editJob.php?job_id=<?php echo $job_id ?>">
            <div>
                <label>Title:</label>
                <input type="text" name="title" required value="<?php echo $data["Title"] ?>">
                <label>Job Type:</label>
                <input type="text" name="jobtype" required value="<?php echo $data["JobType"] ?>">
                <label for="salary">Salary:</label>
                <input type="number" id="salary" name="salary" step="0.01" required value="<?php echo $data["salary"] ?>">
                <label>Description:</label>
                <textarea name="description"><?php echo $data["Description"] ?></textarea>
                <label>Skills:</label>
                <textarea name="skills"><?php echo $data["Skills"] ?></textarea>
            </div>
            <button type="submit">Save Changes</button>
        </form>
        
    </div>
    </div>
</body>
</html>
