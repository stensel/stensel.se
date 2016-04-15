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

$file="timmar.php";
include "include/temp.php";

function init()
{
	global $cmonth, $cyear, $months, $thisyear;

	date_default_timezone_set("Europe/Stockholm");
	$thisyear = date('Y');

	$months = array (1 => "Januari",
					2 => "Februari",
					3 => "Mars",
					4 => "April",
					5 => "Maj",
					6 => "Juni",
					7 => "Juli",
					8 => "Augusti",
					9 => "September",
					10 => "Oktober",
					11 => "November",
					12 => "December");

	if (isset($_POST['show']))
	{
		$cmonth = $_POST['Manad'];
		$cyear = $_POST['Ar'];
	}
	else
	{
		$cmonth = date('n');
		$cyear = $thisyear;
	}
}

function page()
{
	global $cmonth, $cyear, $months, $thisyear;

?>
			<form method="post" class="form-horizontal">
				<div class="well cust_well">
					<div class="row">
						<label class="col-sm-1 control-label txt-right hidden-xs" for="Ar">&Aring;r:</label>
						<div class="col-sm-2">
							<select class="form-control" id="Ar" name="Ar">

<?php
	for ($i = 2013; $i <= $thisyear; $i++)
	{
?>
								<option <?php if ($cyear == $i) echo 'selected="selected" ';?>value="<?php echo $i?>"><?php echo $i?></option>
<?php
	}
?>
							</select>
						</div>
						<label class="col-sm-1 control-label txt-right hidden-xs" for="Manad">M&aring;nad:</label>
						<div class="col-sm-2">
							<select class="form-control" id="Manad" name="Manad">

<?php
	for ($i = 1; $i <= 12; $i++)
	{
?>
								<option <?php if ($cmonth == $i) echo 'selected="selected" ';?>value="<?php echo $i?>"><?php echo $months[$i]?></option>
<?php
	}
?>
							</select>
						</div>
						<div class="col-sm-2">
							<button class="btn btn-success btn-sm hidden-xs" type="submit" name="show" id="Btn_TimSel"><span class="glyphicon glyphicon-plus"></span> V&auml;lj</button>
						</div>
					</div>
					<h3>Lathund</h3>
					<div class="table-responsive">
						<table class="table table-bordered table-striped cust-table">
							<thead>
								<tr>
									<th><span class="glyphicon glyphicon-calendar"></span> Datum</th>
									<th><span class="glyphicon glyphicon-comment"></span> Beskrivning</th>
									<th><span class="glyphicon glyphicon-time"></span> Timmar</th>
									<th><span class="glyphicon glyphicon-user"></span> Kund</th>
									<th><span class="glyphicon glyphicon-list-alt"></span> A-Order</th>
								</tr>
							</thead>
							<tbody>
<?php

	if ($cmonth < 10)
		$month = "0".$cmonth;
	else
		$month = $cmonth;
		
	$sd = $cyear.$month."01";
	$ed = $cyear.$month."31";

	$stmt = DB::getInstance()->prepare("SELECT * FROM Timraport WHERE User_Id=:User_Id AND Datum BETWEEN :sd AND :ed ORDER BY Datum ASC");
	$stmt->bindParam(':User_Id',$_SESSION['sess_id'] , PDO::PARAM_INT);
	$stmt->bindParam(':sd', $sd, PDO::PARAM_STR, 8);
	$stmt->bindParam(':ed', $ed, PDO::PARAM_STR, 8);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($result as $row)
	{
		$kstmt = DB::getInstance()->prepare("SELECT * FROM Kunder WHERE Id=:id");
		$kstmt->bindParam(':id', $row['Kund_Id'], PDO::PARAM_INT);
		$kstmt->execute();
		$krow = $kstmt->fetch();

?>
								<tr>
									<td><?php echo $row['Datum']?></td>
									<td><?php echo $row['Beskrivning']?></td>
									<td><?php echo $row['Tid']?></td>
									<td><?php echo $krow['Namn']?></td>
									<td><?php echo $row['AOrder_Id']?></td>
								</tr>

<?php
	}
?>
							</tbody>
						</table>
					</div>
				</div>

<?php
	
?>
			</form>
<?php
}
