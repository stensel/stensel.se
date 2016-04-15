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

$file="showenummer.php";
include "include/temp.php";

function init()
{
	if (isset($_POST['submit_kat_search']))
	{
		$_SESSION['lastkat'] = $_POST['Kategori'];
	}
}

function page()
{
?>
			<form method="post" class="form-horizontal">
<?php

	show_list();

?>
			</form>
<?php
}

function show_list()
{
?>
				<div class="well cust_well hidden-print">
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
							<button class="btn btn-success btn-sm" type="submit" name="submit_kat_search" id="Btn_Kat"><span class="glyphicon glyphicon-plus"></span> Välj</button>
						</div>
					</div>
				</div>
				<div class="well cust_well">
					<h3>Lathund</h3>
<?php
	$stmt = DB::getInstance()->prepare("SELECT * FROM ENummer WHERE Kategori=:kat ORDER BY ENummer ASC");
	$stmt->bindParam(':kat', $lastkat, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
?>
					<div class="table-responsive">
						<table class="table table-bordered table-striped cust-table">
							<thead>
								<tr>
									<th><span class="glyphicon glyphicon-comment"></span> E Nummer</th>
									<th><span class="glyphicon glyphicon-comment"></span> Beskrivning</th>
								</tr>
							</thead>
							<tbody>
<?php
	foreach($result as $row)
	{
?>
								<tr>
									<td><?php echo $row['ENummer']?></td>
									<td><?php echo $row['Beskrivning']?></td>
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

?>