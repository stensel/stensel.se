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

$file="user.php";
include "include/temp.php";

function init()
{
	global $status;

	if (isset($_POST['submit_add']))
	{
		if ($_POST['UserPassword'] != $_POST['UserPassword2'])
		{
			$status = "Oops!!!<br>Moohahahahahaha du skrev inte samma lösenord 2ggr :D<br><br>Lösenorden matchar inte<br>var god försök igen";
		}
		else
		{
			$stmt = DB::getInstance()->prepare("INSERT INTO users (UserName, UserPassword, UserPriv, FullName, ExtraInfo, Phone, EPost) VALUES (:UserName, :UserPassword, :UserPriv, :FullName, :ExtraInfo, :Phone, :EPost)");
			$stmt->bindParam(':UserName', $_POST['UserName'], PDO::PARAM_STR, 50);
			$pass = md5($_POST['UserPassword']);
			$stmt->bindParam(':UserPassword', $pass, PDO::PARAM_STR, 50);
			$stmt->bindParam(':UserPriv', $_POST['UserPriv'], PDO::PARAM_INT);
			$stmt->bindParam(':FullName', $_POST['FullName'], PDO::PARAM_STR, 30);
			$stmt->bindParam(':ExtraInfo', $_POST['Info'], PDO::PARAM_STR, 50);
			$stmt->bindParam(':Phone', $_POST['Phone'], PDO::PARAM_STR, 15);
			$stmt->bindParam(':EPost', $_POST['EPost'], PDO::PARAM_STR, 40);
			$stmt->execute();
		}
	}
	
	if (isset($_POST['delete-yes']))
	{
		$stmt = DB::getInstance()->prepare("DELETE FROM users WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['delete-yes'], PDO::PARAM_INT);
		$stmt->execute();
	}
	
	if (isset($_POST['submit_change']))
	{
		if ($_POST['UserPassword'] != $_POST['UserPassword2'])
		{
			$status = "Oops!!!<br>Moohahahahahaha du skrev inte samma lösenord 2ggr :D<br><br>Lösenorden matchar inte<br>var god försök igen";
		}
		else if ((intval($_SESSION['userpriv']) & 0x80) == 0x80)
		{
			if ($_POST['UserPassword'] == "")
			{
				$stmt = DB::getInstance()->prepare("UPDATE users SET UserName=:UserName, UserPriv=:UserPriv, FullName=:FullName, ExtraInfo=:ExtraInfo, Phone=:Phone, EPost=:EPost WHERE Id=:id");
			}
			else
			{
				$stmt = DB::getInstance()->prepare("UPDATE users SET UserName=:UserName, UserPassword=:UserPassword, UserPriv=:UserPriv, FullName=:FullName, ExtraInfo=:ExtraInfo, Phone=:Phone, EPost=:EPost WHERE Id=:id");
				$pass = md5($_POST['UserPassword']);
				$stmt->bindParam(':UserPassword', $pass, PDO::PARAM_STR, 50);
			}
			$stmt->bindParam(':UserName', $_POST['UserName'], PDO::PARAM_STR, 50);
			$stmt->bindParam(':UserPriv', $_POST['UserPriv'], PDO::PARAM_INT);
			$stmt->bindParam(':FullName', $_POST['FullName'], PDO::PARAM_STR, 30);
			$stmt->bindParam(':ExtraInfo', $_POST['Info'], PDO::PARAM_STR, 50);
			$stmt->bindParam(':Phone', $_POST['Phone'], PDO::PARAM_STR, 15);
			$stmt->bindParam(':EPost', $_POST['EPost'], PDO::PARAM_STR, 40);
			$stmt->bindParam(':id', $_POST['submit_change'], PDO::PARAM_INT);
			$stmt->execute();
		}
		else
		{
			if ($_POST['UserPassword'] == "")
			{
				$stmt = DB::getInstance()->prepare("UPDATE users SET UserName=:UserName, FullName=:FullName, ExtraInfo=:ExtraInfo, Phone=:Phone, EPost=:EPost WHERE Id=:id");
			}
			else
			{
				$stmt = DB::getInstance()->prepare("UPDATE users SET UserName=:UserName, UserPassword=:UserPassword, FullName=:FullName, ExtraInfo=:ExtraInfo, Phone=:Phone, EPost=:EPost WHERE Id=:id");
				$pass = md5($_POST['UserPassword']);
				$stmt->bindParam(':UserPassword', $pass, PDO::PARAM_STR, 50);
			}

			$stmt->bindParam(':UserName', $_POST['UserName'], PDO::PARAM_STR, 50);
			$stmt->bindParam(':FullName', $_POST['FullName'], PDO::PARAM_STR, 30);
			$stmt->bindParam(':ExtraInfo', $_POST['Info'], PDO::PARAM_STR, 50);
			$stmt->bindParam(':Phone', $_POST['Phone'], PDO::PARAM_STR, 15);
			$stmt->bindParam(':EPost', $_POST['EPost'], PDO::PARAM_STR, 40);
			$stmt->bindParam(':id', $_POST['submit_change'], PDO::PARAM_INT);
			$stmt->execute();
			if ($_SESSION['userpriv'] == 0)
			{
				$_SESSION = array();
				session_destroy();
				header("Location: login.php");
			}
		}
	}
}

function printstatus($s)
{
?>
				<br>
				<div class="alert alert-danger">
					<?php echo $s?>
				</div>
<?php
}

function page()
{
	global $status;
?>
			<form method="post" class="form-horizontal">
<?php

	if (isset($status))
		printstatus($status);

	if (!(intval($_SESSION['userpriv']) & 0x80))
	{
		change($_SESSION['sess_id']);
	}
	else if (isset($_POST['add']))
	{
		addform();
	}
	else if (isset($_POST['delete']))
	{
		askdelete($_POST['delete']);
	}
	else if (isset($_POST['change']))
	{
		change($_POST['change']);
	}
	else
	{
		listusers();
	}
	
?>
			</form>
<?php
}

function addform()
{
?>
				<div class="well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="UserName">Anv&auml;ndarnamn:</label>
						<div class="col-sm-8">
							<input id="UserName" type="text" class="form-control" name="UserName" placeholder="Anv&auml;ndarnamn:" required autofocus>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="FullName">Namn:</label>
						<div class="col-sm-8">
							<input id="FullName" type="text" class="form-control" name="FullName" placeholder="Namn" required>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="EPost">EPost:</label>
						<div class="col-sm-8">
							<input id="EPost" type="text" class="form-control" name="EPost" placeholder="EPost" required>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Phone">Telefon:</label>
						<div class="col-sm-8">
							<input id="Phone" type="text" class="form-control" name="Phone" placeholder="Telefon" required>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Info">Extra info:</label>
						<div class="col-sm-8">
							<input id="Info" type="text" class="form-control" name="Info" placeholder="Extra info">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="UserPriv">Priv:</label>
						<div class="col-sm-8">
							<input id="UserPriv" type="number" class="form-control" name="UserPriv" placeholder="Priv" value="1">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="UserPassword">L&ouml;senord:</label>
						<div class="col-sm-8">
							<input id="UserPassword" type="password" class="form-control" name="UserPassword" placeholder="L&ouml;senord" required>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="UserPassword2">L&ouml;senord:</label>
						<div class="col-sm-8">
							<input id="UserPassword2" type="password" class="form-control" name="UserPassword2" placeholder="L&ouml;senord verifiering" required>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_add"><span class="glyphicon glyphicon-save"></span> Spara</button>
						</div>
					</div>
				</div>
<?php
}

function listusers()
{
?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th><span class="glyphicon glyphicon-user"></span> Anv&auml;ndarnamn</th>
							<th><span class="glyphicon glyphicon-user"></span> Namn</th>
							<th><span class="glyphicon glyphicon-envelope"></span> Email</th>
							<th><?php if ((intval($_SESSION['userpriv']) & 0x80) == 0x80) {?><button class="btn btn-success" type="submit" name="add"><span class="glyphicon glyphicon-plus"></span> Ny</button><?php }?></th>
						</tr>
					</thead>
					<tbody>
<?php
	/*** loop over the results ***/
	$ustmt = DB::getInstance()->prepare("SELECT * FROM users ORDER BY UserName ASC");
	$ustmt->execute();
	$result = $ustmt->fetchAll();
	
    foreach($result as $row)
	{
?>
						<tr>
							<td><?php echo $row['UserName']?></td>
							<td><?php echo $row['FullName']?></td>
							<td><?php echo $row['EPost']?></td>
							<td>
								<button class="btn btn-warning" type="submit" name="change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-edit"></span> &Auml;ndra</button>
								<?php if ((intval($_SESSION['userpriv']) & 0x80) == 0x80) {?><button class="btn btn-danger" type="submit" name="delete" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-remove"></span> Radera</button><?php }?>
							</td>
						</tr>

<?php
	}
?>
					</tbody>
				</table>

<?php
}

function askdelete($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
?>
				<div class="well">
					<p>&Auml;r du s&auml;ker p&aring; att du vill radera: <?php echo $row['UserName']?>, <?php echo $row['FullName']?></p>
					<button class="btn btn-danger" type="submit" name="delete-no" value="no"><span class="glyphicon glyphicon-remove"></span> Nej</button>
					<button class="btn btn-success" type="submit" name="delete-yes" value="<?php echo $id?>"><span class="glyphicon glyphicon-ok"></span> Ja</button>
				</div>

<?php
}

function change($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
?>
				<div class="well">
					<?php if (isset($_GET['first'])) {?><div class="alert alert-danger">
						Skriv in ett nytt lösenord och tryck på spara, logga sedan in på nytt
					</div><?php }?>
					<?php if ((intval($_SESSION['userpriv']) & 0x80) == 0x80) {?><div class="row">
						<code>
					  </code>
					</div><?php }?>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="UserName">Anv&auml;ndarnamn:</label>
						<div class="col-sm-8">
							<input id="UserName" type="text" class="form-control" name="UserName" placeholder="Anv&auml;ndarnamn:" required autofocus value="<?php echo $row['UserName']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="FullName">Namn:</label>
						<div class="col-sm-8">
							<input id="FullName" type="text" class="form-control" name="FullName" placeholder="Namn" required value="<?php echo $row['FullName']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="EPost">EPost:</label>
						<div class="col-sm-8">
							<input id="EPost" type="text" class="form-control" name="EPost" placeholder="EPost" required value="<?php echo $row['EPost']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Phone">Telefon:</label>
						<div class="col-sm-8">
							<input id="Phone" type="text" class="form-control" name="Phone" placeholder="Telefon" value="<?php echo $row['Phone']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Info">Extra info:</label>
						<div class="col-sm-8">
							<input id="Info" type="text" class="form-control" name="Info" placeholder="Extra info" value="<?php echo $row['ExtraInfo']?>">
						</div>
					</div>
					<?php if (($_SESSION['userpriv'] & 0x80) == 0x80) {?><div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="UserPriv">Priv:</label>
						<div class="col-sm-8">
							<input id="UserPriv" type="number" class="form-control" name="UserPriv" placeholder="Priv"  value="<?php echo $row['UserPriv']?>">
						</div>
					</div><?php }?>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="UserPassword">L&ouml;senord:</label>
						<div class="col-sm-8">
							<input id="UserPassword" type="password" class="form-control" name="UserPassword" placeholder="L&ouml;senord">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="UserPassword2">L&ouml;senord:</label>
						<div class="col-sm-8">
							<input id="UserPassword2" type="password" class="form-control" name="UserPassword2" placeholder="L&ouml;senord verifiering">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-save"></span> Spara</button>
						</div>
					</div>
				</div>
<?php
}

?>