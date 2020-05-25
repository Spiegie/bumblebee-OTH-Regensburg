

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
                    <li class="breadcrumb-item active">neuer Benutzer</li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->

		<?php
		if(!empty($_POST)) {
			while(list($name,$value)=each($_POST)){
				$$name=htmlspecialchars($value);
			}
			var_dump($_POST); // todo temp
			
			if (isset($isAdmin)) {
				$isAdmin = '1';
			} else {
				$isAdmin = '0';
			}
			$passwd = sha1($passwd);
			
			$sql = "INSERT INTO new_users (name, nds, passwd , isAdmin, email)
			VALUES ('".$uname."','".$nds."','".$passwd."','".$isAdmin."','".$email."');";
			if (mysqli_query($db, $sql)) {
				header("Location: newUser.php?msg=success");
				return;
			} else {
				header("Location: newUser.php?msg=error");
				return;
			}
		}
		
		?>
		
		
		<h1 class='page-header'>Neuen Benutzer anlegen</h1>
		
		<?php
		if ($_GET['msg'] == 'success') {
			echo "<div class='text-success'>Benutzer erfolgreich angelegt</div>";
		}
		if ($_GET['msg'] == 'error') {
			echo "<div class='text-danger'>irgendwas doofes ist passiert</div>";
		}
		?>
		
		
		<form action="#" method="post">
		<table class="table table-bordered">
			<tr>
				<td>Name:</td>
				<td><input type='text' name='uname' class="form-control"></td>
			</tr>
			<tr>
				<td>nds</td>
				<td><input type='text' name='nds' class="form-control"></td>
			</tr>
			<tr>
				<td>Passwort</td>
				<td><input type='password' name='passwd' class='form-control'></td>
			</tr>
			<tr>
				<td>isAdmin</td>
				<td><input type='checkbox' name='isAdmin' class="form-control" value="1"></td>
			</tr>
			<tr>
				<td>email</td>
				<td><input type='text' name='email' class="form-control "></td>
			</tr>
		</table>
		<input type='submit' class='btn btn-block btn-success' value='Benutzer anlegen' name='erstellen'>
		</form>
		
<!-- -->
		
		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>
	
</html>
