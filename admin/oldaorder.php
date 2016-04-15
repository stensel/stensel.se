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

$file="oldaorder.php";
include "include/temp.php";

function init()
{
	if (isset($_POST['aorder_show']))
	{
		$qstmt = DB::getInstance()->prepare("SELECT * FROM AOrder WHERE Id=:id");
		$qstmt->bindParam(':id', $_POST['aorder_show'], PDO::PARAM_INT);
		$qstmt->execute();
		$qrow = $qstmt->fetch();
		
		$stmt = DB::getInstance()->prepare("UPDATE AOrder SET Besoks_Namn=:Besoks_Namn, Beskrivning=:Beskrivning, Inlamnad=0, Locked=1 WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['aorder_show'], PDO::PARAM_INT);
		$x = "** Redan Inlämnad!!! ** ".$qrow['Besoks_Namn'];
		$stmt->bindParam(':Besoks_Namn', $x, PDO::PARAM_STR, 100);
		$y = "** Redan Inlämnad!!! ** ".$qrow['Beskrivning'];
		$stmt->bindParam(':Beskrivning', $y, PDO::PARAM_STR, 100);
		$stmt->execute();
	}
}

function page()
{
?>
			<form method="post" class="form-horizontal">
<?php
	if (!(intval($_SESSION['userpriv']) & 0x01))
	{
		noaccess();
	}
	else
	{
		if (isset($status))
			printstatus($status);
		listaorder();
	}
	
?>
			</form>
<?php
}

function params()
{
	$s = "";
	if (isset($_GET['apage']))
		$s = "&apage=".$_GET['apage'];
	
	return $s;
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

function listaorder()
{
	$perpage = 10;
	$pagenums = 6;

?>
<?php

	if (($_SESSION['userpriv'] & 0x80) == 0x80)
	{
		$stmt = DB::getInstance()->query("SELECT * FROM AOrder WHERE Inlamnad=1 ORDER BY Registrerad_Datum ASC");
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Find out how many items are in the table
		$cstmt = DB::getInstance()->query("SELECT COUNT(*) FROM AOrder WHERE Inlamnad=1");
		$total = $cstmt->fetchColumn();
	}
	else
	{
		$stmt = DB::getInstance()->prepare("SELECT * FROM AOrder WHERE Inlamnad=1 AND User_Id=:User_Id ORDER BY Registrerad_Datum ASC");
		$stmt->bindParam(':User_Id', $_SESSION['sess_id'], PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Find out how many items are in the table
		$cstmt = DB::getInstance()->prepare("SELECT COUNT(*) FROM AOrder WHERE Inlamnad=1  AND User_Id=:User_Id");
		$cstmt->bindParam(':User_Id', $_SESSION['sess_id'], PDO::PARAM_INT);
		$cstmt->execute();
		$total = $cstmt->fetchColumn();
	}

	// How many items to list per page
	$limit = 10;

	// How many pages will there be
	$pages = ceil($total / $limit);

	// What page are we currently on?
	if (isset($_GET['apage']))
	{
		$page = min($pages, $_GET['apage']);
	}
	else
	{
		$page = 1;
	}

	// Calculate the offset for the query
	$offset = ($page - 1)  * $limit;

	// Some information to display to the user
	$start = $offset;

	$end = min(($offset + $limit), $total);

	$startpage = $page > 4 ? $page - 4 : 1;
	$endpage = min($startpage + 8, $pages);

	if (($endpage - $startpage) < 8)
	{
		if (($endpage - 8) > 0)
			$startpage = $endpage - 8;
	}

	// Display the paging information
?>				<div class="well cust_well">
				<h3>A-Order</h3>
				<ul class="pagination cust-pagination">
					<li><a href="?apage=<?php if ($page > 1) echo $page - 1; else echo "1";?>">«</a></li>
<?php
	for ($p = $startpage; $p <= $endpage; $p++)
	{
?>
					<li <?php if ($page == $p) echo 'class="active"'?>><a href="?apage=<?php echo $p?>"><?php echo $p?></a></li>
<?php
	}
?>
					<li><a href="?apage=<?php if ($page < $pages) echo $page + 1; else echo $pages;?>">»</a></li>
				</ul>
				<table class="table table-bordered table-striped cust-table">
					<thead>
						<tr>
							<th><span class="glyphicon glyphicon-user"></span> A-Order</th>
							<th><span class="glyphicon glyphicon-user"></span> Kund</th>
							<th><span class="glyphicon glyphicon-user"></span> Beskrivning</th>
							<th><span class="glyphicon glyphicon-envelope"></span> Info</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
<?php
	
    for($i = $start; $i < $end; $i++)
{
		$row = $result[$i];
		$kstmt = DB::getInstance()->prepare("SELECT * FROM Kunder WHERE Id=:id");
		$kstmt->bindParam(':id', $row['Kund_Id'], PDO::PARAM_INT);
		$kstmt->execute();
		$krow = $kstmt->fetch();
		
		if (($_SESSION['userpriv'] & 0x80) == 0x80)
		{
			$ustmt = DB::getInstance()->prepare("SELECT FullName FROM users WHERE Id=:id");
			$ustmt->bindParam(':id', $row['User_Id'], PDO::PARAM_INT);
			$ustmt->execute();
			$urow = $ustmt->fetch();
		}

?>
						<tr>
							<td><?php echo $row['Id']?></td>
							<td><?php echo $krow['Namn']?></td>
							<td><?php echo $row['Beskrivning']?><?php if (($_SESSION['userpriv'] & 0x80) == 0x80) { echo "<br>Anv: ".$urow['FullName']; }?></td>
							<td><?php echo $row['Info']?></td>
							<td>
								<button class="btn btn-primary btn-sm" type="submit" name="aorder_show" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-book"></span> O inl&auml;mna</button>
							</td>
						</tr>

<?php
	}
?>
					</tbody>
				</table>
				</div>

<?php
}

?>