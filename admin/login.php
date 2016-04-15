<?php 
/*
	Copyright (c) 2015-2016 "Morten Svendsen"

	This file is part of Business Management.

    Business Management is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Business Management is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Business Management.  If not, see <http://www.gnu.org/licenses/>.
*/

$file="login.php";
include "include/temp.php";

function init()
{
	if (isset($_POST['login']))
	{	
		$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE UserName=:user AND UserPassword=:pass");
		$stmt->bindParam(':user', $_POST['user'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':pass', md5($_POST['passwd']), PDO::PARAM_STR, 50);
		$stmt->execute();
		
		// Hittades inte användarnamn och lösenord
		// skicka till formulär med felmeddelande 
		if ($stmt->rowCount() == 0)
		{
			header("Location: login.php?badlogin=");
			exit;
		}
		
		$data = $stmt->fetch();
		
		// Sätt sessionen med unikt index 
		$_SESSION['sess_id'] = $data['Id'];
		$_SESSION['sess_user'] = $_POST['user'];
		$_SESSION['sess_fullname'] = $data['FullName'];
		if ($_POST['passwd'] == "el")
		{
			$_SESSION['userpriv'] = 0;
			header("Location: user.php?first=1");
		}
		else
		{
			$_SESSION['userpriv'] = $data['UserPriv'];
			header("Location: index.php");
		}
		
		/*
		 * UserPriv Bitfield       7 6 5 4 3 2 1 0  Value
		 *          User                         x   0x01
		 *          Test                       x     0x02
		 *    Inte Ändra Andras              x       0x04
		                                   x         0x08
		 *			Lager                x			 0x10
		 * Ändra ENummer Reg           x			 0x20
		                             x               0x40
		 *          Admin          x                 0x80
		 */			
		exit;
	}
}

function page()
{
	if (isset($_GET['badlogin']))
	{ ?>
			<div class="alert alert-danger">
				Fel anv&auml;ndarnamn eller l&ouml;senord!<br>
				F&ouml;rs&ouml;k igen!
			</div>
<?php
	}
?>
			<form class="form-signin" method="post">
				<h2 class="form-signin-heading">Logga in</h2>
				<br>
				<div class="row">
					<div class="col-sm-1 text-right">
						<label for="inputUser" class="hidden-sm">Anv&auml;ndarnamn: </label>
					</div>
					<div class="col-sm-4">
						<input type="text" id="inputUser" name="user" class="form-control" placeholder="Anv&auml;ndarnamn" required autofocus>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-1 text-right">
						<label for="inputPassword" class="hidden-sm">Password: </label>
					</div>
					<div class="col-sm-4">
						<input type="password" id="inputPassword" name="passwd" class="form-control" placeholder="L&ouml;senord" required>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-1"></div>
					<div class="col-sm-2">
						<button class="btn btn-lg btn-primary btn-block" type="submit" id="login" name="login"><span class="glyphicon glyphicon-log-in"></span> Logga in</button>
					</div>
				</div>
			</form>
<?php
}
?>