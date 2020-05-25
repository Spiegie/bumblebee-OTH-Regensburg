
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
if($user_arr['isAdmin'] != '1') {
	header("Location: welcome_bumble.php?errormsg=nopermission");
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
                    <li class="breadcrumb-item active">Adminseite</li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->
		<h1 class='page-header'>Adminseite</h1>
		
		<table class='table table-bordered'>
			<tr>
				<td><center><a href='manageusers.php'>Benutzerverwaltung</a></center></td>
				<td><center><a href='newUser.php'>neuen Benutzer anlegen</a></center></td>
			</tr>
			<tr>
				<td><center><a href='manageinstruments.php'>Instrumentenverwaltung</a></center></td>
				<td><center><a href='newInstrument.php'>neues Instrument anlegen</a></center></td>
			</tr>
			<tr>
				<td><center><a href='managebookings.php'>Buchungsverwaltung</a></td>
				<td><center><a href='telefonliste.php'>Telefonliste</a></td>
			</tr>
		</table>
<!-- -->
		
		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>
	
</html>
