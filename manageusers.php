
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
                    <li class="breadcrumb-item active">Benutzerverwaltung</li>
				</ol>
            </div>
			<a href="login.php" class="pull-right" style="padding:10px;background:lightgrey;font-size: large">LOGOUT</a>
		</nav>
		
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
		
<!-- -->

		<h1 class='page-header'>Benutzerverwaltung</h1>
		
		<?php
		$sql = "SELECT * 
		FROM new_users
		ORDER BY nds";
		$res_newusers = mysqli_query($db, $sql);
		
		?>
		<form action='newUser.php' method='post'>
			<input type='submit' class='btn btn-block btn-info' value='Neuen Benutzer anlegen '>
		</form>

		
		<table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>id</span></th>
            <th>name</span></th>
            <th>nds</span></th>
            <th>isAdmin</span></th>
            <th>email</span></th>
			<th>Berechtigungen</th>
            <th></th>
        </tr>
        </thead>
        <?php
            while ($users_arr = mysqli_fetch_assoc($res_newusers)) {
                echo "<tr>";
                echo "<td>";
                echo $users_arr['id'];
                echo "</td>";
                echo "<td>";
                echo $users_arr['name'];
                echo "</td>";
                echo "<td>";
                echo $users_arr['nds'];
                echo "</td>";
                echo "<td>";
                echo $users_arr['isAdmin'];
                echo "</td>";
                echo "<td>";
                echo $users_arr['email'];
                echo "</td>";
				echo "<td>";

				$sql = "SELECT i.name,i.id
					FROM new_instruments i
					INNER JOIN new_permissions p
					ON i.id = p.instrumentid
					WHERE p.userid='".$users_arr['id']."'
					ORDER BY i.name;";
				echo "<form>";
				echo "<select class='form-control'>";
				echo "<option selected>- Berechtigungen -</option>";
				$permitted_arr = mysqli_query($db, $sql);
				while ($perm = mysqli_fetch_assoc($permitted_arr)) {
					echo "<option value='".$perm['name']."'>" .$perm['name']. "</option>";
				}
				echo "</select>";
				echo "</form>";

				
				echo "</td>";
				if ($users_arr['id'] != 1) {
				echo "
					<td>
					<table>
					<tr><td>
						<form action='alteruser.php' method='get'>
							<input type='hidden' name='nds' value='".$users_arr['nds']."'>
							<center><input type='submit' class='btn btn-default' value='bearbeiten'></center>
						</form>
					</td></tr>
					<tr><td>
                        <form action='deleteuser.php' method='post'>
                            <input type='hidden' name='Puserid' value='".$users_arr['id']."'> 
                            <center><input type='submit' class='btn btn-danger'  name='deletebutton' value='delete User'></center>
						</form>
					</td></tr>
					</table>
                    </td>";
				}
               echo "</tr>";

                
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
