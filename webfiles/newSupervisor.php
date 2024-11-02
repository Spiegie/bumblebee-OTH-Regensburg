<?php
session_start();
$userid=$_SESSION['userid'];
if(!isset($userid)){
	// falls Benutzer noch nicht angemeldet ist, wird er auf die Loginseite geleitet.
	header("Location: login.php");
	exit;
} else {
	// holt sich die daten �ber den Benutzer (TODO: vielleicht in funktion schreiben)
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
                    <li class="breadcrumb-item"><a href="manageinstruments.php">Instrumentenverwaltung</a></li>
                    <li class="breadcrumb-item active">Betreuer Zuweisungsbest&aumltigung</li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
            
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->
		
		<h1 class='page-header'>Zuweisungsbest&aumltigung f&uumlr Betreuer</h1>
		<?php 
			if ($_POST['selectNDS'] != '' and $_POST['baddsupervisor'] != '') {
			$nds = htmlspecialchars($_POST['selectNDS']);
			$sql = "SELECT id FROM new_users WHERE nds='".$nds."';";
			$res = mysqli_query($db,$sql);
			$row = mysqli_fetch_assoc($res);


			$sql = "INSERT INTO new_supervisors (instrumentid, userid)
				VALUES ('".$_POST['hinstid']."','".$row['id']."');";
			if (mysqli_query($db, $sql)) {
				header("Location: newSupervisor.php?msg=success");
				return;
			} else {
				header("Location: newSupervisor.php?msg=error");
				return;
			}
		} else {
			if ($_GET['msg'] == 'success') {
				echo "<div class='text-success'>Betreuer erfolgreich angelegt</div>";
			}
			if ($_GET['msg'] == 'error') {
				echo "<div class='text-danger'>Supervisor f&uumlr dieses Ger&aumlt bereits zugewiesen</div>";
			} 
			if ($_GET['msg'] == '') {
				echo "<div class='text-danger'>bitte eine NDS-kennung ausw�hlen</div>";
			}
		}
		?>

<!-- -->
		
		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>
	
</html>
