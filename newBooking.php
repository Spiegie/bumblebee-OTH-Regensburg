
<!-- Hier weitermachen -->
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
$instrumentname = $_GET['instname'];
$sql = "SELECT id FROM new_instruments WHERE name='".$instrumentname."';";
if (!$res_instname_id = mysqli_query($db, $sql)) {
	$errormsg = "Instrument wurde nicht gefunden";
	echo "<p>".$errormsg."</p>";
}
$res_instrumentid= mysqli_fetch_assoc($res_instname_id);
$instrumentid = $res_instrumentid['id'];
// Abfrage ob user zugriffsrechte auf die Seite hat
$sql = "SELECT p.userid userid FROM new_permissions p INNER JOIN new_instruments i ON p.instrumentid=i.id WHERE i.name= '".$instrumentname."'";
if (!$res_permission_arr = mysqli_query($db, $sql)) {
	echo "<p> error".$errormsg."</p>";
}
$isPermitted = 0 ;
while($res_permission = mysqli_fetch_assoc($res_permission_arr)) {
	if ($res_permission['userid'] == $user_arr['id']) {
		$isPermitted = True;
		break ;
	}
}
if ($isPermitted or $user_arr['isAdmin'] == '1') {
	
} else {
	echo "Sie haben keine Berechtigung dieses instrument zu Buchen";
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
                    <li class="breadcrumb-item"><a href="instruments.php?instname=<?php echo $_GET['instname'];?>">Instrumentübersicht <?php echo $_GET['instname'];?></a></li>
                    <li class="breadcrumb-item active">Buchung für <?php echo $_GET['instname'];?></li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->
		<?php echo "<h1 class='page-header'> Buchung für ".$instrumentname." </h1>"; ?>
		<?php 
		
			if ($_GET['errormsg'] == 'inputerror') {
				echo "
				<hr>
				<div class='text-danger'>Fehler bei der Eingabe</div>
				";
			}
			
			if ($_GET['errormsg'] == 'dataerror') {
				echo "
				<hr>
				<div class='text-danger'>Sie haben einen fehler bei der Eingabe gemacht</div>
				";
			}
			
			if ($_GET['errormsg'] == 'isalreadybooked') {
				echo "
				<hr>
				<div class='text-danger'>Instrument ist um diese Zeit bereits gebucht</div>
				";
			}
			if ($_GET['booked'] != '') {
				echo "<div class='text-success'>Buchung erfolgreich</div>";
			}
		
			mysqli_data_seek($res_permission_arr,0); 
		?>
		<form action='processnew_Booking.php?instname=<?php echo $_GET['instname'];?>' method='post'>
			<table class="table table-bordered table-striped">
			<tr>
				<td> <span> Datum: </span> </td>
				<td> <input type='date' name='date' class="form-control"> </td>
			</tr>
			<tr>
				<td> <span> Start (Zeit): </span> </td>
				<td> <input type='time' name='startTime' class="form-control"> </td>
			</tr>
			<tr>
				<td> <span> Ende (Zeit): </span> </td>
				<td> <input type='time' name='endTime' class="form-control"> </td>
			</tr>
			<tr>
				<td> <span> Kommentar : </span> </td>
				<td> <input type='text'  name='comment' class="form-control" > </td>
			</tr>
			<input type='hidden' name='instrumentname' value='<?php echo $instrumentname;?>'>
			<input type='hidden' name='instrumentid' value='<?php echo $instrumentid;?>'>
			<input type='hidden' name='bookedby' value=''>
			
			</table>
			<input type='submit' class='btn btn-block btn-success' value='OK' name='button'>
		</form>
		<?php
			// read $_GET and get stuff
			$instname = htmlspecialchars($_GET['instname']);
			$sql = "SELECT id FROM new_instruments WHERE name='".$instname."';";
			if (!$res_instname_id = mysqli_query($db, $sql)) {
				$errormsg = "Instrument wurde nicht gefunden";
				echo "<p>".$errormsg."</p>";
			}
			$res_instrumentid= mysqli_fetch_assoc($res_instname_id);
			$instrumentid = $res_instrumentid['id'];
			
			//print errormsg to user
			
			
			// get instrument id with given name
			if ($_POST['bfiltern'] == '') {
				$sql = "SELECT 
			    b.date as date,
			    b.startTime as startTime,
			    b.endTime as endTime,
			    u.name as uname,
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE i.name='".$instname."' AND b.date >= CURDATE() 
				ORDER BY b.date ASC, b.startTime ASC;";
				if (!$res_booking_arr = mysqli_query($db, $sql)) {
					$errormsg = "Instrument wurde nicht gefunden";
					echo "<p>".$errormsg."</p>";
				}
			} 
			if ($_POST['bfiltern'] != '' and $_POST['dvon'] == '' and $_POST['dbis'] == '') {
				$sql = "SELECT 
			    b.date as date,
			    b.startTime as startTime,
			    b.endTime as endTime,
			    u.name as uname,
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE i.name='".$instname."' AND b.date >= CURDATE() 
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
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE i.name='".$instname."' 
				AND b.date >= CURDATE() 
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
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE i.name='".$instname."' AND b.date >= '".$_POST['dvon']."' 
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
			    b.comments as comments,
			    b.id as id,
			    b.bookedbyid as uid
				FROM new_bookings b
				INNER JOIN new_instruments i ON b.instruments = i.id 
				INNER JOIN new_users u ON b.bookedbyid = u.id
				WHERE i.name='".$instname."' 
				AND b.date >= '".$_POST['dvon']."'
				AND b.date <= '".$_POST['dbis']."'
				ORDER BY b.date ASC, b.startTime ASC;";
				if (!$res_booking_arr = mysqli_query($db, $sql)) {
					$errormsg = "Instrument wurde nicht gefunden";
					echo "<p>".$errormsg."</p>";
				}
			}
			
			
?>

			<div class="col-md-4">
			<table class="table">
			<tr><td>Zeitraum filtern</td></tr>
			<tr>
			<?php echo "<form action='newBooking.php?instname=".$instname."' method='post'>"; ?>
			<td>von: <input class='form-control' type='date' name='dvon' value='<?php echo $_POST['dvon']; ?>'></td>
			<td>bis: <input class='form-control' type='date' name='dbis' value='<?php echo $_POST['dbis']; ?>'></td>
			<td> <input class='btn btn-default' type='submit' value='filtern' name='bfiltern'> </td>
			</form>
			</tr>
			</table>
			</div>

			<table class="table table-bordered table-striped">
			<thead>
            <tr>
				<th>Gebucht am (YYY-MM-TT)</span></th>
				<th>Gebucht von</span></th>
				<th>Gebucht bis</span></th>
				<th>Benutzer</span></th>
				<th>Kommentar</span></th>
				<th></th>
			</tr>
            </thead>
				<?php
				while ($booking_arr = mysqli_fetch_assoc($res_booking_arr)) {
					echo "<tr>";
					echo "<td>";
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
					echo "<td>";
					
					
					if ($_SESSION['adminmode'] == '1'){
						echo "
						<form action='deletebooking.php?instname=".$instname."' method='post'>
							<input type='hidden' name='bookingid' value='".$booking_arr['id']."'> 
							<center><input type='submit' class='btn btn-danger'  name='deletebutton' value='superdelete'></center>
						</form>";
					} else {
						if ($booking_arr['uid'] == $user_arr['id']) {
							echo "
							<form action='deletebooking.php?instname=".$instname."' method='post'>
								<input type='hidden' name='bookingid' value='".$booking_arr['id']."'> 
								<input type='hidden' name='bookindate' value='".$booking_arr['date']."'> 
								<input type='hidden' name='bookingstartTime' value='".$booking_arr['startTime']."'> 
								<input type='hidden' name='bookingendTime' value='".$booking_arr['endTime']."'> 
								<center><input type='submit' class='btn btn-danger'  name='deletebutton' ' value='delete'></center>
							</form>";
						}
					}
					
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
