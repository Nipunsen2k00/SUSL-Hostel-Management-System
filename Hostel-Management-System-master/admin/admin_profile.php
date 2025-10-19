<?php
session_start();
require '../includes/config.inc.php';

// Initialize session variables safely
$fname = isset($_SESSION['fname']) ? $_SESSION['fname'] : "First name not set";
$lname = isset($_SESSION['lname']) ? $_SESSION['lname'] : "Last name not set";
$mob_no = isset($_SESSION['mob_no']) ? $_SESSION['mob_no'] : "Phone not set";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Username not set";

// Initialize $row to null
$row = null;

// Fetch hostel manager info only if username session is set
if(isset($_SESSION['username'])) {
    $sql = "SELECT * FROM hostel_manager_profile WHERE Username='".mysqli_real_escape_string($conn, $_SESSION['username'])."'";
    $result = mysqli_query($conn, $sql);

    if($result && mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>User Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<link href="../web_profile/css/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="../web_profile/css/font-awesome.min.css" />
<link rel="stylesheet" href="../web_profile/css/smoothbox.css" type='text/css' media="all" />
<link rel="stylesheet" href="../web_home/css_home/bootstrap.css">
<link rel="stylesheet" href="../web_home/css_home/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="../web_home/css_home/fontawesome-all.css">
<link rel="stylesheet" href="../web_home/css_home/flexslider.css" type="text/css" media="screen" />
<link href="//fonts.googleapis.com/css?family=Pathway+Gothic+One|Open+Sans:300,400,600,700,800" rel="stylesheet">
<link href="//fonts.googleapis.com/css?family=Poiret+One&subset=cyrillic,latin-ext" rel="stylesheet">
<script src="../web_profile/js/jquery-2.1.3.min.js"></script>
<script src="../web_profile/js/sliding.form.js"></script>
<script>
addEventListener("load", function() { setTimeout(function(){ window.scrollTo(0,1); }, 0); }, false);
</script>
</head>
<body>
<!-- Header -->
<header>
    <div class="container agile-banner_nav">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <h1><a class="navbar-brand" href="admin_home.php">SUSL</a></h1>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active"><a class="nav-link" href="admin_home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="create_hm.php">Appoint/Remove Hostel Manager</a></li>
                    <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="../includes/logout.inc.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </div>
</header>
<br><br><br><br><br>

<div class="main">
    <div id="wrapper" class="w3ls_wrapper w3layouts_wrapper">
        <div id="steps" style="margin:0 auto;" class="agileits w3_steps">
            <form id="formElem" name="formElem" action="#" method="post" class="w3_form w3l_form_fancy">

                <!-- Personal Info -->
                <fieldset class="step agileinfo w3ls_fancy_step">
                    <legend>Personal Info</legend>
                    <div class="abt-agile">
                        <div class="abt-agile-left"></div>
                        <div class="abt-agile-right">
                            <h3><?php echo $fname . " " . $lname; ?></h3>
                            <h5>Admin</h5>
                            <ul class="address">
                                <li>Username: <?php echo $username; ?></li>
                                <li>Phone: <?php echo $mob_no; ?></li>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                </fieldset>

                <!-- Hostel Manager Info -->
                <fieldset class="step agileinfo w3ls_fancy_step">
                    <legend>Hostel Manager Info</legend>
                    <?php if($row): ?>
                        <b>Managing Hostel:</b> <?php echo isset($row['Hostel_name']) ? $row['Hostel_name'] : "No data"; ?><br>
                        <b>Managing Mess:</b> <?php echo isset($row['Mess_name']) ? $row['Mess_name'] : "No data"; ?><br>
                        <b>Hostel Occupancy:</b> <?php echo isset($row['Hostel_Occupancy']) ? $row['Hostel_Occupancy'] : "No data"; ?> %<br>
                        <b>Mess Occupancy:</b> <?php echo isset($row['Mess_Occupancy']) ? $row['Mess_Occupancy'] : "No data"; ?> %<br>
                    <?php else: ?>
                        <b>Managing Hostel:</b> No data<br>
                        <b>Managing Mess:</b> No data<br>
                        <b>Hostel Occupancy:</b> No data<br>
                        <b>Mess Occupancy:</b> No data<br>
                    <?php endif; ?>
                </fieldset>

            </form>
        </div>
    </div>
</div>

<script src="../web_profile/js/smoothbox.jquery2.js"></script>
</body>
</html>
