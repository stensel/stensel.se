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

$file="kategori.php";
include "include/temp.php";

function init()
{
	if (isset($_POST['submit_add']))
	{
		$stmt = DB::getInstance()->prepare("INSERT INTO Kategori (Beskrivning) VALUES (:Beskrivning)");
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'], PDO::PARAM_STR, 50);
		$stmt->execute();
	}
	
	if (isset($_POST['delete-yes']))
	{
		$stmt = DB::getInstance()->prepare("DELETE FROM Kategori WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['delete-yes'], PDO::PARAM_INT);
		$stmt->execute();
	}
	
	if (isset($_POST['submit_change']))
	{
		$stmt = DB::getInstance()->prepare("UPDATE Kategori SET Beskrivning=:Beskrivning WHERE Id=:id");
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':id', $_POST['submit_change'], PDO::PARAM_INT);
		$stmt->execute();
	}
}
	
function page()
{
?>
			<form method="post" class="form-horizontal">
<?php
	if (!(intval($_SESSION['userpriv']) & 0x20))
	{
		noaccess();
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
		listkategori();
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
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning:</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning" required autofocus>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_add"><span class="glyphicon glyphicon-save"></span> Spara
						</div>
					</div>
				</div>
<?php
}

function listkategori()
{
?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th><span class="glyphicon glyphicon-user"></span> Beskrivning</th>
							<th><button class="btn btn-success" type="submit" name="add"><span class="glyphicon glyphicon-plus"></span> Ny</button></th>
						</tr>
					</thead>
					<tbody>
<?php
	/*** loop over the results ***/
    $result = DB::getInstance()->query("SELECT * FROM Kategori ORDER BY Beskrivning ASC");

    foreach($result as $row)
	{
?>
						<tr>
							<td><?php echo $row['Beskrivning']?></td>
							<td>
								<button class="btn btn-warning" type="submit" name="change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-edit"></span> &Auml;ndra</button>
								<button class="btn btn-danger" type="submit" name="delete" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-remove"></span> Radera</button>
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
		$stmt = DB::getInstance()->prepare("SELECT * FROM Kategori WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
?>
				<div class="well">
					<p>&Auml;r du s&auml;ker p&aring; att du vill radera: <?php echo $row['Beskrivning']?></p>
					<button class="btn btn-danger" type="submit" name="delete-no" value="no"><span class="glyphicon glyphicon-remove"></span> Nej</button>
					<button class="btn btn-success" type="submit" name="delete-yes" value="<?php echo $id?>"><span class="glyphicon glyphicon-ok"></span> Ja</button>
				</div>

<?php
}

function change($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM Kategori WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
?>
				<div class="well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning:</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning" required autofocus value="<?php echo $row['Beskrivning']?>">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-save"></span> Spara
						</div>
					</div>
				</div>
<?php
}

function noaccess()
{
?>
				<br>
				<div class="alert alert-danger">
					No Access !!!
				</div>
<?php
}

?>