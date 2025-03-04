<?php
require 'db.php';
session_start();

if (!isset($_SESSION['uid']) || !isset($_SESSION['type'])) {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];
$type = $_SESSION['type'];

$stmt = $conn->prepare("SELECT * FROM User WHERE UID = :UID");
$stmt->execute(['UID' => $uid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = 'Unknown User';
if ($user) {
    $username = $user['Username'];
} else {
    $username = 'Unknown User';
}

$stmt = $conn->prepare(" SELECT jp.JobID AS JobID, jp.Title AS Title, jp.salary AS salary, jp.Description AS Description, jp.JobType AS JobType, jp.Skills AS Skills, jp.RecruiterID, CASE WHEN a.JobID IS NOT NULL THEN 'Yes' ELSE 'No' END AS Applied FROM jobpost jp LEFT JOIN application a ON jp.JobID = a.JobID AND a.JobSeekerID = :uid ORDER BY jp.JobID;");

$stmt->execute([
    ':uid' => $_SESSION['uid']
]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .jobcontainer{
            display: flex;
            flex-direction: row;
            justify-content:space-between;
            padding: 15px;
            transition: 0.4s background-color;
        }
        .jobcontainer:hover{
            background-color:rgb(233, 233, 233);
            cursor: pointer;
        }
        .jobs{
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .titleandall{
            display: flex;
            gap: 10px;
            justify-content: left;
            margin-left: 10px;
        }
        .titleandall h1{
            color: #474a4a;
        }
        .titleandall p{
            color: #828787;
            margin-top: 25px;
        }
        .btns{
            margin-top: 15px;
            display: flex;
            gap: 20px;
        }
        .btns a{
            font-size: 20px;
            border: 1px solid;
            text-decoration: none;
            color: black;
            border-radius: 5px;
            padding: 10px;
            font-weight: 600;
        }
        .btns .apply{
            background-color:rgb(184, 253, 187);
            transition: 0.3s background-color color;
        }
        .btns .apply:hover{
            background-color:rgb(175, 233, 191);
            color: rgb(21, 21, 21);
        }
        .btns .description{
            background-color:rgb(199, 225, 232);
            transition: 0.3s background-color color;
        }
        .btns .description:hover{
            background-color:rgb(183, 208, 216);

        }
        .btns .description1{
            background-color:rgb(199, 225, 232);
            transition: 0.3s background-color color;
        }
        .btns .description1:hover{
            background-color:rgb(183, 208, 216);

        }
        .modal {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 50%;
            max-width: 600px;
            text-align: center;
            position: relative;
        }

        .modal-content h2 {
            margin-bottom: 15px;
        }

        .modal-content p {
            color: #333;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            color: #888;
        }

        .close-btn:hover {
            color: #333;
        }

        
     </style>
</head>
<body>
    <!-- Navi Bar -->
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

    <!-- Welcome -->
    <div class="container">
        <div class="heading">
            <h1>
                <?php
                if ($type == 'job_seeker') {
                    echo "Hello, Job Seeker $username!";
                } else if ($type == 'recruiter') {
                    echo "Hello, Recruiter $username!";
                }
                ?>
            </h1>
        </div>
    </div>
    <div class="container">
        <div class="jobs">
            <?php for ($i = 0; $i < count($data); $i++) { ?>
                <div class="jobcontainer">
                    <div class="titleandall">
                        <h1><?php echo $data[$i]["Title"] ?></h1>
                        <p>Job Type: <?php echo $data[$i]["JobType"] ?></p>
                        <p>Skill Set: <?php echo $data[$i]["Skills"] ?></p>
                        <p>Salary: <?php echo $data[$i]["salary"] ?></p>
                    </div>
                    <?php if($type=='job_seeker'){ ?>
                        <div class="btns">
                        <?php if($data[$i]["Applied"]=="No"){ ?>
                                <a href="apply.php?job_id=<?php echo $data[$i]["JobID"] ?>" class="apply">Apply</a>
                            <?php } else{ ?>
                                <a href="#" class="apply" onclick="alreadyapplied()">Applied</a>
                            <?php } ?>
                            <a href="#" class="description" onclick='openModal(<?php echo $data[$i]["JobID"] ?>)'>Description</a>
                        </div>
                        <div id="<?php echo $data[$i]["JobID"] ?>" class="modal">
                                <div class="modal-content">
                                <span class="close-btn" id="close-modal" onclick='closeModal(<?php echo $data[$i]["JobID"] ?>)'>&times;</span>
                            <h2><?php echo $data[$i]["Title"]; ?></h2>
                            <p>Job Type: <?php echo $data[$i]["JobType"]; ?></p>
                            <p>Skill Set: <?php echo $data[$i]["Skills"]; ?></p>
                            <p>Salary: <?php echo $data[$i]["salary"]; ?></p>
                            <p id="job-description-text">Job Description: <?php echo $data[$i]["Description"]; ?></p>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <div class="btns">
                            <a href="#" class="apply" onclick="showalert()">Apply</a>
                            <a href="#" class="description1" onclick="showalert()">Description</a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        function alreadyapplied(){
            alert("You have already applied for the job.")
        }
        function showalert() {
            alert("You must sign in as a job seeker!")
        }
        function openModal(cl) {
            console.log(cl)
            let part = document.getElementById(`${cl}`)
            part.style.display = "flex";
            let closeModalBtn = document.getElementById("close-modal");
            function closeModal() {
                let part = document.getElementById(`${cl}`)
                part.style.display = "none";
            }
            closeModalBtn.addEventListener("click", closeModal);
        }
        function closeModal(cl) {
            let part = document.getElementById(`${cl}`)
            part.style.display = "none";
        }

    </script>
</body>
</html>
