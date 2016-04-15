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

$file="enummer.php";
include "include/temp.php";

function init()
{
	if (isset($_POST['submit_kat_search']))
	{
		$_SESSION['lastkat'] = $_POST['Kategori'];
	}

	if (isset($_POST['submit_add']))
	{
		$stmt = DB::getInstance()->prepare("INSERT INTO ENummer (ENummer, Beskrivning, Enhet, Kategori) VALUES (:ENummer, :Beskrivning, :Enhet, :Kategori)");
		$stmt->bindParam(':ENummer', $_POST['ENummer'], PDO::PARAM_STR, 10);
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Enhet', $_POST['Enhet'], PDO::PARAM_STR, 5);
		$stmt->bindParam(':Kategori', $_POST['Kategori'], PDO::PARAM_INT);
		$stmt->execute();
	}
	
	if (isset($_POST['delete-yes']))
	{
		$stmt = DB::getInstance()->prepare("DELETE FROM ENummer WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['delete-yes'], PDO::PARAM_INT);
		$stmt->execute();
	}
	
	if (isset($_POST['submit_change']))
	{
		$stmt = DB::getInstance()->prepare("UPDATE ENummer SET ENummer=:ENummer, Beskrivning=:Beskrivning, Enhet=:Enhet, Kategori=:Kategori WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['submit_change'], PDO::PARAM_INT);
		$stmt->bindParam(':ENummer', $_POST['ENummer'], PDO::PARAM_STR, 10);
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Enhet', $_POST['Enhet'], PDO::PARAM_STR, 5);
		$stmt->bindParam(':Kategori', $_POST['Kategori'], PDO::PARAM_INT);
		$stmt->execute();
	}
}

function page()
{
?>
			<form role="form" method="post" class="form-horizontal">
<?php
	if (!($_SESSION['userpriv'] & 0x20))
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
		show_list();
	}
	
?>
			</form>
<?php
}

function addform()
{
?>
				<div class="well">
					<div class="alert alert-info">
						Enummer från Solar börjar med "S".
						Enummer från Malmbergs börjar med "M".
						Enummer från Ahlsell har ingen prefix.
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="ENummer">E-Nummer:</label>
						<div class="col-sm-8">
							<input id="ENummer" type="text" class="form-control" name="ENummer" placeholder="E-Nummer" required autofocus>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning:</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning" required autofocus>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Enhet:</label>
						<div class="col-sm-8">
							<input id="Enhet" type="text" class="form-control" name="Enhet" placeholder="Enhet" required autofocus>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Kategori">Kategori:</label>
						<div class="col-sm-8">
							<select class="form-control" id="Kategori" name="Kategori">

<?php
    $result = DB::getInstance()->query("SELECT * FROM Kategori ORDER BY Beskrivning ASC");

    if (isset($_SESSION['lastkat']))
	{
		$lastkat = $_SESSION['lastkat'];
	}
	else
	{
		$lastkat = -1;
	}

    foreach($result as $row)
	{
?>
								<option <?php if ($lastkat == $row['Id']) echo 'selected="selected" ';?>value="<?php echo $row['Id']?>"><?php echo $row['Beskrivning']?></option>
<?php
	}
?>
							</select>
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

function show_list()
{
?>
				<div class="well cust_well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Kategori">Kategori:</label>
						<div class="col-sm-6 col-xs-10">
							<select class="form-control" id="Kategori" name="Kategori">

<?php
    $result = DB::getInstance()->query("SELECT * FROM Kategori ORDER BY Beskrivning ASC");

    if (isset($_SESSION['lastkat']))
	{
		$lastkat = $_SESSION['lastkat'];
	}
	else
	{
		$lastkat = 33;
	}
	
    
    foreach($result as $row)
	{
?>
								<option <?php if ($lastkat == $row['Id']) echo 'selected="selected" ';?>value="<?php echo $row['Id']?>"><?php echo $row['Beskrivning']?></option>
<?php
	}
?>
							</select>
						</div>
						<div class="col-sm-2">
							<button class="btn btn-success btn-sm hidden-xs" type="submit" name="submit_kat_search" id="Btn_Kat"><span class="glyphicon glyphicon-plus"></span> Välj</button>
						</div>
					</div>
					<div class="well cust_well">
						<h3>Lathund</h3>
						<div class="table-responsive">
							<table class="table table-bordered table-striped cust-table">
								<thead>
									<tr>
										<th><span class="glyphicon glyphicon-comment"></span> E Nummer</th>
										<th><span class="glyphicon glyphicon-comment"></span> Beskrivning</th>
										<th><button class="btn btn-success" type="submit" name="add"><span class="glyphicon glyphicon-plus"></span> Ny</button></th>
									</tr>
								</thead>
								<tbody>
<?php
	$stmt = DB::getInstance()->prepare("SELECT * FROM ENummer WHERE Kategori=:kat ORDER BY ENummer ASC");
	$stmt->bindParam(':kat', $lastkat, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($result as $row)
	{
?>
									<tr>
										<td><?php echo $row['ENummer']?></td>
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
						</div>
					</div>
				</div>
<?php
}

function askdelete($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM ENummer WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
?>
				<div class="well">
					<p>&Auml;r du s&auml;ker p&aring; att du vill radera: <?php echo $row['ENummer']?> - <?php echo $row['Beskrivning']?></p>
					<button class="btn btn-danger" type="submit" name="delete-no" value="no"><span class="glyphicon glyphicon-remove"></span> Nej</button>
					<button class="btn btn-success" type="submit" name="delete-yes" value="<?php echo $id?>"><span class="glyphicon glyphicon-ok"></span> Ja</button>
				</div>

<?php
}

function change($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM ENummer WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
?>
				<div class="well">
					<div class="alert alert-info">
						Enummer från Solar börjar med "S".
						Enummer från Malmbergs börjar med "M".
						Enummer från Ahlsell har ingen prefix.
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="ENummer">E-Nummer:</label>
						<div class="col-sm-8">
							<input id="ENummer" type="text" class="form-control" name="ENummer" placeholder="E-Nummer" required autofocus value="<?php echo $row['ENummer']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning:</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning" required autofocus value="<?php echo $row['Beskrivning']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Enhet">Enhet:</label>
						<div class="col-sm-8">
							<input id="Enhet" type="text" class="form-control" name="Enhet" placeholder="Enhet" required autofocus value="<?php echo $row['Enhet']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Kategori">Kategori:</label>
						<div class="col-sm-8">
							<select class="form-control" id="Kategori" name="Kategori">

<?php
    $result = DB::getInstance()->query("SELECT * FROM Kategori ORDER BY Beskrivning ASC");

    foreach($result as $krow)
	{
?>
								<option <?php if ($row['Kategori'] == $krow['Id']) echo 'selected="selected" ';?>value="<?php echo $krow['Id']?>"><?php echo $krow['Beskrivning']?></option>
<?php
	}
?>
							</select>
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