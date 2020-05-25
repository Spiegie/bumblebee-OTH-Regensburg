

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
                    <li class="breadcrumb-item active">Benutzerseite </li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->
		<h1 class='page-header'>Benutzerseite</h1>

		<h3 class='page-header'>Ihre Daten</h2>
		<?php
		$refresh = 0;
		if ($_POST['bName'] == 'Namen ändern' ) {
			$sql = "UPDATE new_users 
				SET name='".htmlspecialchars($_POST['iName'])."' 
				WHERE id='".$user_arr['id']."';";
			if (!mysqli_query($db, $sql)) {
				echo "<span class='text-danger'>Fehler! Name wurde nicht geändert</span>";
			} else {
				$refresh = 1;
				echo "<span class='text-success'>Erfolg! Name wurde geändert</span>";
			}
		}
		if ($_POST['bemail'] == 'e-Mail ändern' ) {
			$sql = "UPDATE new_users 
				SET email='".htmlspecialchars($_POST['iemail'])."' 
				WHERE id='".$user_arr['id']."';";
			if (!mysqli_query($db, $sql)) {
				echo "<span class='text-danger'>Fehler! e-Mail wurde nicht geändert</span>";
			} else {
				$refresh = 1;
				echo "<span class='text-success'>Erfolg! e-Mail wurde geändert</span>";
			}
		}
		if ($_POST['bPW'] == 'Passwort ändern' and htmlspecialchars($_POST['iPW1']) == htmlspecialchars($_POST['iPW2']) ) {
			$sql = "UPDATE new_users 
				SET passwd='".sha1(htmlspecialchars($_POST['iPW1']))."' 
				WHERE id='".$user_arr['id']."';";
			if (!mysqli_query($db, $sql)) {
				echo "<span class='text-danger'>Fehler! Passwort wurde nicht geändert</span>";
			} else {
				$refresh = 1;
				echo "<span class='text-success'>Erfolg! Passwort wurde geändert</span>";
			}
		} elseif ($_POST['bPW'] != '') {
			echo "<span class='text-danger'>Fehler! Die Eingaben müssen übereinstimmen. Bitte versuchen Sie es nochmal.</span>";
		}

		if ($refresh = 1) {
			$sql="SELECT id, name, nds, isAdmin, email FROM new_users WHERE id='".$userid."';";
			$resultname=mysqli_query($db,$sql);
			$user_arr=mysqli_fetch_assoc($resultname);
			$username=$user_arr['name'];

		}
		
		
		?>
		
		<form action='userpage.php' method='post'>
		<table class='table'>
		<tr>
			<td><span>NDS-Kennung: </span></td>
			<td><?php echo $user_arr['nds']; ?></td>
			<td></td>
		</tr>

		<tr>
			<td><span>Name: <?php echo $user_arr['name']; ?></span></td>
			<td><input type='text' class='form-control' name='iName' placeholder=<?php echo $user_arr['name']; ?>></td>
			<td><input type='submit' class='btn btn-default' name='bName' value='Namen ändern'></td>
		</tr>
		<tr>
			<td><span>e-Mail: <?php echo $user_arr['email']; ?></span></td>
			<td><input type='text' class='form-control' name='iemail' placeholder=<?php echo $user_arr['email']?>></td>
			<td><input type='submit' class='btn btn-default' name= 'bemail' value='e-Mail ändern'></td>
		</tr>
		<tr>
			<td><span>Passwort:</span></td>
			<td> 
				<input type='password' class='form-control' name='iPW1'><br>
				<input type='password' class='form-control' name='iPW2'>
			</td>
			<td><input type='submit' class='btn btn-default' name='bPW' value='Passwort ändern'></td>
		</tr>
		</table>
		
		</form>
		

<h3 class='page-header'>weitere Funktionen</h3>
<div class="col-md-2">
</div>
<div class="col-md-8">
<form action='userbookings.php' method='post'>
	<input class="btn btn-info form-control" type="submit" value='eigene Buchungen anzeigen'  name='beigene Buchungen'>
</form>
</div>	
<?php
?>	




<!-- -->
		</div>
		</div>
		</div>
	</center></body>
</html>
