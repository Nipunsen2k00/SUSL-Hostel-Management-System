<?php
session_start();
require 'includes/config.inc.php';

// Check if hostel id is passed in URL
if(!isset($_GET['id'])){
    die("Hostel ID not provided.");
}

$hostel = $_GET['id'];

if(isset($_POST['submit'])){
    $roll = $_SESSION['roll'];
    $password = $_POST['pwd'];
    $message = $_POST['Message'];

    // Get student info
    $query = "SELECT * FROM Student WHERE Student_id = '$roll'";
    $result = mysqli_query($conn, $query);
    if(!$result){
        die("Query failed: ".mysqli_error($conn));
    }
    $row = mysqli_fetch_assoc($result);

    if(!$row){
        echo "<script>alert('Student not found');</script>";
    } 
    elseif(!password_verify($password, $row['Pwd'])) {
        echo "<script>alert('Incorrect Password!');</script>";
    }
    elseif(!is_null($row['Room_id'])) {
        echo "<script>alert('You have already been allotted a room');</script>";
    } 
    else {
        // Check if student already applied
        $checkApp = "SELECT * FROM Application WHERE Student_id = '$roll' AND Application_status = 1";
        $resApp = mysqli_query($conn, $checkApp);

        if(mysqli_num_rows($resApp) > 0){
            echo "<script>alert('You have already applied for a room');</script>";
        } 
        else {
            // Check payment status
            $checkPay = "SELECT * FROM Payment WHERE Student_id = '$roll'";
            $resPay = mysqli_query($conn, $checkPay);
            $rowPay = mysqli_fetch_assoc($resPay);

            if(!$rowPay || $rowPay['Status'] != 1){
                echo "<script>alert('Please pay fees before applying');</script>";
            } 
            else {
                // Get hostel info
                $resHostel = mysqli_query($conn, "SELECT * FROM Hostel WHERE Hostel_id='$hostel'");
                if(mysqli_num_rows($resHostel) == 0){
                    die("Invalid hostel selected.");
                }
                $rowHostel = mysqli_fetch_assoc($resHostel);
                $hostel_id = $rowHostel['Hostel_id'];

                // Insert application
                $insertApp = "INSERT INTO Application (Student_id, Hostel_id, Application_status, Message) 
                              VALUES ('$roll', '$hostel_id', 1, '$message')";
                $resInsert = mysqli_query($conn, $insertApp);

                if($resInsert){
                    // **Update Student table with Hostel_id**
                    $updateStudent = "UPDATE Student SET Hostel_id='$hostel_id' WHERE Student_id='$roll'";
                    mysqli_query($conn, $updateStudent);

                    echo "<script>alert('Application sent successfully');</script>";
                } 
                else {
                    echo "<script>alert('Failed to send application: ".mysqli_error($conn)."');</script>";
                }
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<title>Application Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">

<link rel="stylesheet" href="web_home/css_home/bootstrap.css">
<link rel="stylesheet" href="web_home/css_home/style.css" type="text/css" media="all">
<link rel="stylesheet" href="web_home/css_home/fontawesome-all.css">

<link href="//fonts.googleapis.com/css?family=Poiret+One&amp;subset=cyrillic,latin-ext" rel="stylesheet">
<link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin-ext" rel="stylesheet">
</head>

<body>

<!-- banner -->
<div class="inner-page-banner" id="home">
<header>
<div class="container agile-banner_nav">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
<h1><a class="navbar-brand" href="home.php">SUSL <span class="display"></span></a></h1>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
<ul class="navbar-nav ml-auto">
<li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
<li class="nav-item active"><a class="nav-link" href="services.php">Blocks</a></li>
<li class="nav-item"><a class="nav-link" href="payment_form.php">Payment</a></li>
<li class="nav-item"><a class="nav-link" href="services_mess.php">Mess</a></li>
<li class="dropdown nav-item">
<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown"><?php echo $_SESSION['roll']; ?><b class="caret"></b></a>
<ul class="dropdown-menu agile_short_dropdown">
<li><a href="profile.php">My Profile</a></li>
<li><a href="includes/logout.inc.php">Logout</a></li>
</ul>
</li>
</ul>
</div>
</nav>
</div>
</header>
</div>

<section class="contact py-5">
<div class="container">
<h2 class="heading text-capitalize mb-sm-5 mb-4">Application Form</h2>
<div class="mail_grid_w3l">
<form action="" method="POST">
<div class="row">
<div class="col-md-6 contact_left_grid">
<div class="contact-fields-w3ls">
<input type="text" name="Name" value="<?php echo $_SESSION['fname']." ".$_SESSION['lname']; ?>" readonly>
</div>
<div class="contact-fields-w3ls">
<input type="text" name="roll_no" value="<?php echo $_SESSION['roll']?>" readonly>
</div>
<div class="contact-fields-w3ls">
<?php
// Get hostel info from database
$resHostel = mysqli_query($conn, "SELECT * FROM Hostel WHERE Hostel_id='$hostel'");
$rowHostel = mysqli_fetch_assoc($resHostel);
?>
<input type="text" name="hostel_name" value="<?php echo $rowHostel['Hostel_name']; ?>" readonly>

</div>
<div class="contact-fields-w3ls">
<input type="password" name="pwd" placeholder="Password" required>
</div>
</div>
<div class="col-md-6 contact_left_grid">
<div class="contact-fields-w3ls">
<textarea name="Message" placeholder="Message..."></textarea>
</div>
<input type="submit" name="submit" value="Click to Apply">
</div>
</div>
</form>
</div>
</div>
</section>

<footer class="py-5">
<div class="container py-md-5">
<div class="footer-logo mb-5 text-center">
<a class="navbar-brand" href="https://www.sab.ac.lk/" target="_blank">SUSL <span class="display"> Hostel</span></a>
</div>
<div class="list-footer text-center">
<ul class="footer-nav">
<li><a href="home.php">Home</a></li>
<li><a href="services.php">Blocks</a></li>
<li><a href="profile.php">Profile</a></li>
</ul>
</div>
</div>
</footer>

<script src="web_home/js/jquery-2.2.3.min.js"></script>
<script src="web_home/js/bootstrap.js"></script>
</body>
</html>
