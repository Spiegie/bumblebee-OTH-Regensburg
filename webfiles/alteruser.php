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
					<li class="breadcrumb-item"><a href="adminpage.php">Adminseite</a></li>
					<li class="breadcrumb-item"><a href="manageusers.php">Benutzerverwaltung</a></li>
					<li class="breadcrumb-item"><?php echo $_GET['nds'];?></li>
				</ol>
			</div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>

		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">

<!-- -->
<?php //process data 
// change name
if ($_POST['Bname'] != '' and $_POST['Iname'] != '') {
	$sql = "UPDATE new_users 
		SET name='".htmlspecialchars($_POST['Iname'])."' 
		WHERE id='".htmlspecialchars($_POST['Huid'])."';";
	if (!mysqli_query($db, $sql)) {
		echo "<span class='text-danger'>Fehler! Name wurde nicht geändert</span>";
	} else {
		echo "<span class='text-success'>Erfolg! Name wurde geändert</span>";
	}
}
// change email
if ($_POST['Bemail'] != '' and $_POST['Iemail'] != '') {
	$sql = "UPDATE new_users 
		SET email='".htmlspecialchars($_POST['Iemail'])."' 
		WHERE id='".htmlspecialchars($_POST['Huid'])."';";
	if (!mysqli_query($db, $sql)) {
		echo "<span class='text-danger'>Fehler! e-Mail wurde nicht geändert</span>";
	} else {
		echo "<span class='text-success'>Erfolg! e-Mail wurde geändert</span>";
	}
}
// change password
if ($_POST['BchangePass'] != '' and $_POST['pwd1'] != '' and $_POST['pwd2'] != '') {
	if ($_POST['pwd1'] == $_POST['pwd2']) {
		$sql = "UPDATE new_users 
			SET passwd='".sha1($_POST['pwd1'])."' 
			WHERE id='".htmlspecialchars($_POST['Huid'])."';";
		if (!mysqli_query($db, $sql)) {
			echo "<span class='text-dagner'>Fehler! Passwort wurde nicht geändert</span>";
		} else {
			echo "<span class='text-success'>Erfolg! Passwort wurde geändert</span>";
		}
	} else {
		echo "<span class='text-warning'>die Eingaben stimmen nicht überein</span>";
	}
} elseif ($_POST['BchangePass'] != '') {
	echo "<span class='text-warning'>bitte füllen sie beide Eingabefelder aus</span>";
}	
// change permission
if ($_POST['selectInstname'] != '' and $_POST['BnewPermission'] != '') {
	$instId = htmlspecialchars($_POST['selectInstname']);
	$sql = "INSERT INTO new_permissions (userid,instrumentid)
		VALUES ('".htmlspecialchars($_POST['Huid'])."','".$instId."');";
	if (!mysqli_query($db, $sql)) {
		echo "<span class='text-dagner'>Fehler! Berechtigung konnte nicht vergeben werden</span>";
	} else {
		echo "<span class='text-success'>Erfolg! Berechtigung wurde vergeben</span>";
	}
} 
// change isAdmin
if ($_POST['Bchangeadmin'] != '') { 	
	$sql = "UPDATE new_users
		SET isAdmin='". ($_POST['HisAdmin']+1) % 2 ."'
		WHERE id='".htmlspecialchars($_POST['Huid'])."';";
	if (!mysqli_query($db, $sql)) {
		echo "<span class='text-dagner'>Fehler! Adminrechte konnten nicht vergeben werden</span>";
	} else {
		echo "<span class='text-success'>Erfolg! Adminrechte wurden vergeben</span>";
	}
}
// change suspended
if ($_POST['Bchangesuspended'] != '') { 	
	$sql = "UPDATE new_users
		SET suspended='". ($_POST['Hsuspended']+1) % 2 ."'
		WHERE id='".htmlspecialchars($_POST['Huid'])."';";
	if (!mysqli_query($db, $sql)) {
		echo "<span class='text-dagner'>Fehler! Benutzer konnte nicht deaktiviert werden</span>";
	} else {
		echo "<span class='text-success'>Erfolg! Benutzer wurde deaktiviert</span>";
	}
}


?>

<h1 class='page-header'>Benutzer <?php echo htmlspecialchars($_GET['nds'])?> bearbeiten</h1>

<?php

$sql = "SELECT * 
FROM new_users
WHERE nds='".$_GET['nds']."';";
$res_newusers = mysqli_query($db, $sql);

?>
<table class="table table-bordered table-striped">
<thead>
	<tr>
		<th>id</span></th>
		<th>name</span></th>
		<th>nds</span></th>
		<th>isAdmin</span></th>
		<th>email</span></th>
		<th>Berechtigungen</th>
	</tr>
</thead>
<?php
if ($users_arr = mysqli_fetch_assoc($res_newusers)) { 
	echo "<tr>";
	echo "<td>";
	echo $users_arr['id'];
	echo "</td>";
	echo "<td>";
	echo $users_arr['name'];
	echo "</td>";
	echo "<td>";
	echo $users_arr['nds'];
	echo "</td>";
	echo "<td>";
	echo $users_arr['isAdmin'];
	echo "</td>";
	echo "<td>";
	echo $users_arr['email'];
	echo "</td>";
	echo "<td>";


	echo "<table class='table table-bordered'>"; 
	$sql = "SELECT i.name,i.id
		FROM new_instruments i
		INNER JOIN new_permissions p
		ON i.id = p.instrumentid
		WHERE p.userid='".$users_arr['id']."'
		ORDER BY i.name;";
	$permitted_arr = mysqli_query($db, $sql);
	while ($perm = mysqli_fetch_assoc($permitted_arr)) {
		echo "<tr><td>";
		echo $perm['name'];
		echo "</td>";
		echo "<td><form action='deletepermission.php' method='post'>
			<input type='hidden' name='Hinstid' value='".$perm['id']."'>
			<input type='hidden' name='Huid' value='".$users_arr['id']."'>
			<input type='hidden' name='Hnds' value='".$_GET['nds']."'>
			<input type='submit' class='btn btn-danger' name='Bdelpermission' value='Berechtigung entfernen'>
			</form>";
	echo "</td></tr>";
	}



	echo "</table></td>";
	echo "</tr>";

}
?>
</table>
<div class='col-md-3'></div>
<div class='col-md-6'>


<h2 class="h2">Berechtigung für Instrumente hinzufügen</h2>
<table class='table'>
<tr><td>
<form action='alteruser.php?nds=<?php echo htmlspecialchars($_GET['nds']); ?>' method='post'>
<input type='hidden' name='Huid' value='<?php echo $users_arr['id'];?>'>
<input type='hidden' name='Hnds' value='<?php echo htmlspecialchars($_GET['nds']) ?>'>
	<select name='selectInstname' class='form-control'>
		<option selected value=''></option>";
<?php $sql = "SELECT i.* FROM new_instruments i 
				JOIN (SELECT i.id FROM new_instruments i 
				EXCEPT
				SELECT p.instrumentid FROM new_permissions p WHERE p.userid = '".$users_arr['id']."') x ON i.id = x.id ORDER BY i.name";
$inst_arr = mysqli_query($db, $sql);
echo "<p>".mysqli_error()."</p>";
while ($inst = mysqli_fetch_assoc($inst_arr)) {
	echo "<option value='".$inst['id']."'>".$inst['name']."</option>";
} 
?>
	</select>
	</td>
	<td>
	<input type='submit' class='btn btn-default' name='BnewPermission' value='Berechtigung hinzufügen'>
	</td>
</tr>
</form>
</table>


<h2>Adminberechtigung ändern</h2>
<table class='table'>
<form action='alteruser.php?nds=<?php echo htmlspecialchars($_GET['nds']); ?>' method='post'>
<tr>
<td>
<input type='hidden' name='Huid' value='<?php echo $users_arr['id']; ?>'>
<center>
<?php
if ($users_arr['isAdmin'] == 0){
	echo "<input type='submit' class='btn btn-warning' name='Bchangeadmin' value='Adminrechte vergeben'>";
	echo "<input type='hidden' name='HisAdmin' value='".$users_arr['isAdmin']."'>";
	echo "<input type='hidden' name='Huid' value='".$users_arr['id']."'>";
} else {
	echo "<input type='submit' class='btn btn-warning' name='Bchangeadmin' value='Adminrechte entziehen'>";
	echo "<input type='hidden' name='HisAdmin' value='".$users_arr['isAdmin']."'>";
	echo "<input type='hidden' name='Huid' value='".$users_arr['id']."'>";
}
?>
</center>
</td>
</tr>
</form>
</table>

<h2>Benutzer deaktivieren ändern</h2>
<table class='table'>
<form action='alteruser.php?nds=<?php echo htmlspecialchars($_GET['nds']); ?>' method='post'>
<tr>
<td>
<input type='hidden' name='Huid' value='<?php echo $users_arr['id']; ?>'>
<center>
<?php
if ($users_arr['suspended'] == 0){
	echo "<input type='submit' class='btn btn-warning' name='Bchangesuspended' value='Benutzer deaktivieren'>";
	echo "<input type='hidden' name='Hsuspended' value='".$users_arr['suspended']."'>";
	echo "<input type='hidden' name='Huid' value='".$users_arr['id']."'>";
} else {
	echo "<input type='submit' class='btn btn-warning' name='Bchangesuspended' value='Benutzer aktivieren'>";
	echo "<input type='hidden' name='Hsuspended' value='".$users_arr['suspended']."'>";
	echo "<input type='hidden' name='Huid' value='".$users_arr['id']."'>";
}
?>
</center>
</td>
</tr>
</form>
</table>

<h2>Passwort ändern</h2>
<table class='table'>
<form action='alteruser.php?nds=<?php echo htmlspecialchars($_GET['nds']); ?>' method='post'>
<tr>
<td>
<input type='password' class='form-control' name='pwd1'>
<input type='password' class='form-control' name='pwd2'>
<input type='hidden' name='Huid' value='<?php echo $users_arr['id']; ?>'>
</td>
<td>
<input type='submit' class='btn btn-warning' name='BchangePass' value='Passwort ändern'>
</td>
</tr>
</form>
</table>


<h2>E-Mail ändern</h2>
<table class='table'>
<form action='alteruser.php?nds=<?php echo htmlspecialchars($_GET['nds']); ?>' method='post'>
<tr>
<td>
<input type='hidden' name='Huid' value='<?php echo $users_arr['id']; ?>'>
<input type='input' class='form-control' name='Iemail' >
</td>
<td>
<input type='submit' class='btn btn-default' name='Bemail' value='Email ändern'> 
</td>
</tr>
</form>
</table>

<h2>Name ändern</h2>
<table class='table'>
<form action='alteruser.php?nds=<?php echo htmlspecialchars($_GET['nds']); ?>' method='post'>
<tr>
<td>
<input type='hidden' name='Huid' value='<?php echo $users_arr['id']; ?>'>
<input type='input' class='form-control' name='Iname' >
</td>
<td>
<input type='submit' class='btn btn-default' name='Bname' value='Name ändern'> 
</td>
</tr>
</form>
</table>

</div>

<!-- -->

		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>

</html>
