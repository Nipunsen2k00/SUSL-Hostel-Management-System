<?php
session_start();
require 'includes/config.inc.php';

// Check if user is logged in
if(!isset($_SESSION['roll']) || !isset($_SESSION['fname']) || !isset($_SESSION['lname'])){
    header("Location: login.php");
    exit();
}


$mess_id = intval($_GET['id']); // make sure it's an integer
$messQuery = "SELECT * FROM Mess WHERE Mess_id = $mess_id";
$messResult = mysqli_query($conn, $messQuery);
$mess = mysqli_fetch_assoc($messResult);
$mess_name = $mess ? $mess['Mess_name'] : 'Invalid Mess';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Application Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<link rel="stylesheet" href="web_home/css_home/bootstrap.css">
<link rel="stylesheet" href="web_home/css_home/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="web_home/css_home/fontawesome-all.css">
<link href="//fonts.googleapis.com/css?family=Poiret+One&amp;subset=cyrillic,latin-ext" rel="stylesheet">
<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
</head>

<body>

<div class="inner-page-banner" id="home">
<header>
    <div class="container agile-banner_nav">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <h1><a class="navbar-brand" href="home.php">NITK <span class="display"></span></a></h1>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Blocks</a></li>
                    <li class="nav-item"><a class="nav-link" href="payment_form.php">Payment</a></li>
                    <li class="nav-item active"><a class="nav-link" href="services_mess.php">Mess</a></li>
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
            <form action="application_form_mess.php?id=<?php echo $mess_id; ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 contact_left_grid">
                        <div class="contact-fields-w3ls">
                            <input type="text" name="Name" value="<?php echo $_SESSION['fname']." ".$_SESSION['lname']; ?>" disabled>
                        </div>
                        <div class="contact-fields-w3ls">
                            <input type="text" name="roll_no" value="<?php echo $_SESSION['roll']?>" disabled>
                        </div>
                        <div class="contact-fields-w3ls">
                            <input type="text" name="mess" value="<?php echo $mess_name; ?>" disabled>
                        </div>
                        <div class="contact-fields-w3ls">
                            <input type="password" name="pwd" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="col-md-6 contact_left_grid">
                        <div class="contact-fields-w3ls">
                            <textarea name="Message" placeholder="Message..."></textarea>
                        </div>
                        <input type="submit" name="submitmess" value="Click to Apply">
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<footer class="py-5">
    <div class="container py-md-5">
        <div class="footer-logo mb-5 text-center">
            <a class="navbar-brand" href="https://www.sab.ac.lk/" target="_blank">NITK <span class="display"> Surathkal</span></a>
        </div>
    </div>
</footer>

<script type="text/javascript" src="web_home/js/jquery-2.2.3.min.js"></script>
<script type="text/javascript" src="web_home/js/bootstrap.js"></script>
</body>
</html>

<?php
if(isset($_POST['submitmess'])){
    $roll = $_SESSION['roll'];
    $password = $_POST['pwd'];
    $message = $_POST['Message'];

    // Get student info
    $query = "SELECT * FROM Student WHERE Student_id = '$roll'";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);

    if(!$student){
        echo "<script>alert('Student not found');</script>";
        exit();
    }

    // Secure password check
    if(!password_verify($password, $student['Pwd'])){
        echo "<script>alert('Incorrect Password!');</script>";
        exit();
    }

    // Check if student already has a mess card
    if(!is_null($student['Mess_card_id'])){
        echo "<script>alert('You have already been allotted a mess');</script>";
        exit();
    }

    // Check if student already applied
    $checkApp = "SELECT * FROM Application_mess WHERE Student_id = '$roll'";
    $resApp = mysqli_query($conn, $checkApp);
    if(mysqli_num_rows($resApp) > 0){
        echo "<script>alert('You have already applied for a mess');</script>";
        exit();
    }

    // Check payment status
    $checkPay = "SELECT * FROM Payment WHERE Student_id = '$roll'";
    $resPay = mysqli_query($conn, $checkPay);
    $pay = mysqli_fetch_assoc($resPay);
    if(!$pay || $pay['Status'] != 1){
        echo "<script>alert('Please pay fees before applying');</script>";
        exit();
    }

    // Insert application
    if(!$mess){
        echo "<script>alert('Invalid Mess selected');</script>";
        exit();
    }

// Find first available mess card for this mess
$cardQuery = "SELECT * FROM mess_allocation WHERE Mess_id = $mess_id AND Allocated = 0 LIMIT 1";
$cardResult = mysqli_query($conn, $cardQuery);
$card = mysqli_fetch_assoc($cardResult);

if(!$card){
    echo "<script>alert('No mess cards available for this mess');</script>";
    exit();
}
$card_id = $card['Mess_card_id'];

// Insert application
$insert = "INSERT INTO Application_mess (Student_id, Mess_id, Application_status, Message, Mess_card_No) 
           VALUES ('$roll', '$mess_id', 1, '$message', '$card_id')";
$insertRes = mysqli_query($conn, $insert);

if($insertRes){
    // Update student's mess_card_id
    $updateStudent = "UPDATE Student SET Mess_card_id = $card_id WHERE Student_id = '$roll'";
    mysqli_query($conn, $updateStudent);

    // Update mess_allocation
    $updateCard = "UPDATE mess_allocation SET Allocated = 1, Student_id = '$roll' WHERE Mess_card_id = $card_id";
    mysqli_query($conn, $updateCard);

    echo "<script>alert('Application sent successfully');</script>";
} else {
    echo "<script>alert('Failed to send application: ".mysqli_error($conn)."');</script>";
}

}
?>
