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


function int_to_tag ($day) {
	switch($day) {
	case "0":
		return "So";
		break;
	case "1":
		return "Mo";
		break;
	case "2":
		return "Di";
		break;
	case "3":
		return "Mi";
		break;
	case "4":
		return "Do";
		break;
	case "5":
		return "Fr";
		break;
	case "6":
		return "Sa";
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
			<div class="pull-left" style="padding:5px">Sie sind eingeloggt als <span style='color:green'><?php echo $username ?></span> </br>
				<ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="welcome_bumble.php">Startseite</a></li>
                    <li class="breadcrumb-item">Buchungsübersicht</li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
        
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
        
        <!--make headline-->
			<h1 class='page-header'> Buchungsübersicht </h1>
        
	<div class="col-md-6">
		<h4>Zeitraum filtern:</h4>
		<table class="table">
		<tr>
		<?php echo "<form action='bookings_overview.php' method='post'>"; ?>
		<td>von: <input class='form-control' type='date' name='dvon' value='<?php echo $_POST['dvon']; ?>'></td>
		<td>bis: <input class='form-control' type='date' name='dbis' value='<?php echo $_POST['dbis']; ?>'></td>
		<td> <input class='btn btn-default' type='submit' value='filtern' name='bfiltern'> </td>
		</form>
		<td>
			<form action='userbookings.php' method='post'>
				<input class="btn btn-info" type="submit" value='nur eigene Buchungen anzeigen'  name='beigene Buchungen'>
			</form>
		</td>
		</tr>
		</table>
	</div>	

<!-- -->
            
            
			
            <?php
			//print errormsg to user
            
            if ($_POST['bfiltern'] == '') {
				$sql = "SELECT 
			    b.date as date,
			    b.startTime as startTime,
			    b.endTime as endTime,
			    u.name as uname,
                i.name as iname,
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE b.date >= CURDATE()
				ORDER BY b.date ASC, b.startTime ASC;";
				if (!$res_booking_arr = mysqli_query($db, $sql)) {
					$errormsg = "Instrument wurde nicht gefunden!";
					echo "<p>".$errormsg."</p>";
				}
			} 
			if ($_POST['bfiltern'] != '' and $_POST['dvon'] == '' and $_POST['dbis'] == '') {
				$sql = "SELECT 
			    b.date as date,
			    b.startTime as startTime,
			    b.endTime as endTime,
			    u.name as uname,
                i.name as iname,
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE b.date >= CURDATE()
				ORDER BY b.date ASC, b.startTime ASC;";
				if (!$res_booking_arr = mysqli_query($db, $sql)) {
					$errormsg = "Instrument wurde nicht gefunden";
					echo "<p>".$errormsg."</p>";
				}
			} 

			if ($_POST['bfiltern'] != '' and $_POST['dvon'] == '' and $_POST['dbis'] != '') {
				$sql = "SELECT 
			    b.date as date,
			    b.startTime as startTime,
			    b.endTime as endTime,
			    u.name as uname,
                i.name as iname,
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE b.date >= CURDATE() 
				AND b.date <= '".$_POST['dbis']."'
				ORDER BY b.date ASC, b.startTime ASC;";
				if (!$res_booking_arr = mysqli_query($db, $sql)) {
					$errormsg = "Instrument wurde nicht gefunden";
					echo "<p>".$errormsg."</p>";
				}
			} 

			if ($_POST['bfiltern'] != '' and $_POST['dvon'] != ''  and $_POST['dbis'] == '' ) {
				$sql = "SELECT 
			    b.date as date,
			    b.startTime as startTime,
			    b.endTime as endTime,
			    u.name as uname,
                i.name as iname,
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE b.date >= '".$_POST['dvon']."' 
				ORDER BY b.date ASC, b.startTime ASC;";
				if (!$res_booking_arr = mysqli_query($db, $sql)) {
					$errormsg = "Instrument wurde nicht gefunden";
					echo "<p>".$errormsg."</p>";
				}
			}
			if ($_POST['bfiltern'] != '' and $_POST['dvon'] != ''  and $_POST['dbis'] != '' ) {
				$sql = "SELECT 
			    b.date as date,
			    b.startTime as startTime,
			    b.endTime as endTime,
			    u.name as uname,
                i.name as iname,
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE b.date >= '".$_POST['dvon']."'
				AND b.date <= '".$_POST['dbis']."'
				ORDER BY b.date ASC, b.startTime ASC;";
				if (!$res_booking_arr = mysqli_query($db, $sql)) {
					$errormsg = "Instrument wurde nicht gefunden";
					echo "<p>".$errormsg."</p>";
				}
			}
			
			
			
			
			?>
			
			<table class="table table-bordered table-striped">
			<tr>
				<td><span style='font-weight:bold'>Instrument</span></td>
				<td><span style='font-weight:bold'>Gebucht am</span></td>
				<td><span style='font-weight:bold'>Gebucht von</span></td>
				<td><span style='font-weight:bold'>Gebucht bis</span></td>
				<td><span style='font-weight:bold'>Benutzer</span></td>
				<td><span style='font-weight:bold'>Kommentar</span></td>
			</tr>
				<?php
				while ($booking_arr = mysqli_fetch_array($res_booking_arr)) {
					echo "<tr>";
					echo "<td>";
					echo $booking_arr['iname'];
					echo "</td>";
					echo "<td>";
					$day = date("w",strtotime($booking_arr['date']));
					echo "<span style='font-weight:bold'>" . int_to_tag($day) . " </span>";
					echo $booking_arr['date'];
					echo "</td>";
					echo "<td>";
					echo $booking_arr['startTime'];
					echo "</td>";
					echo "<td>";
					echo $booking_arr['endTime'];
					echo "</td>";
					echo "<td>";
					echo $booking_arr['uname'];
					echo "</td>";
					echo "<td>";
					echo $booking_arr['comments'];
					echo "</td>";
					echo "</tr>";
					
				}
				
				?>
			</table>

		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>
	
</html>
