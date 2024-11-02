<?php
session_start();
//logout:
unset($_SESSION['userid']);
session_unset();
require "DB/connect.inc.php";
//password, user und login in variablen ablegen:

foreach($_POST as $name => $value) {
    $$name=htmlspecialchars($value);
}
if (isset($login)) {
    if($login){//zum vergleich hashen
        $hash=sha1($pwd);
        //überprüfung ob die daten gültig sind:
        $sql="SELECT id, suspended FROM new_users WHERE passwd='".$hash."' AND nds='".$user."';";
        $result=mysqli_query($db,$sql);
        if($row=mysqli_fetch_assoc($result)){//login erfolgreich
            if ($row['suspended'] == 1) {
                $message="Dieser User ist deaktiviert. Bitte wenden Sie sich an einen Administrator";
            } else {
                $_SESSION['userid']=$row['id'];
                header("Location: welcome_bumble.php");
            }
        }else{//login falsch
            $message="Benutzername oder Passwort falsch.";
        }
    }
}


?>

<html>
	<head>
		<title>Login - BumBee</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="style.css"><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	<body><center>
	
		<div class="containter">
		<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">

<!-- INOFBOX -->

<h3> Arbeitsplan Arbeitsgruppen </h3>
<table class="table table-bordered table-stripped">
<tr>
<td><div class="text-danger"><b>gerade KW</b></div></td>
<td><b>Montag</b></td>
<td><b>Dienstag</b></td>
<td><b>Mittwoch</b></td>
<td><b>Donnerstag</b></td>
<td><b>Freitag</b></td>
</tr>
<tr>
<td><b>bis 12:30</b></td>
<td>AG Schreiner</td>
<td>AG Bierl</td>
<td>AG Schreiner</td>
<td>AG Lechner/Kammler</td>
<td>AG Schreiner</td>
</tr>
<tr>
<td><b>ab 13:00</b></td>
<td>AG Lechner/Kammler</td>
<td>AG Schreiner</td>
<td>AG Lechner/Kammler</td>
<td>AG Bierl</td>
<td>AG Lechner/Kammler</td>
</tr>
<tr>
<td colspan=6></td>
<tr>
<tr>
<td><div class="text-danger"><b>ungerade KW</b></div></td>
<td><b>Montag</b></td>
<td><b>Dienstag</b></td>
<td><b>Mittwoch</b></td>
<td><b>Donnerstag</b></td>
<td><b>Freitag</b></td>
</tr>
<tr>
<td><b>bis 12:30</b></td>
<td>AG Lechner/Kammler</td>
<td>AG Bierl</td>
<td>AG Lechner/Kammler</td>
<td>AG Schreiner</td>
<td>AG Lechner/Kammler</td>
</tr>
<tr>
<td><b>ab 13:00</b></td>
<td>AG Schreiner</td>
<td>AG Lechner/Kammler</td>
<td>AG Schreiner</td>
<td>AG Bierl</td>
<td>AG Schreiner</td>
</tr>
</table>

<div class="text-info" style="font-size:1.5em">Arbeiten im Reinraum nur nach vorheriger Abklärung der Anwesenheit einer zweiten Person</div>

<!-- INOFBLOCK  -->
		<h1>Login</h1>
		<form action=<?php echo $_SERVER['PHP_SELF'];?> method='post'>
            <?php if (isset($message)) {echo $message;}?>
		<table>
		<tr>
			<td align="right">NDS-Kennung:</td>
			<td><input type="text" name="user" value="<?php if (isset($user)) {echo $user;}?>"></td>
		</tr>
		<tr>
			<td align="right">Passwort:</td>
			<td><input type="password" name="pwd"></td>
		</tr>
		<tr><td></td>
			<td ><input type="submit" value="login" name="login"></td>
		</tr>
		</table>
		</form>
		
		</div>
		<div class="col-md-2"></div>
		</div>
		</div>
	</center></body>
</html>
