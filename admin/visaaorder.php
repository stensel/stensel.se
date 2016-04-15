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

$file="aorder.php";
include "include/temp.php";

$locked = 0;

function init()
{
	global $locked, $status, $aorder;
	
	if (isset($_SESSION['aorder']))
	{
		$aorder = $_SESSION['aorder'];
		$stmt = DB::getInstance()->prepare("SELECT Locked FROM AOrder WHERE Id=:id");
		$stmt->bindParam(':id', $aorder, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
		$locked = $row['Locked'];
	}
	else
	{
		header("Location: aorder.php");
	}

	if (isset($_POST['search_material']))
	{
		$_SESSION['search'] = '1';
	}
	
	if (isset($_POST['submit_kat_search']))
	{
		$_SESSION['lastkat'] = $_POST['Kategori'];
	}

	if (isset($_POST['submit_search_back']))
	{
		unset($_SESSION['search']);
	}
	
	if (isset($_POST['submit_add_timmar']))
	{
		$astmt = DB::getInstance()->prepare("SELECT * FROM AOrder WHERE Id=:id");
		$astmt->bindParam(':id', $aorder, PDO::PARAM_INT);
		$astmt->execute();
		$arow = $astmt->fetch();

		$stmt = DB::getInstance()->prepare("INSERT INTO Timraport (Datum, Beskrivning, Tid, Kund_Id, User_Id, AOrder_Id) VALUES (:Datum, :Beskrivning, :Tid, :Kund_Id, :User_Id, :AOrder_Id)");
		$stmt->bindParam(':Datum', $_POST['Datum'], PDO::PARAM_STR, 8);
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'], PDO::PARAM_STR, 100);
		$t = str_replace(",", ".", $_POST['Tid']);
		$stmt->bindParam(':Tid', $t, PDO::PARAM_INT);
		$stmt->bindParam(':User_Id', $_POST['User_Id'], PDO::PARAM_INT);
		$stmt->bindParam(':Kund_Id', $arow['Kund_Id'], PDO::PARAM_INT);
		$stmt->bindParam(':AOrder_Id', $aorder, PDO::PARAM_INT);
		$stmt->execute();
		$status = "Timmar sparad";
	}

	if (isset($_POST['submit_change_timmar']))
	{
		$stmt = DB::getInstance()->prepare("UPDATE Timraport SET Datum=:Datum, Beskrivning=:Beskrivning, Tid=:Tid, User_Id=:User_Id WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['submit_change_timmar'], PDO::PARAM_INT);
		$stmt->bindParam(':Datum', $_POST['Datum'], PDO::PARAM_STR, 8);
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'], PDO::PARAM_STR, 100);
		$t = str_replace(",", ".", $_POST['Tid']);
		$stmt->bindParam(':Tid', $t, PDO::PARAM_INT);
		$stmt->bindParam(':User_Id', $_POST['User_Id'], PDO::PARAM_INT);
		$stmt->execute();
		$status = "Timmar ".$_POST['submit_change_timmar']." ändrad";
	}
	
	if (isset($_POST['timmar_delete-yes']))
	{
		$stmt = DB::getInstance()->prepare("DELETE FROM Timraport WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['timmar_delete-yes'], PDO::PARAM_INT);
		$stmt->execute();
		$status = "Timmar ".$_POST['timmar_delete-yes']." raderad";
	}
	
	if (isset($_POST['submit_add_material']))
	{
		$d = mydate();
		$stmt = DB::getInstance()->prepare("INSERT INTO Material (AOrder_Id, User_Id, E_Nummer, Beskrivning, Antal, Enhet, Datum) VALUES (:AOrder_Id, :User_Id, :E_Nummer, :Beskrivning, :Antal, :Enhet, :Datum)");
		$stmt->bindParam(':AOrder_Id', $aorder, PDO::PARAM_INT);
		$stmt->bindParam(':User_Id', $_POST['User_Id'],PDO::PARAM_INT);
		$stmt->bindParam(':E_Nummer', $_POST['E-Nummer'], PDO::PARAM_STR, 15);
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'],PDO::PARAM_STR, 30);
		$antal = str_replace(",", ".", $_POST['Antal']);
		$stmt->bindParam(':Antal', $antal, PDO::PARAM_INT);
		$stmt->bindParam(':Enhet', $_POST['Enhet'], PDO::PARAM_STR, 10);
		$stmt->bindParam(':Datum',$d, PDO::PARAM_STR, 8);
		$stmt->execute();
		$status = "Material sparad";
	}
	
	if (isset($_POST['submit_change_material']))
	{
		$d = mydate();
		$stmt = DB::getInstance()->prepare("UPDATE Material SET User_Id=:User_Id, E_Nummer=:E_Nummer, Beskrivning=:Beskrivning, Antal=:Antal, Enhet=:Enhet, Datum=:Datum WHERE Id=:id");
		$stmt->bindParam(':User_Id', $_POST['User_Id'], PDO::PARAM_INT);
		$stmt->bindParam(':E_Nummer', $_POST['E-Nummer'], PDO::PARAM_STR, 15);
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'],PDO::PARAM_STR, 30);
		$antal = str_replace(",", ".", $_POST['Antal']);
		$stmt->bindParam(':Antal', $antal, PDO::PARAM_INT);
		$stmt->bindParam(':Enhet', $_POST['Enhet'], PDO::PARAM_STR, 10);
		$stmt->bindParam(':Datum',$d, PDO::PARAM_STR, 8);
		$stmt->bindParam(':id', $_POST['submit_change_material'], PDO::PARAM_INT);
		$stmt->execute();
		$status = "Material ".$_POST['submit_change_material']." ändrad";
	}

	if (isset($_POST['material_delete-yes']))
	{
		$stmt = DB::getInstance()->prepare("DELETE FROM Material WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['material_delete-yes'], PDO::PARAM_INT);
		$stmt->execute();
		$status = "Material ".$_POST['material_delete-yes']." raderad";
	}
}

function page()
{
		global $locked, $status, $aorder;

?>
			<form method="post" class="form-horizontal">
<?php

	if (isset($_POST['timmar_add']))
	{
		add_timmar();
	}
	else if (isset($_POST['timmar_change']))
	{
		change_timmar($_POST['timmar_change']);
	}
	else if (isset($_POST['timmar_delete']))
	{
		delete_timmar($_POST['timmar_delete']);
	}
	else if (isset($_POST['add_material']))
	{
		add_material();
	}
	else if (isset($_POST['material_change']))
	{
		change_material($_POST['material_change']);
	}
	else if (isset($_POST['material_delete']))
	{
		delete_material($_POST['material_delete']);
	}
	else if (isset($_POST['submit_search']))
	{
		unset($_SESSION['search']);
		add_material_search($_POST['submit_search']);
	}
	else if (isset($_SESSION['search']))
	{
		show_list();
	}
	else
	{
		printaordernum($aorder);
		
		if (isset($status))
			printstatus($status);

		show_timmar($aorder);
		show_material($aorder);
	}
?>
			</form>
<?php
}

function printstatus($s)
{
?>
				<br>
				<div class="alert alert-success">
					<?php echo $s?>
				</div>
<?php
}

function printaordernum($s)
{
	$stmt = DB::getInstance()->prepare("SELECT Beskrivning, Kund_Id FROM AOrder WHERE Id=:Id");
	$stmt->bindParam(':Id', $s, PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch();
	$kstmt = DB::getInstance()->prepare("SELECT Namn FROM Kunder WHERE Id=:Id");
	$kstmt->bindParam(':Id', $row['Kund_Id'], PDO::PARAM_INT);
	$kstmt->execute();
	$krow = $kstmt->fetch();
?>
				<br>
				<div class="alert alert-info">
					A-Order Nummer <?php echo $s?> - <?php echo $krow['Namn']?> - <?php echo $row['Beskrivning']?>
				</div>
<?php
}

function add_timmar()
{
?>
				<div class="well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Datum">Datum:</label>
						<div class="col-sm-8">
							<input id="Datum" type="date" class="form-control" name="Datum" placeholder="Datum yyyy-mm-dd" required value="<?php echo mydate2()?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="tBeskrivning">Beskrivning</label>
						<div class="col-sm-8">
							<input id="tBeskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning" autofocus="autofocus">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Tid">Tid</label>
						<div class="col-sm-8">
							<input id="Tid" type="text" class="form-control" name="Tid" placeholder="Tid">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="User_Id">Användare:</label>
						<div class="col-sm-8">
							<select class="form-control" id="User_Id" name="User_Id">

<?php
    $result = DB::getInstance()->query("SELECT * FROM users WHERE Visible=1 ORDER BY FullName ASC");

    foreach($result as $row)
	{
?>
								<option <?php if ($_SESSION['sess_id'] == $row['Id']) echo 'selected="selected" ';?>value="<?php echo $row['Id']?>"><?php echo $row['FullName']?></option>
<?php
	}
?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_add_timmar" id="Btn_save_timmar"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-warning" name="submit_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
				</div>
<?php
}

function change_timmar($id)
{
	$stmt = DB::getInstance()->prepare("SELECT * FROM Timraport WHERE Id=:id");
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch();
?>
				<div class="well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Datum">Datum:</label>
						<div class="col-sm-8">
							<input id="Datum" type="date" class="form-control" name="Datum" placeholder="Datum" required value="<?php echo $row['Datum']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="tBeskrivning">Beskrivning</label>
						<div class="col-sm-8">
							<input id="tBeskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning"value="<?php echo $row['Beskrivning']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Tid">Tid</label>
						<div class="col-sm-8">
							<input id="Tid" type="text" class="form-control" name="Tid" placeholder="Tid" value="<?php echo $row['Tid']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="User_Id">Användare:</label>
						<div class="col-sm-8">
							<select class="form-control" id="User_Id" name="User_Id">

<?php
    $result = DB::getInstance()->query("SELECT * FROM users WHERE Visible=1 ORDER BY FullName ASC");

    foreach($result as $urow)
	{
?>
								<option <?php if ($row['User_Id'] == $urow['Id']) echo 'selected="selected" ';?>value="<?php echo $urow['Id']?>"><?php echo $urow['FullName']?></option>
<?php
	}
?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_change_timmar" id="Btn_save_timmar" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-warning" name="submit_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
				</div>
<?php
}

function delete_timmar($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM Timraport WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
?>
				<div class="well">
					<p>&Auml;r du s&auml;ker p&aring; att du vill radera: <?php echo $row['Datum']."-".$row['Beskrivning']?></p>
					<button class="btn btn-danger" type="submit" name="timmar_delete-no" value="no"><span class="glyphicon glyphicon-remove"></span> Nej</button>
					<button class="btn btn-success" type="submit" name="timmar_delete-yes" value="<?php echo $id?>"><span class="glyphicon glyphicon-ok"></span> Ja</button>
				</div>

<?php
}

function show_timmar($id)
{
	global $locked;


?>				<div class="well cust_well">
					<h3>Timmar</h3>
					<div class="table-responsive">
						<table class="table table-bordered table-striped cust-table">
							<thead>
								<tr>
									<th><span class="glyphicon glyphicon-calendar"></span> Datum</th>
									<th><span class="glyphicon glyphicon-user"></span> Användare</th>
									<th><span class="glyphicon glyphicon-comment"></span> Beskrivning</th>
									<th><span class="glyphicon glyphicon-time"></span> Tid</th>
									<?php if ((($_SESSION['userpriv'] & 0x01) == 0x01) && $locked == 0) {?><th><button class="btn btn-success btn-sm" type="submit" name="timmar_add"><span class="glyphicon glyphicon-plus"></span> Ny</button></th><?php }?>
								</tr>
							</thead>
							<tbody>
<?php
	/*** loop over the results ***/
    $stmt = DB::getInstance()->prepare("SELECT * FROM Timraport WHERE AOrder_Id=:id ORDER BY Datum ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($result as $row)
	{
		$ustmt = DB::getInstance()->prepare("SELECT FullName FROM users WHERE Id=:id");
		$ustmt->bindParam(':id', $row['User_Id'], PDO::PARAM_INT);
		$ustmt->execute();
		$urow = $ustmt->fetch();

?>
								<tr>
									<td><?php echo $row['Datum']?></td>
									<td><?php echo $urow['FullName']?></td>
									<td><?php echo $row['Beskrivning']?></td>
									<td><?php echo $row['Tid']?></td>
									<?php if ((($_SESSION['userpriv'] & 0x05) == 0x01) && $locked == 0) {?><td>
										<button class="btn btn-warning btn-sm" type="submit" name="timmar_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-edit"></span> &Auml;ndra</button>
										<button class="btn btn-danger btn-sm" type="submit" name="timmar_delete" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-remove"></span> Radera</button>
									</td><?php }?>
								</tr>

<?php
	}
?>
							</tbody>
						</table>
					</div>
				</div>

<?php
}

function add_material()
{
?>
				<div class="well">
					<div class="alert alert-info">
						Enummer från Solar börjar med "S".
						Enummer från Malmbergs börjar med "M".
						Enummer från Ahlsell har ingen prefix.
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="E-Nummer">E Nummer:</label>
						<div class="col-sm-8">
							<input id="E-Nummer" type="text" class="form-control" name="E-Nummer" placeholder="E Nummer">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Antal">Antal</label>
						<div class="col-sm-8">
							<input id="Antal" type="text" class="form-control" name="Antal" placeholder="Antal">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Enhet">Antal</label>
						<div class="col-sm-8">
							<input id="Enhet" type="text" class="form-control" name="Enhet" placeholder="Enhet">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="User_Id">Användare:</label>
						<div class="col-sm-8">
							<select class="form-control" id="User_Id" name="User_Id">

<?php
    $result = DB::getInstance()->query("SELECT * FROM users WHERE Visible=1 ORDER BY FullName ASC");

    foreach($result as $row)
	{
?>
								<option <?php if ($_SESSION['sess_id'] == $row['Id']) echo 'selected="selected" ';?>value="<?php echo $row['Id']?>"><?php echo $row['FullName']?></option>
<?php
	}
?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_add_material"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-warning" name="submit_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
				</div>
<?php
}

function add_material_search($list_id)
{
	$stmt = DB::getInstance()->prepare("SELECT * FROM ENummer WHERE Id=:id");
	$stmt->bindParam(':id', $list_id, PDO::PARAM_INT);
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
						<label class="col-sm-2 control-label txt-right hidden-xs" for="E-Nummer">E Nummer:</label>
						<div class="col-sm-8">
							<input id="E-Nummer" type="text" class="form-control" name="E-Nummer" placeholder="E Nummer" value="<?php echo $row['ENummer']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning" value="<?php echo $row['Beskrivning']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Antal">Antal</label>
						<div class="col-sm-8">
							<input id="Antal" type="text" class="form-control" name="Antal" placeholder="Antal" autofocus="autofocus">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Enhet">Antal</label>
						<div class="col-sm-8">
							<input id="Enhet" type="text" class="form-control" name="Enhet" placeholder="Enhet" value="<?php echo $row['Enhet']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="User_Id">Användare:</label>
						<div class="col-sm-8">
							<select class="form-control" id="User_Id" name="User_Id">

<?php
    $result = DB::getInstance()->query("SELECT * FROM users WHERE Visible=1 ORDER BY FullName ASC");

    foreach($result as $urow)
	{
?>
								<option <?php if ($_SESSION['sess_id'] == $urow['Id']) echo 'selected="selected" ';?>value="<?php echo $urow['Id']?>"><?php echo $urow['FullName']?></option>
<?php
	}
?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_add_material" id="Btn_Save"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-warning" name="submit_add_material_cancel"><span class="glyphicon glyphicon-circle-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
				</div>
<?php
}

function change_material($id)
{
	$stmt = DB::getInstance()->prepare("SELECT * FROM Material WHERE Id=:id");
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
						<label class="col-sm-2 control-label txt-right hidden-xs" for="E-Nummer">E Nummer:</label>
						<div class="col-sm-8">
							<input id="E-Nummer" type="text" class="form-control" name="E-Nummer" placeholder="E Nummer" value="<?php echo $row['E_Nummer']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning" value="<?php echo $row['Beskrivning']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Antal">Antal</label>
						<div class="col-sm-8">
							<input id="Antal" type="text" class="form-control" name="Antal" placeholder="Antal" autofocus="autofocus" value="<?php echo $row['Antal']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Enhet">Antal</label>
						<div class="col-sm-8">
							<input id="Enhet" type="text" class="form-control" name="Enhet" placeholder="Enhet" value="<?php echo $row['Enhet']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="User_Id">Användare:</label>
						<div class="col-sm-8">
							<select class="form-control" id="User_Id" name="User_Id">

<?php
    $result = DB::getInstance()->query("SELECT * FROM users WHERE Visible=1 ORDER BY FullName ASC");

    foreach($result as $urow)
	{
?>
								<option <?php if ($_SESSION['sess_id'] == $urow['Id']) echo 'selected="selected" ';?>value="<?php echo $urow['Id']?>"><?php echo $urow['FullName']?></option>
<?php
	}
?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_change_material"  id="Btn_Save" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-warning" name="submit_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
				</div>
<?php
}

function delete_material($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM Material WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
?>
				<div class="well">
					<p>&Auml;r du s&auml;ker p&aring; att du vill radera: <?php echo $row['E_Nummer']."-".$row['Beskrivning']?></p>
					<button class="btn btn-danger" type="submit" name="material_delete-no" value="no"><span class="glyphicon glyphicon-remove"></span> Nej</button>
					<button class="btn btn-success" type="submit" name="material_delete-yes" value="<?php echo $id?>"><span class="glyphicon glyphicon-ok"></span> Ja</button>
				</div>

<?php
}


function show_material($id)
{
	global $locked;
?>
				<div class="well cust_well">
					<h3>Material</h3>
					<div class="table-responsive">
						<table class="table table-bordered table-striped cust-table">
							<thead>
								<tr>
									<th><span class="glyphicon glyphicon-list-alt"></span> E Nummer</th>
									<th><span class="glyphicon glyphicon-comment"></span> Beskrivning</th>
									<th><span class="glyphicon glyphicon-list-alt"></span> Antal</th>
									<th><span class="glyphicon glyphicon-comment"></span> Enhet</th>
									<?php if ((($_SESSION['userpriv'] & 0x01) == 0x01) && $locked == 0) {?><th>
										<button class="btn btn-success btn-sm" type="submit" name="add_material"><span class="glyphicon glyphicon-plus"></span> Ny</button>
										<button class="btn btn-success btn-sm" type="submit" name="search_material"><span class="glyphicon glyphicon-search"></span> Ny från lista</button>
									</th><?php }?>
								</tr>
							</thead>
							<tbody>
<?php
	/*** loop over the results ***/
    $stmt = DB::getInstance()->prepare("SELECT * FROM Material WHERE AOrder_Id=:id ORDER BY E_Nummer,Datum ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($result as $row)
	{
?>
								<tr>
									<td><?php echo $row['E_Nummer']?></td>
									<td><?php echo $row['Beskrivning']?></td>
									<td><?php echo $row['Antal']?></td>
									<td><?php echo $row['Enhet']?></td>
									<?php if ((($_SESSION['userpriv'] & 0x05) == 0x01) && $locked == 0) {?><td>
										<button class="btn btn-warning btn-sm" type="submit" name="material_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-edit"></span> &Auml;ndra</button>
										<button class="btn btn-danger btn-sm" type="submit" name="material_delete" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-remove"></span> Radera</button>
									</td><?php }?>
								</tr>

<?php
	}
?>
							</tbody>
						</table>
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
						<div class="col-sm-6">
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
										<th></th>
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
											<button class="btn btn-success btn-sm" type="submit" name="submit_search" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-plus"></span> Välj</button>
											<button class="btn btn-warning btn-sm" type="submit" name="submit_search_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
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

function mydate()
{
	date_default_timezone_set("Europe/Stockholm");
	return date("Ymd");
}

function mydate2()
{
	date_default_timezone_set("Europe/Stockholm");
	return date("Y-m-d");
}

function show_pagination($posts, $postsperpasge, $pegesshown, $pagevar)
{
	
}

?>