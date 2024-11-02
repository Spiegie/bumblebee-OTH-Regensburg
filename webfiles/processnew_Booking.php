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

if(!empty($_POST)) {
	if (!array_key_exists('instrumentid', $_POST)) {
		header("Location: welcome_bumble.php");
	}
	
	
    foreach($_POST as $name => $value) {
		$$name=htmlspecialchars($value);
	}
	$startTime = $startTime.":00";
	$endTime = $endTime.":00";
	if ($date == "") {
		header("Location: newBooking.php?instname=".$instrumentname."&errormsg=inputerror");
		return;
	}
	/*
	$DstartTime = new DateTime($date." ".$startTime);
	$DendTime = new DateTime($date." ".$endTime);
	$diff = $DstartTime->diff($DendTime);
	$minutes = ($diff->y * 24 * 60 * 365) +
			($diff->days * 24 * 60) +
			($diff->h * 60) + $diff->i;
	*/
	$testvar = true;
	if ( strtotime($date." ".$startTime) > strtotime($date." ".$endTime)) {
		header("Location: newBooking.php?instname=".$instrumentname."&errormsg=inputerror");
		$testvar = false;
		return;		// echo to user by routing back to newBooking.php?instname=RIE
	} else {
		echo "done";
	}
	
	// Test if booking intersects with other booking
	/*$sql = "SELECT b.startTime, b.endTime, b.bookedbyid 
			FROM new_bookings as b 
			WHERE
			(b.startTime > '".$startTime."' AND b.startTime > '".$endTime."' AND b.date = '".$date."') 
			OR 
			(b.endTime < '".$startTime."' AND b.endTime < '".$endTime."' AND b.date = '".$date."');";
	
	if (!$res_bookings_at_date = mysqli_query($db, $sql)) {
		$errormsg = "fehler in $sql";
		echo "<p>".$errormsg."</p>";
	}
	while ($bookings_at_date = mysqli_fetch_assoc($res_bookings_at_date)) {
		$testvar = false;
		echo "doof";
		break;
	}*/
	
	
	$sql = "SELECT * FROM new_bookings WHERE date = '".$date."' AND instruments='".$instrumentid."';";
	if (!$res_bookings_at_date = mysqli_query($db, $sql)) {
		$errormsg = "fehler in $sql";
		echo "<p>".$errormsg."</p>";
	}
	$testvar = true;
	while ($bookings_at_date = mysqli_fetch_assoc($res_bookings_at_date)) {
		if ( (strtotime($startTime) <= strtotime($bookings_at_date['startTime']) 
			and strtotime($endTime) <= strtotime($bookings_at_date['startTime'])) 
			or (strtotime($startTime) >= strtotime($bookings_at_date['endTime']) 
			and strtotime($endTime) >= strtotime($bookings_at_date['endTime']))) {
			echo "<p> gut <p>";
			echo "<p> ".$startTime." >= ".$bookings_at_date['startTime']." </p>";
			echo "<p> ".$endTime." >= ".$bookings_at_date['startTime']." </p>";
			echo "<p> ".$startTime." <= ".$bookings_at_date['endTime']." </p>";
			echo "<p> ".$endTime." <= ".$bookings_at_date['endTime']." </p>";
		} else {
			echo "nö";
			$testvar = false;
			break;
		}
	}
	
	if ($testvar == true){
		$sql = "INSERT INTO new_bookings (bookwhen, instruments, date, startTime, endTime, bookedbyid, comments) 
			VALUES (NOW(),'".$instrumentid."','".$date."','".$startTime."','".$endTime."', '".$userid."','".$comment."');";
		if (mysqli_query($db, $sql)) {
			echo mysqli_errno($db);
			header("Location: newBooking.php?instname=".$instrumentname."&booked=success");
			return;
		} else {
			echo "ERROR: not able to execute $sql. " . mysqli_error($db);
			header("Location: newBooking.php?instname=".$instrumentname."&errormsg=dataerror");
			return;
		}
	} else {
		header("Location: newBooking.php?instname=".$instrumentname."&errormsg=isalreadybooked");
	}
	
	
	
	
}

?>
