
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

if ($_POST['bStatus'] != '') {
	$text = htmlspecialchars($_POST['statustext']);
	$stateselect = htmlspecialchars($_POST['stateselect']);
    $sql = "UPDATE new_instruments 
			SET status = '".$text."',
				state = '".$stateselect."'
            WHERE id ='".$_POST['hID']."';";
    mysqli_query($db, $sql);
}

function get_selected_state($dbvar, $number) {
	if ($dbvar == $number) {
		return "selected";
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
                    <li class="breadcrumb-item"><a href="adminpage.php">Adminseite</a></li>
                    <li class="breadcrumb-item active">Instrumentenverwaltung</li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
            
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
<!-- -->
		
		<h1 class='page-header'>Instrumentenverwaltung</h1>
		
		<form action='newInstrument.php' method='post'>
			<input type='submit' class='btn btn-block btn-info' value='Neues Instrument anlegen'>
		</form>

		<table class="table table-bordered table-striped">
        <thead>
		<tr>
			<th> id </th>
            <th> Name </th>
            <th> Staus </th>
			<th> Betreuer (hinzufügen mit NDS-Kennung)<!-- TODO: implement --> </th>
			<th>  </th>
		</tr>
        </thead>
		
		<?php
		// get instruments
		$sql = "SELECT * 
			FROM new_instruments
			ORDER BY name;";
		$result_instruments = mysqli_query($db, $sql);
		while ($instruments_arr = mysqli_fetch_assoc($result_instruments)){
			// TODO liste bauen
			echo "
			<tr>
                <td>
                    <p>".$instruments_arr['id']."</p>
                </td>
				<td>
					<p>".$instruments_arr['name']."</p>
				</td>
                <td>
                    <form action='manageinstruments.php ' method='post'>
                    <table class='table table-bordered '>
                        <tr>
                        <td>
                            <textarea name='statustext'>".$instruments_arr['status']."</textarea>
                        </td>
						<td>
							<select name='stateselect' class='form-control' style='font-weight:bold'>
								<option ".get_selected_state($instruments_arr['state'], 0)." value='0' class='text-default' style='font-weight:bold'>Default</option>
								<option ".get_selected_state($instruments_arr['state'], 1)." value='1' class='text-success' style='font-weight:bold'>OK</option>
								<option ".get_selected_state($instruments_arr['state'], 2)." value='2' class='text-warning' style='font-weight:bold'>Danger</option>
								<option ".get_selected_state($instruments_arr['state'], 3)." value='3' class='text-danger' style='font-weight:bold'>Error</option>
							</select> 

                            <input type='hidden' name='hID' value='".$instruments_arr['id']."'>
                            <input type='submit' name='bStatus' value='Status ändern' class='btn btn-default'>
                        </td>
                        </tr>
                    </table>
                    </form>
                </td>
				<td>
					<span>";
					$sql = "SELECT 
                    u.name as name,
                    u.id as uid,
                    s.instrumentid as instid
					FROM new_users u
					INNER JOIN new_supervisors s ON u.id=s.userid
					WHERE s.instrumentid='".$instruments_arr['id']."';";
					$result_supervisors = mysqli_query($db, $sql);
                    echo"<table class='table table-bordered '>";
					while ($supervisors_arr = mysqli_fetch_assoc($result_supervisors)) {
						
                        echo"<tr><td><form action='deletesupervisor.php' method='post'>";
                        echo $supervisors_arr['name'];
                        echo " </td><td> 
                            <input type='hidden' name='hinstid' value='".$instruments_arr['id']."'> 
                            <input type='hidden' name='huid' value='".$supervisors_arr['uid']."'> 
                            <input type='hidden' name='from' value='manage'>
                            <input type='submit' class='btn btn-danger'  name='bdeletesupervisor' value='Betreuer entfernen'>
                        </form>";
                        echo "</td></tr>";
					}
                    echo "
                    <tr>
                    <form action='newSupervisor.php ' method='post'>
                        <td><select name='selectNDS' class='form-control'>
                        <option selected value=''></option>
                        ";
                        $sql = 'SELECT nds, name FROM new_users ORDER BY nds';
                        $res = mysqli_query($db, $sql);
                        while ($allusersnds_arr = mysqli_fetch_assoc($res)){
                            echo "<option value='".$allusersnds_arr['nds']."'>" . $allusersnds_arr['name'] . "</option>";
                        }
                        echo "
                            </select></td>
                            <input type='hidden' name='hinstid' value='".$instruments_arr['id']."'>
                        <td>
                        <input type='submit' class='btn btn-default'name='baddsupervisor' value='Betreuer hinzufügen'></td>
                    </form>
                    </tr>
                    ";
                    echo"</table>";
			echo "</span>
				</td>
				<td>
					<form action='deleteinstrument.php' method='post'>
						<input type='hidden' name='Pinstid' value='".$instruments_arr['id']."'> 
                        <input type='hidden' name='from' value='manage'>
						<center><input type='submit' class='btn btn-danger'  name='deletebutton' value='delete'></center>
					</form>
				</td>
			</tr>";
			
		}
		?>
		</table>
		<!-- -->
		
		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center> </body>
	
</html>
