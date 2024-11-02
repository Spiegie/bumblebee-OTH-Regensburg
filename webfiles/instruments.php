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

if ($_POST['bStatus'] != '') {
	$text = htmlspecialchars($_POST['statustext']);
	$stateselect = htmlspecialchars($_POST['stateselect']);
    $sql = "UPDATE new_instruments 
			SET status = '".$text."',
				state = '".$stateselect."'
            WHERE id ='".$_POST['hID']."';";
    mysqli_query($db, $sql);
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
function get_selected_state($dbvar, $number) {
	if ($dbvar == $number) {
		return "selected";
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
			<div class="pull-left" style="padding:5px">Sie sind eingeloggt als <span style='color:green'><?php echo $username ?></span> </br>
				<ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="welcome_bumble.php">Startseite</a></li>
                    <li class="breadcrumb-item active"><?php echo $_GET['instname'];?></li>
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
			// read $_GET and get stuff
			$instname = htmlspecialchars($_GET['instname']);
			$sql = "SELECT id FROM new_instruments WHERE name='".$instname."';";
			if (!$res_instname_id = mysqli_query($db, $sql)) {
				$errormsg = "Instrument wurde nicht gefunden";
				echo "<p>".$errormsg."</p>";
			}
			$res_instrumentid= mysqli_fetch_assoc($res_instname_id);
			$instrumentid = $res_instrumentid['id'];
			
			// make headline
			echo "<h1 class='page-header'> Instrumentübersicht ".$instname." </h1>";
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
			<p class="">
            
			</p>
			<div class="col-md-4">
            <h4>Zeitraum filtern:</h4>
			<table class="table">
			<tr>
			<?php echo "<form action='instruments.php?instname=".$instname."' method='post'>"; ?>
			<td>von: <input class='form-control' type='date' name='dvon' value='<?php echo $_POST['dvon']; ?>'></td>
			<td>bis: <input class='form-control' type='date' name='dbis' value='<?php echo $_POST['dbis']; ?>'></td>
			<td> <input class='btn btn-default' type='submit' value='filtern' name='bfiltern'> </td>
			</form>
			</tr>
			</table>
			</div>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
			<h4>Status: </h4>
            <?php 
            

            ?>
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
			<?php 
			$sql = "SELECT * FROM new_permissions WHERE userid= '".$user_arr['id']."'";
			if (!$res_permission_arr = mysqli_query($db, $sql)) {
				echo "<p> error".$errormsg."</p>";
			}
			$isPermitted = 0 ;
			while($res_permission = mysqli_fetch_assoc($res_permission_arr)) {
				if ($res_permission['instrumentid'] == $instrumentid) {
					$isPermitted = True;
					break ;
				}
			}
			if (!isset($instrumentid)) {
				echo "<div>Instrument wurde nicht gefunden</div>";
			}
			if (isset($instrumentid) and ($isPermitted or $user_arr['isAdmin'] == '1')) {
				echo "
					<form action='newBooking.php' method='get'>
						<input type='hidden' name='instname' value='".$instname."'>
						<input type='submit' class='btn btn-block btn-info' value='Neue Buchung'>
					</form>
				
				";
			} else {
				echo "Sie haben keine Berechtigung dieses instrument zu Buchen";
			}
			?>

		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>
	
</html>
