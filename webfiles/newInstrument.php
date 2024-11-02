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
                    <li class="breadcrumb-item"><a href="manageinstruments.php">Instrumentenverwaltung</a></li>
                    <li class="breadcrumb-item">neues Instrument</li>
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
            foreach($_POST as $name => $value) {
				$$name=htmlspecialchars($value);
			}
			$supervisorerror = 0;
            $instinserterror = 0;
			$sql = "INSERT INTO new_instruments (name, usualopen, usualclose )
                VALUES ('".$instrumentname."','".$openTime."','".$closeTime."');";
			if (!mysqli_query($db, $sql)) {
                $instinserterror = 1;
			} 
            if ($instinserterror != 1 and $supervisor != '') {
                $sql = "SELECT id FROM new_instruments WHERE id = @@Identity;";
                if (!$res=mysqli_query($db,$sql)) {
                    $supervisorerror = 1;
                }
                $currid_arr=mysqli_fetch_array($res);
                $sql = "SELECT id 
                    FROM new_users 
                    WHERE nds='".$supervisor."';";
                if (!$res=mysqli_query($db,$sql)) {
                    $supervisorerror = 1;
                } 
                $id_arr=mysqli_fetch_assoc($res);
                $sql = "INSERT INTO new_supervisors (instrumentid, userid)
                    VALUES ('".$currid_arr[0]."','".$id_arr['id']."');";
                if (!mysqli_query($db, $sql)) {
                    $supervisorerror = 1;
                }
            }
            
            if ($supervisorerror == 1) {
                header("Location: newInstrument.php?msg=supervisorerror");
                return;
            } elseif ($instinserterror == 1) {
                header("Location: newInstrument.php?msg=insterror");
                return;
            } else {
                header("Location: newInstrument.php?msg=success");
                return;
            }
		}
		
		?>
		
		
		<h1 class='page-header'>Neues Instrument anlegen</h1>
		
		<?php
		if ($_GET['msg'] == 'success') {
			echo "<div class='text-success'>Instrument erfolgreich angelegt</div>";
		}
		if ($_GET['msg'] == 'insterror') {
			echo "<div class='text-danger'>Fehler! Instrument konnte nicht angelegt werden</div>";
		} 
        if ($_GET['msg'] == 'supervisorerror') {
			echo "<div class='text-warning'>Instrument erfolgreich angelegt <br> Aber Instrument konnte nicht mit Supervisor verknüpft werden</div>";
		} 
		?>
		
		<form action="#" method="post">
		<table class="table table-bordered">
			<tr>
				<td>Name:</td>
				<td><input type='text' name='instrumentname' class="form-control"></td>
			</tr>
			<tr>
				<td>offen ab</td>
				<td><input type='time' name='openTime' class="form-control" value="06:00"></td>
			</tr>
			<tr>
				<td>offen bis</td>
				<td><input type='time' name='closeTime' class="form-control" value="20:00"></td>
			</tr>
			<tr>
				<td>Betreuer (nds) (nicht implementiert)</td>
				<td><select name='supervisor' class='form-control'>
                    <option selected value=''></option>
                    <?php 
                    $sql = 'SELECT nds FROM new_users ORDER BY nds';
                    $res = mysqli_query($db, $sql);
                    while ($allusersnds_arr = mysqli_fetch_assoc($res)){
                        echo "<option value='".$allusersnds_arr['nds']."'>" . $allusersnds_arr['nds'] . "</option>";
                    } 
                    ?>
                </select></td>

			</tr>
		</table>
		<input type='submit' class='btn btn-block btn-success' value='Instrument anlegen' name='erstellen'>
		</form>

				
<!-- -->
		
		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>
	
</html>
