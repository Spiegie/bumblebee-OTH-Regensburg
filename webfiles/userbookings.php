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
					<li class="breadcrumb-item"><a href="userpage.php">Benutzerseite</a></li>
                    <li class="breadcrumb-item active">Benutzerbuchungen</li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">


<h1 class='page-header'>Ihre Buchungen</h3>


	<div class="col-md-6">
		<h4>Zeitraum filtern:</h4>
		<table class="table">
		<tr>
		<?php echo "<form action='userbookings.php' method='post'>"; ?>
		<td>von: <input class='form-control' type='date' name='dvon' value='<?php echo $_POST['dvon']; ?>'></td>
		<td>bis: <input class='form-control' type='date' name='dbis' value='<?php echo $_POST['dbis']; ?>'></td>
		<td> <input class='btn btn-default' type='submit' value='filtern' name='bfiltern'> </td>
		</form>
		<td>
			<form action='bookings_overview.php' method='post'>
				<input class="btn btn-info" type="submit" value='alle Buchungen anzeigen'  name='beigene Buchungen'>
			</form>	

		</td>
		</tr>
		</table>
	</div>	





<!-- -->

<?php
			//print errormsg to user, Process Filter
            
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







<?php
$sql = "SELECT * 
	FROM new_instruments i
JOIN new_bookings b ON i.id = b.instruments
WHERE b.bookedbyid='".$user_arr['id']."' AND b.date >= CURDATE()
ORDER BY b.date ASC, b.startTime ASC;";
			if (!$res_mybookings_arr = mysqli_query($db, $sql)) {
				$errormsg = "irgendwas doofes ist passiert";
				echo "<p>".$errormsg."</p>";
			}
?>
<table class="table table-bordered table-striped">
<thead>
<tr>
	<th>Instrument</th>
	<th>Geburcht am (YYYY-MM-TT)</th>
	<th>Gebucht von</th>
	<th>Gebucht bis</th>
	<th>Kommentar</th>
</tr>
</thead>
<tbody>
<?php
			while ($res_mybookings = mysqli_fetch_assoc($res_mybookings_arr)) {
				echo "<tr>";
				echo "<td>";
				echo $res_mybookings['name'];
				echo "</td>";
				echo "<td>";
				$day = date("w",strtotime($booking_arr['date']));
				echo "<span style='font-weight:bold'>" . int_to_tag($day) . " </span>";
				echo $res_mybookings['bookwhen'];
				echo "</td>";
				echo "<td>";
				echo $res_mybookings['startTime'];
				echo "</td>";
				echo "<td>";
				echo $res_mybookings['endTime'];
				echo "</td>";
				echo "<td>";
				echo $res_mybookings['comments'];
				echo "</td>";
				echo "<td>";
				echo "
							<form action='deletebooking.php?instname=".$res_mybookings['name']."' method='post'>
								<input type='hidden' name='bookingid' value='".$res_mybookings['id']."'> 
								<input type='hidden' name='bookindate' value='".$res_mybookings['date']."'> 
								<input type='hidden' name='bookingstartTime' value='".$res_mybookings['startTime']."'> 
								<input type='hidden' name='bookingendTime' value='".$res_mybookings['endTime']."'> 
								<center><input type='submit' class='btn btn-danger'  name='deletebutton' ' value='delete'></center>
							</form>";
				echo "</td>";

				echo "</tr>";

			}

?>

</tbody>
</table>

<?php
?>	




<!-- -->
		</div>
		</div>
		</div>
	</center></body>
</html>
