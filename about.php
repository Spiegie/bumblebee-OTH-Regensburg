
<!-- -->
<?php
session_start();
$userid=$_SESSION['userid'];
if(!isset($userid)){
	// falls Benutzer noch nicht angemeldet ist, wird er auf die Loginseite geleitet.
	header("Location: login.php");
	exit;
} else {
	// holt sich die daten über den Benutzer (TODO: vielleicht in funktion schreiben)
	require "DB/connect.inc.php";
	$sql="SELECT id, name, nds, isAdmin, email FROM new_users WHERE id='".$userid."';";
	$resultname=mysqli_query($db,$sql);
	$user_arr=mysqli_fetch_assoc($resultname);
	$username=$user_arr['name'];
}
if ($user_arr['isAdmin']=='1') {
	if ($_POST['ADMINMODE'] == 'Superadmin Modus starten') {
		$_SESSION['adminmode'] = '1';
	}
	if ($_POST['ADMINMODE'] == 'Superadmin Modus verlassen') {
		$_SESSION['adminmode'] = '0';
	}
}
?>

<html>
	<head>
		<title> BumBee </title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
	<body> <center> 
		<nav class="navbar navbar-default"> 
			<div class="pull-left" style="padding:5px">Sie sind eingeloggt als <span style='color:green'><?php echo $username ?></span> <br>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="welcome_bumble.php">Startseite</a></li>
                    <li class="breadcrumb-item active">Über Bumblebee</li>
                </ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->

<h2> Über Bumblebee </h2>

Diese Webseite wurde von Michael Spiegelhalter (git: Spiegie) entwickelt. <br>
Sie ist unter der <a href='LICENSE'>MIT Lizenz</a> entwickelt worden. <br> 
Neuere Versionen können auf github unter <a target="_blank" href="https://github.com/Spiegie/bumblebee-OTH-Regensburg">https://github.com/Spiegie/bumblebee-OTH-Regensburg</a> bezogen werden. <br>



<!-- -->
		</div>
		</div>
		</div>
	</center></body>
</html>
