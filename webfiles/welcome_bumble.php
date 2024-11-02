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

function state_to_color($state) {

	switch($state) {
	case "0":
		return "text-info";
		break;
	case "1":
		return "text-success";
		break;
	case "2":
		return "text-warning";
		break;
	case "3";
		return "text-danger";
		break;
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
                    <li class="breadcrumb-item active">Startseite</li>
                </ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->
			
			<p class="userinfobox"> <!-- wird verwendet um User Informationen auszugeben -->
			Hallo <span style='color:green'><?php echo $username ?></span>. Hier sind einige Informationen für Sie: 
			<?php // Infos anzeigen
				if ($user_arr['isAdmin']=='1') {
					if ($_SESSION['adminmode']== '1'){
						echo "<div class='text-danger'>Sie sind als Superadmin angemeldet! <br><br>
						Als Superadmin haben sie für jede Buchung die Möglichkeit der Löschung. <br>
						Dieser Modus sollte sehr vorsichtig benutzt werden, da Sie Buchungen löschen können, die sich nicht getätigt haben!<br>
						Falls Sie nicht vor haben fremde Buchung zu löschen, sollten Sie diesen Modus umgehend verlassen!</div>";
						echo "
						<form action='#' method='post'>
						<input type='submit' class='btn btn-info' name='ADMINMODE' value='Superadmin Modus verlassen'>
						</form>";
					} else {
						echo "<div class='text-info'>Sie sind als Administrator angemeldet! <br><br>
						Als Superadmin können Sie auch Buchungen löschen, die nicht von ihnen getätigt wurden<br> 
						Bitte nur verwenden, wenn Sie fremde Buchungen löschen wollen</div>";
						echo "
						<form action='#' method='post'>
						<input type='submit' class='btn btn-info' name='ADMINMODE' value='Superadmin Modus starten'>
						</form>";
					}
				}
				if ($_GET['errormsg']=='nopermission'){ 
					echo "<div class='text-danger'>Sie haben eine Seite aufgerufen, die für die sie keine Rechte hatten</div>";
				}
				if ($_GET['errormsg']=='dataerror'){ 
					echo "<div class='text-danger'>irgendwas ist schief gegangen</div>";
				}
				if ($_GET['booked']=='success') {
					echo "<br><div class='text-success'>erfolgreich gebucht</div>";
				}
				if ($_GET['errormsg']=='deleteerror') {
					echo "<div class='text-danger'>beim löschen ist was schief gegangen</div>";
				}
				?>
			</p>
			<hr>

			<?php // alle instrumente anzeigen für die der user freigeschaltet ist ?>	
			<h2 class='page-header'><b>freigeschaltete Instrumente</b></h2>
			<table class="table table-bordered table-striped">
            <thead>
			<tr>
				<th>Instrument</th>
				<th>Status</th>
				<th>Betreuer</th>
			</tr>
            </thead>
			
			<?php
			// get instruments
			$sql = "SELECT * 
			FROM new_instruments i 
			INNER JOIN new_permissions p ON i.id = p.instrumentid
			WHERE p.userid='".$user_arr['id']."'
			ORDER BY i.name;";
			$result_instruments = mysqli_query($db, $sql);
			while ($instruments_arr = mysqli_fetch_assoc($result_instruments)){
			// TODO liste bauen
			echo "
			<tr>
				<td style='width:50%'>
				<form action='instruments.php' method='get'>
					<input type='hidden' name='instname' value='".$instruments_arr['name']."'>
					<input type='submit' class='btn  btn-info btn-block' value='".$instruments_arr['name']."'>
				</form>
				</td>
				<td style='width=25%'>
					<div style='font-weight:bold' class=".state_to_color($instruments_arr['state']).">".$instruments_arr['status']."</div>
				</td>
				<td style='width:25%'>
					<span>";
					$sql = "SELECT u.name
					FROM new_users u
					INNER JOIN new_supervisors s ON u.id=s.userid
					WHERE s.instrumentid='".$instruments_arr['id']."';";
					$result_supervisors = mysqli_query($db, $sql);
					while ($supervisors_arr = mysqli_fetch_assoc($result_supervisors)) {
						echo "<div>".$supervisors_arr['name']."</div>";
					}
			echo	"</span>
				</td>
			</tr>";
			
			}
			?>

			</table>


			<?php //Alle instrumente anzeigen ?>
			<h2 class='page-header'><b>verfügbare Instrumente</b></h2>
			<table class="table table-bordered table-striped">
            <thead>
			<tr>
				<th>Instrument</th>
				<th>Status</th>
				<th>Betreuer</th>
			</tr>
            </thead>
			
			<?php 
			// get instruments
			/*$sql = "SELECT * 
			FROM new_instruments
			ORDER BY name;";*/
			$sql = "SELECT i.* from new_instruments i JOIN (SELECT i.id FROM new_instruments i
				EXCEPT 
				SELECT p.instrumentid FROM new_permissions p WHERE p.userid = '".$user_arr['id']."') x ON i.id = x.id
				ORDER BY i.name;";
			$result_instruments = mysqli_query($db, $sql);
			echo mysqli_error($db);
			while ($instruments_arr = mysqli_fetch_assoc($result_instruments)){
			// TODO liste bauen
			echo "
			<tr>
				<td style='width:50%'>
				<form action='instruments.php' method='get'>
					<input type='hidden' name='instname' value='".$instruments_arr['name']."'>
					<input type='submit' class='btn  btn-info btn-block' value='".$instruments_arr['name']."'>
				</form>
				</td>
				<td style='width:25%'>
					<div style='font-weight:bold' class=".state_to_color($instruments_arr['state']).">".$instruments_arr['status']."</div>
				</td>
				<td style='width:25%'>
					<span>";
					$sql = "SELECT u.name
					FROM new_users u
					INNER JOIN new_supervisors s ON u.id=s.userid
					WHERE s.instrumentid='".$instruments_arr['id']."';";
					$result_supervisors = mysqli_query($db, $sql);
					echo "<form>";
					while ($supervisors_arr = mysqli_fetch_assoc($result_supervisors)) {
						echo "<div>".$supervisors_arr['name']." </div>";
					}
					echo "</form>";
			echo	"</span>
				</td>
			</tr>";
			
			}
			?>

			</table>
			

			
			<!-- TODO: links zu funktionsseiten aufbauen -->
		</div>
		<div class='col-md-2'>
		<h2> Einstellungen </h2>
		<table class='table table-bordered'>
			<tr>
				<td><center><a href='userpage.php'>Benutzerkonto</a></center></td>
			</tr>
			<tr>
				<td><center><a href='bookings_overview.php'>Buchungsübersicht</a></center></td>
			</tr>

			<?php 
			if ($user_arr['isAdmin'] == '1') {
				echo "
				<tr>
					<td><center><a href='adminpage.php'>Adminseite</center></td>
				</tr>";
			}
			?>
			<tr>
				<td><center><a href='about.php'>Über Bumblebee</a></center></td>
			</tr>
			<tr>
				<td><center><a target="_blank" href='pdf/Telefonliste.pdf'>Telefonliste</a></center></td>
			</tr>
		</table>
		
<!-- -->
		</div>
		</div>
		</div>
	</center></body>
</html>
