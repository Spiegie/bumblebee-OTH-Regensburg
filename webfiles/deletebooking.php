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
 // löscht Falls das fehl schlägt schickts zurück zur willkommen
if ($_POST['erinverstanden'] == 'Ich bin mir sicher!' && $_POST['bookingid'] != '') {
	
	$sql = "DELETE FROM new_bookings WHERE id='".$_POST['bookingid']."';";
	if (mysqli_query($db, $sql)) {
		header("Location: deletebooking.php?msg=success&instname=".$_GET['instname']."");
		return;
	} else {
		header("Location: welcome_bumble.php?msg=errormsg&instname=".$_GET['instname']."");
		return;
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
			<div class="pull-left" style="padding:5px">Sie sind eingeloggt als <span style='color:green'><?php echo $username ?></span> </br>
				<ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="welcome_bumble.php">Startseite</a></li>
                    <li class="breadcrumb-item active"><a href="instruments.php?instname=<?php echo htmlspecialchars($_GET['instname'])?>"><?php echo htmlspecialchars($_GET['instname'])?></a></li>
                    <li class="breadcrumb-item active">Löschbestätigung</li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->
		
		<h1 class='page-header'>Löschbestätigung für Buchung</h1>
		
		
		<?php 
		
		// erkennt ob superadmin oder user löschen und frag nochmal nach
		// TODO: nochmal alles auflisten
		if (htmlspecialchars(htmlspecialchars($_POST['deletebutton'])) == 'superdelete') {
			echo "
			<div class='text-danger'>WARNUNG! <br>
			Sie löschen gerade als Superadmin!<br> 
			Sie könnten also auch die Buchung eines anderen löschen?<br>
			Wollen sie den Eintrag wirklich löschen?</div>
			<form action='deletebooking.php?instname=".$_GET['instname']."' method='post'>
				<input type='hidden' name='bookingid' value='".$_POST['bookingid']."'> 
				<input type='submit' class='btn btn-block btn-success' name='erinverstanden' value='Ich bin mir sicher!'>
			</form>";
		}
		
		if (htmlspecialchars($_POST['deletebutton']) == 'delete') {
			echo "
			<div class='text-warning'>Wollen sie den Eintrag wirklich löschen?</div>
			<form action='deletebooking.php?instname=".$_GET['instname']."' method='post'>
				<input type='hidden' name='bookingid' value='".$_POST['bookingid']."'> 
				<input type='submit' class='btn btn-block btn-success' name='erinverstanden' value='Ich bin mir sicher!'>
			</form>";
		}
		
		if (htmlspecialchars($_GET['msg']) == 'success') {
			echo "<div class='text-success'>Buchung gelöscht!</div>";
		}
			
		?>

<!-- -->
		
		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>
	
</html>
