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

function init()
{
	global $localnav, $localnavname;
	
	$localnav = "#kund";
	$localnavname = "Gå till kunder";
	
	if (isset($_SESSION['search']))
	{
		unset($_SESSION['search']);
	}

	if (isset($_POST['aorder_show']))
	{
		$_SESSION['aorder'] = $_POST['aorder_show'];
		header("Location: visaaorder.php");
	}

	if (isset($_POST['k_search']))
	{
		$s = "?ksearch=".$_POST['ksearch'].params();
		header("Location: ".$file.$s."#kund");
	}

	if (isset($_POST['aorder_send']))
	{
		send_aorder($_POST['aorder_send']);
		$status = "A-Order: ".$_POST['aorder_send']." skickad";
	}
	
	if (isset($_POST['submit_add']))
	{
		if ($_POST['BSame'])
		{
			$kstmt = DB::getInstance()->prepare("SELECT * FROM Kunder WHERE Id=:id");
			$kstmt->bindParam(':id', $_POST['KundId'], PDO::PARAM_INT);
			$kstmt->execute();
			$krow = $kstmt->fetch();
		}
		date_default_timezone_set("Europe/Stockholm");
		$d = date("Ymd");
		$sql = "INSERT INTO AOrder (Kund_Id, User_Id, Besoks_Namn, Besoks_Adress, Besoks_PostNr, Besoks_PostAdress, Besoks_Land, Registrerad_Datum, Andrad_Datum, Beskrivning, Info) ";
		$sql = $sql."VALUES (:Kund_Id, :User_Id, :Besoks_Namn, :Besoks_Adress, :Besoks_PostNr, :Besoks_PostAdress, :Besoks_Land, :Registrerad_Datum, :Andrad_Datum, :Beskrivning, :Info);";
		$stmt = DB::getInstance()->prepare($sql);
		$stmt->bindParam(':Kund_Id', $_POST['KundId'], PDO::PARAM_INT);
		$stmt->bindParam(':User_Id', $_SESSION['sess_id'], PDO::PARAM_INT);

		if ($_POST['BSame'])
		{
			$stmt->bindParam(':Besoks_Namn', $krow['Namn'], PDO::PARAM_STR, 50);
			$stmt->bindParam(':Besoks_Adress', $krow['Faktura_Adress'], PDO::PARAM_STR,30);
			$stmt->bindParam(':Besoks_PostNr', $krow['Faktura_PostNr'], PDO::PARAM_STR, 10);
			$stmt->bindParam(':Besoks_PostAdress', $krow['Faktura_PostAdress'], PDO::PARAM_STR, 50);
		}
		else
		{
			$stmt->bindParam(':Besoks_Namn', $_POST['BNamn'], PDO::PARAM_STR, 50);
			$stmt->bindParam(':Besoks_Adress', $_POST['BAddr'], PDO::PARAM_STR,30);
			$stmt->bindParam(':Besoks_PostNr', $_POST['BPostNr'], PDO::PARAM_STR, 10);
			$stmt->bindParam(':Besoks_PostAdress', $_POST['BPostAddr'], PDO::PARAM_STR, 50);
		}
		$stmt->bindParam(':Registrerad_Datum', $d, PDO::PARAM_STR, 8);
		$stmt->bindParam(':Andrad_Datum', $d, PDO::PARAM_STR, 8);
		$stmt->bindParam(':Besoks_Land', $_POST['BLand'], PDO::PARAM_STR, 20);
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'], PDO::PARAM_STR, 200);
		$stmt->bindParam(':Info', $_POST['Info'], PDO::PARAM_STR, 50);
		$stmt->execute();
		$_SESSION['aorder'] = DB::getInstance()->lastInsertId();
		header("Location: visaaorder.php");
	}
	
	if (isset($_POST['aorder_delete-yes']))
	{
		$stmt = DB::getInstance()->prepare("DELETE FROM AOrder WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['aorder_delete-yes'], PDO::PARAM_INT);
		$stmt->execute();
		$status = "A-Order ".$_POST['aorder_delete-yes']." raderad";
	}
	
	if (isset($_POST['aorder_submit_change']))
	{
		date_default_timezone_set("Europe/Stockholm");
		$d = date("Ymd");
		$stmt = DB::getInstance()->prepare("UPDATE AOrder SET Kund_Id=:Kund_Id, Besoks_Namn=:Besoks_Namn, Besoks_Adress=:Besoks_Adress, Besoks_PostNr=:Besoks_PostNr, Besoks_PostAdress=:Besoks_PostAdress, Andrad_Datum=:Andrad_Datum, Besoks_Land=:Besoks_Land, Beskrivning=:Beskrivning, Info=:Info WHERE Id=:Id");
		$stmt->bindParam(':Kund_Id', $_POST['KundId'], PDO::PARAM_INT);
		$stmt->bindParam(':Besoks_Namn', $_POST['BNamn'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Besoks_Adress', $_POST['BAddr'], PDO::PARAM_STR,30);
		$stmt->bindParam(':Besoks_PostNr', $_POST['BPostNr'], PDO::PARAM_STR, 10);
		$stmt->bindParam(':Besoks_PostAdress', $_POST['BPostAddr'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Andrad_Datum', $d, PDO::PARAM_STR, 8);
		$stmt->bindParam(':Besoks_Land', $_POST['BLand'], PDO::PARAM_STR, 20);
		$stmt->bindParam(':Beskrivning', $_POST['Beskrivning'], PDO::PARAM_STR, 200);
		$stmt->bindParam(':Info', $_POST['Info'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Id', $_POST['aorder_submit_change'], PDO::PARAM_INT);
		$stmt->execute();
	}
	
	if (isset($_POST['submit_kund_add']))
	{
		$stmt = DB::getInstance()->prepare("INSERT INTO Kunder (Namn, Faktura_Adress, Faktura_PostNr, Faktura_PostAdress, Faktura_Land, Telefon, Mobil, EPost, PersonNr, Fastighetsbeteckning) VALUES (:Namn, :Faktura_Adress, :Faktura_PostNr, :Faktura_PostAdress, :Faktura_Land, :Telefon, :Mobil, :EPost, :PersonNr, :Fastighetsbeteckning)");
		$stmt->bindParam(':Namn', $_POST['Namn'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Faktura_Adress', $_POST['Faktura_Adress'], PDO::PARAM_STR, 30);
		$stmt->bindParam(':Faktura_PostNr', $_POST['Faktura_PostNr'], PDO::PARAM_STR, 15);
		$stmt->bindParam(':Faktura_PostAdress', $_POST['Faktura_PostAdress'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Faktura_Land', $_POST['Faktura_Land'], PDO::PARAM_STR, 30);
		$stmt->bindParam(':Telefon', $_POST['Telefon'], PDO::PARAM_STR, 30);
		$stmt->bindParam(':Mobil', $_POST['Mobil'], PDO::PARAM_STR, 30);
		$stmt->bindParam(':EPost', $_POST['EPost'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':PersonNr', $_POST['PersonNr'], PDO::PARAM_STR, 15);
		$stmt->bindParam(':Fastighetsbeteckning', $_POST['Fastighetsbeteckning'], PDO::PARAM_STR, 30);
		$stmt->execute();
	}
	
	if (isset($_POST['submit_kund_change']))
	{
		$stmt = DB::getInstance()->prepare("UPDATE Kunder SET Namn=:Namn, Faktura_Adress=:Faktura_Adress, Faktura_PostNr=:Faktura_PostNr, Faktura_PostAdress=:Faktura_PostAdress, Faktura_Land=:Faktura_Land, Telefon=:Telefon, Mobil=:Mobil, EPost=:EPost, PersonNr=:PersonNr, Fastighetsbeteckning=:Fastighetsbeteckning WHERE Id=:id");
		$stmt->bindParam(':id', $_POST['submit_kund_change'], PDO::PARAM_INT);
		$stmt->bindParam(':Namn', $_POST['Namn'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Faktura_Adress', $_POST['Faktura_Adress'], PDO::PARAM_STR, 30);
		$stmt->bindParam(':Faktura_PostNr', $_POST['Faktura_PostNr'], PDO::PARAM_STR, 15);
		$stmt->bindParam(':Faktura_PostAdress', $_POST['Faktura_PostAdress'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':Faktura_Land', $_POST['Faktura_Land'], PDO::PARAM_STR, 30);
		$stmt->bindParam(':Telefon', $_POST['Telefon'], PDO::PARAM_STR, 30);
		$stmt->bindParam(':Mobil', $_POST['Mobil'], PDO::PARAM_STR, 30);
		$stmt->bindParam(':EPost', $_POST['EPost'], PDO::PARAM_STR, 50);
		$stmt->bindParam(':PersonNr', $_POST['PersonNr'], PDO::PARAM_STR, 15);
		$stmt->bindParam(':Fastighetsbeteckning', $_POST['Fastighetsbeteckning'], PDO::PARAM_STR, 30);
		$stmt->execute();
	}
}

function page()
{
?>
			<form method="post" class="form-horizontal">
<?php
	
	if (isset($_POST['aorder_new']))
	{
		addaorderform($_POST['aorder_new']);
	}
	else if (isset($_POST['aorder_delete']))
	{
		askaorderdelete($_POST['aorder_delete']);
	}
	else if (isset($_POST['aorder_change']))
	{
		changeaorder($_POST['aorder_change']);
	}
	else if (isset($_POST['add_kund']))
	{
		addkundform();
	}
	else if (isset($_POST['kund_change']))
	{
		changekunderform($_POST['kund_change']);
	}
	else
	{
		if (isset($status))
			printstatus($status);
		listaorder();
		listkunder();
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

function aparams()
{
	$s = "";
	if (isset($_GET['ksearch']))
		$s = "&ksearch=".$_GET['ksearch'];
	if (isset($_GET['kpage']))
		$s = $s."&kpage=".$_GET['kpage'];
	
	echo $s;
}

function kparams()
{
	$s = "";
	if (isset($_GET['apage']))
		$s = "&apage=".$_GET['apage'];
	if (isset($_GET['ksearch']))
		$s = $s."&ksearch=".$_GET['ksearch'];
	
	echo $s."#kund";
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

function addaorderform($id)
{
?>
				<div class="well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="KundId">Kund Id:</label>
						<div class="col-sm-8">
							<input id="KundId" type="text" class="form-control" name="KundId" placeholder="Kund Id" required value="<?php echo $id?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning:">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Info">Extra Info:</label>
						<div class="col-sm-8">
							<input id="Info" type="text" class="form-control" name="Info" placeholder="Extra Info">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BNamn">Bes&ouml;ks Namn:</label>
						<div class="col-sm-8">
							<input id="BNamn" type="text" class="form-control" name="BNamn" placeholder="Bes&ouml;ks Namn">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BAddr">Bes&ouml;ks Adress:</label>
						<div class="col-sm-8">
							<input id="BAddr" type="text" class="form-control" name="BAddr" placeholder="Bes&ouml;ks Adress">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BPostNr">Bes&ouml;ks Post Nr:</label>
						<div class="col-sm-8">
							<input id="BPostNr" type="text" class="form-control" name="BPostNr" placeholder="Bes&ouml;ks Post Nr">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BPostAddr">Bes&ouml;ks Post Adress:</label>
						<div class="col-sm-8">
							<input id="BPostAddr" type="text" class="form-control" name="BPostAddr" placeholder="Bes&ouml;ks Post Adress">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BLand">Bes&ouml;ks Land:</label>
						<div class="col-sm-8">
							<input id="BLand" type="text" class="form-control" name="BLand" placeholder="Bes&ouml;ks Land">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<label class="checkbox-inline"><input id="BSame" type="checkbox" name="BSame" value="1">Bes&ouml;ks adress samma som faktura adress</label>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_add"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-success" name="submit_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
				</div>
<?php
}

function listaorder()
{
	$perpage = 10;
	$pagenums = 6;

?>				<div class="well cust_well">
					<h3>A-Order för <?php echo $_SESSION['sess_fullname']?></h3>

<?php
    $stmt = DB::getInstance()->prepare("SELECT * FROM AOrder WHERE Inlamnad=0 AND User_Id=:User_Id ORDER BY Registrerad_Datum ASC");
	$stmt->bindParam(':User_Id', $_SESSION['sess_id'], PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Find out how many items are in the table
	$cstmt = DB::getInstance()->prepare("SELECT COUNT(*) FROM AOrder WHERE Inlamnad=0 AND User_Id=:User_Id");
	$cstmt->bindParam(':User_Id', $_SESSION['sess_id'], PDO::PARAM_INT);
	$cstmt->execute();
	$total = $cstmt->fetchColumn();

	// Find out how many items are in the table
//	$total = $stmt->rowCount();

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
?>
					<ul class="pagination cust-pagination">
						<li><a href="?apage=1<?php aparams()?>">|«</a></li>
						<li><a href="?apage=<?php if ($page > 1) echo $page - 1; else echo "1";?><?php aparams()?>">«</a></li>
<?php
	for ($p = $startpage; $p <= $endpage; $p++)
	{
?>
						<li <?php if ($page == $p) echo 'class="active"'?>><a href="?apage=<?php echo $p?><?php aparams()?>"><?php echo $p?></a></li>
<?php
	}
?>
						<li><a href="?apage=<?php if ($page < $pages) echo $page + 1; else echo $pages;?><?php aparams()?>">»</a></li>
						<li><a href="?apage=<?php echo $pages?><?php aparams()?>">»|</a></li>
					</ul>
					<div class="table-responsive">
						<table class="table table-bordered table-striped cust-table">
							<thead>
								<tr>
									<th class="col-sm-2 col-md-2 col-lg-1"><span class="glyphicon glyphicon-user"></span> Kund</th>
									<th class="col-sm-4 col-md-3 col-lg-3"><span class="glyphicon glyphicon-comment"></span> Beskrivning</th>
									<th class="hidden-xs hidden-sm hidden-md col-lg-2"><span class="glyphicon glyphicon-comment"></span> Info</th>
									<th class="hidden-xs hidden-sm col-md-2 col-lg-2"><span class="glyphicon glyphicon-list-alt"></span> A-Order Nr</th>
									<th class="col-sm-6 col-md-5 col-lg-4"></th>
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

?>
								<tr>
									<td class="col-sm-2 col-md-2 col-lg-1"><?php echo $krow['Namn']?></td>
									<td class="col-sm-4 col-md-3 col-lg-3"><?php echo $row['Beskrivning']?></td>
									<td class="hidden-xs hidden-sm hidden-md col-lg-2"><?php echo $row['Info']?></td>
									<td class="hidden-xs hidden-sm col-md-2 col-lg-2"><?php echo $row['Id']?></td>
									<td class="col-sm-6 col-md-5 col-lg-4 vert-align">
										<button class="btn btn-primary btn-sm" type="submit" name="aorder_show" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-book"></span> Visa</button>
										<?php if ($row['Locked'] == 0) {?><button class="btn btn-warning btn-sm" name="aorder_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-edit"></span> &Auml;ndra</button>
										<button class="btn btn-danger btn-sm" type="submit" name="aorder_delete" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-remove"></span> Radera</button><?php }?>
										<button class="btn btn-success btn-sm" type="submit" name="aorder_send" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-envelope"></span> Inl&auml;mna</button>
									</td>
								</tr>
<?php
	}
?>
							</tbody>
						</table>
					</div>
					<h3>A-Order</h3>

<?php
    $stmt = DB::getInstance()->prepare("SELECT * FROM AOrder WHERE Inlamnad=0 AND User_Id!=:User_Id AND Locked=0 ORDER BY Registrerad_Datum ASC");
	$stmt->bindParam(':User_Id', $_SESSION['sess_id'], PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Find out how many items are in the table
	$cstmt = DB::getInstance()->prepare("SELECT COUNT(*) FROM AOrder WHERE Inlamnad=0 AND User_Id!=:User_Id AND Locked=0");
	$cstmt->bindParam(':User_Id', $_SESSION['sess_id'], PDO::PARAM_INT);
	$cstmt->execute();
	$total = $cstmt->fetchColumn();

	// Find out how many items are in the table
//	$total = $stmt->rowCount();

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
?>
					<ul class="pagination cust-pagination">
						<li><a href="?apage=1<?php aparams()?>">|«</a></li>
						<li><a href="?apage=<?php if ($page > 1) echo $page - 1; else echo "1";?><?php aparams()?>">«</a></li>
<?php
	for ($p = $startpage; $p <= $endpage; $p++)
	{
?>
						<li <?php if ($page == $p) echo 'class="active"'?>><a href="?apage=<?php echo $p?><?php aparams()?>"><?php echo $p?></a></li>
<?php
	}
?>
						<li><a href="?apage=<?php if ($page < $pages) echo $page + 1; else echo $pages;?><?php aparams()?>">»</a></li>
						<li><a href="?apage=<?php echo $pages?><?php aparams()?>">»|</a></li>
					</ul>
					<div class="table-responsive">
						<table class="table table-bordered table-striped cust-table">
							<thead>
								<tr>
									<th class="col-sm-2 col-md-2 col-lg-1"><span class="glyphicon glyphicon-user"></span> Kund</th>
									<th class="col-sm-4 col-md-3 col-lg-3"><span class="glyphicon glyphicon-comment"></span> Beskrivning</th>
									<th class="hidden-xs hidden-sm hidden-md col-lg-2"><span class="glyphicon glyphicon-comment"></span> Info</th>
									<th class="hidden-xs hidden-sm col-md-2 col-lg-2"><span class="glyphicon glyphicon-list-alt"></span> A-Order Nr</th>
									<th class="col-sm-6 col-md-5 col-lg-4"></th>
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
		$ustmt = DB::getInstance()->prepare("SELECT * FROM users WHERE Id=:id");
		$ustmt->bindParam(':id', $row['User_Id'], PDO::PARAM_INT);
		$ustmt->execute();
		$urow = $ustmt->fetch();

?>
								<tr>
									<td class="col-sm-2 col-md-2 col-lg-1"><?php echo $krow['Namn']?></td>
									<td class="col-sm-4 col-md-3 col-lg-3"><?php echo $row['Beskrivning']?><br>(Anv: <?php echo $urow['FullName']?>)</td>
									<td class="hidden-xs hidden-sm hidden-md col-lg-2"><?php echo $row['Info']?></td>
									<td class="hidden-xs hidden-sm col-md-2 col-lg-2"><?php echo $row['Id']?></td>
									<td class="col-sm-6 col-md-5 col-lg-4 vert-align">
										<button class="btn btn-primary btn-sm" type="submit" name="aorder_show" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-book"></span> Visa</button>
										<?php if (intval($_SESSION['userpriv']) & 0x80) {?><button class="btn btn-warning btn-sm" name="aorder_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-edit"></span> &Auml;ndra</button>
										<button class="btn btn-danger btn-sm" type="submit" name="aorder_delete" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-remove"></span> Radera</button>
										<button class="btn btn-success btn-sm" type="submit" name="aorder_send" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-envelope"></span> Inl&auml;mna</button><?php }?>
									</td>
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

function askaorderdelete($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM AOrder WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
	
		$kstmt = DB::getInstance()->prepare("SELECT * FROM Kunder WHERE Id=:id");
		$kstmt->bindParam(':id', $row['Kund_Id'], PDO::PARAM_INT);
		$kstmt->execute();
		$krow = $kstmt->fetch();
		
?>
				<div class="well">
					<p>&Auml;r du s&auml;ker p&aring; att du vill radera: <?php echo $krow['Namn']?>, <?php echo $row['Beskrivning']?></p>
					<button class="btn btn-danger" type="submit" name="aorder_delete-no" value="no"><span class="glyphicon glyphicon-remove"></span> Nej</button>
					<button class="btn btn-success" type="submit" name="aorder_delete-yes" value="<?php echo $id?>"><span class="glyphicon glyphicon-ok"></span> Ja</button>
				</div>

<?php
}

function changeaorder($id)
{
		$stmt = DB::getInstance()->prepare("SELECT * FROM AOrder WHERE Id=:id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();

		$kstmt = DB::getInstance()->prepare("SELECT * FROM Kunder WHERE Id=:id");
		$kstmt->bindParam(':id', $row['Kund_Id'], PDO::PARAM_INT);
		$kstmt->execute();
		$krow = $kstmt->fetch();
?>
				<div class="well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="KundId">Kund Id: (<?php echo $krow['Namn']?>)</label>
						<div class="col-sm-8">
							<input id="KundId" type="text" class="form-control" name="KundId" placeholder="Kund Id" required value="<?php echo $row['Kund_Id']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Beskrivning">Beskrivning</label>
						<div class="col-sm-8">
							<input id="Beskrivning" type="text" class="form-control" name="Beskrivning" placeholder="Beskrivning:" required value="<?php echo $row['Beskrivning']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Info">Extra Info:</label>
						<div class="col-sm-8">
							<input id="Info" type="text" class="form-control" name="Info" placeholder="Extra Info" value="<?php echo $row['Info']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BNamn">Bes&ouml;ks Namn:</label>
						<div class="col-sm-8">
							<input id="BNamn" type="text" class="form-control" name="BNamn" placeholder="Bes&ouml;ks Namn" value="<?php echo $row['Besoks_Namn']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BAddr">Bes&ouml;ks Adress:</label>
						<div class="col-sm-8">
							<input id="BAddr" type="text" class="form-control" name="BAddr" placeholder="Bes&ouml;ks Adress" value="<?php echo $row['Besoks_Adress']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BPostNr">Bes&ouml;ks Post Nr:</label>
						<div class="col-sm-8">
							<input id="BPostNr" type="text" class="form-control" name="BPostNr" placeholder="Bes&ouml;ks Post Nr" value="<?php echo $row['Besoks_PostNr']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BPostAddr">Bes&ouml;ks Post Adress:</label>
						<div class="col-sm-8">
							<input id="BPostAddr" type="text" class="form-control" name="BPostAddr" placeholder="Bes&ouml;ks Post Adress" value="<?php echo $row['Besoks_PostAdress']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="BLand">Bes&ouml;ks Land:</label>
						<div class="col-sm-8">
							<input id="BLand" type="text" class="form-control" name="BLand" placeholder="Bes&ouml;ks Land" value="<?php echo $row['Besoks_Land']?>">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="aorder_submit_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-success" name="submit_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
<?php
}

function listkunder()
{
	$perpage = 10;
	$pagenums = 6;
?>				<div class="well cust_well">
					<a name="kund"></a><h3>Kunder</h3>
<?php
	/*** loop over the results ***/
	if (isset($_GET['ksearch']))
	{
		$s = '%'.$_GET['ksearch'].'%';
		$cstmt = DB::getInstance()->prepare("SELECT COUNT(*) FROM Kunder WHERE Namn LIKE :search");
		$cstmt->bindParam(':search', $s, PDO::PARAM_STR);
		$stmt = DB::getInstance()->prepare("SELECT * FROM Kunder WHERE Namn LIKE :search ORDER BY Namn ASC;");
		$stmt->bindParam(':search', $s, PDO::PARAM_STR);
	}
	else
	{
		$cstmt = DB::getInstance()->prepare("SELECT COUNT(*) FROM Kunder");
		$stmt = DB::getInstance()->prepare("SELECT * FROM Kunder ORDER BY Namn ASC");
	}
	
	$cstmt->execute();
	$total = $cstmt->fetchColumn();

	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	// Find out how many items are in the table
//	$total = $stmt->rowCount();

	// How many items to list per page
	$limit = 10;

	// How many pages will there be
	$pages = ceil($total / $limit);

	// What page are we currently on?
	if (isset($_GET['kpage']))
	{
		$page = min($pages, $_GET['kpage']);
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
?>
					<div class="row">
						<label class="col-sm-1 control-label txt-right hidden-xs" for="ksearch">S&ouml;k kund:</label>
						<div class="col-sm-9 col-xs-8">
							<input id="ksearch" type="text" class="form-control" name="ksearch" placeholder="S&ouml;k kund:">
						</div>
						<div class="col-sm-2 col-xs-2">
							<button class="btn btn-primary" type="submit" id="Btn_ksearch" name="k_search"><span class="glyphicon glyphicon-search"></span> S&ouml;k</button>
						</div>
					</div>
					<ul class="pagination cust-pagination">
						<li><a href="?kpage=1<?php kparams()?>">|«</a></li>
						<li><a href="?kpage=<?php if ($page > 1) echo $page - 1; else echo "1";?><?php kparams()?>">«</a></li>
<?php
	for ($p = $startpage; $p <= $endpage; $p++)
	{
?>
						<li <?php if ($page == $p) echo 'class="active"'?>><a href="?kpage=<?php echo $p?><?php kparams()?>"><?php echo $p?></a></li>
<?php
	}
?>
						<li><a href="?kpage=<?php if ($page < $pages) echo $page + 1; else echo $pages;?><?php kparams()?>">»</a></li>
						<li><a href="?kpage=<?php echo $pages;?><?php kparams()?>">»|</a></li>
					</ul>
					<div class="table-responsive">
						<table class="table table-bordered table-striped cust-table">
							<thead>
								<tr>
									<th class="col-lg-8 col-sm-5 col-md-6"><span class="glyphicon glyphicon-user"></span> Kund</th>
									<th class="col-lg-1 col-sm-2 col-md-2"><span class="glyphicon glyphicon-user"></span> Id</th>
									<th class="col-lg-3 col-sm-5 col-md-4"><button class="btn btn-success btn-sm" type="submit" name="add_kund"><span class="glyphicon glyphicon-plus"></span> Ny</button></th>
								</tr>
							</thead>
							<tbody>
<?php
	
    for($i = $start; $i < $end; $i++)
	{
		$row = $result[$i];
?>
								<tr>
									<td class="col-lg-8 col-sm-5 col-md-6"><?php echo $row['Namn']?><div><?php echo $row['Faktura_Adress']?></div></td>
									<td class="col-lg-1 col-sm-2 col-md-2"><?php echo $row['Id']?></td>
									<td class="col-lg-3 col-sm-5 col-md-4 vert-align">
										<button class="btn btn-success btn-sm" type="submit" name="aorder_new" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-plus"></span> Ny A-order</button>
										<button class="btn btn-warning btn-sm" type="submit" name="kund_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-edit"></span> &Auml;ndra</button>
										<button class="btn btn-danger btn-sm" type="submit" name="kund_delete" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-remove"></span> Radera</button>
									</td>
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

function addkundform()
{
?>
				<div class="well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Namn">Namn:</label>
						<div class="col-sm-8">
							<input id="Namn" type="text" class="form-control" name="Namn" placeholder="Kundens namn">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Faktura_Adress">Faktura Adress:</label>
						<div class="col-sm-8">
							<input id="Faktura_Adress" type="text" class="form-control" name="Faktura_Adress" placeholder="Kundens faktura adress">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Faktura_PostNr">Faktura PostNr:</label>
						<div class="col-sm-8">
							<input id="Faktura_PostNr" type="text" class="form-control" name="Faktura_PostNr" placeholder="Kundens faktura post nummer">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Faktura_PostAdress">Faktura Postadress:</label>
						<div class="col-sm-8">
							<input id="Faktura_PostAdress" type="text" class="form-control" name="Faktura_PostAdress" placeholder="Kundens faktura post adress">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Faktura_Land">Faktura land:</label>
						<div class="col-sm-8">
							<input id="Faktura_Land" type="text" class="form-control" name="Faktura_Land" placeholder="Kundens faktura land">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Telefon">Telefon:</label>
						<div class="col-sm-8">
							<input id="Telefon" type="text" class="form-control" name="Telefon" placeholder="Kundens Telefonnummer">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Mobil">Mobil:</label>
						<div class="col-sm-8">
							<input id="Mobil" type="text" class="form-control" name="Mobil" placeholder="Kundens mobilnummer">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="EPost">E-Post:</label>
						<div class="col-sm-8">
							<input id="EPost" type="text" class="form-control" name="EPost" placeholder="Kundens E-Post">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="PersonNr">Person Nr:</label>
						<div class="col-sm-8">
							<input id="PersonNr" type="text" class="form-control" name="PersonNr" placeholder="Kundens personnummer">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Fastighetsbeteckning">Fastighetsbeteckning:</label>
						<div class="col-sm-8">
							<input id="Fastighetsbeteckning" type="text" class="form-control" name="Fastighetsbeteckning" placeholder="Kundens fastighetsbeteckning">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button class="btn btn-success" type="submit" name="submit_kund_add"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-success" name="submit_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
				</div>
<?php
}

function changekunderform($id)
{
	$stmt = DB::getInstance()->prepare("SELECT * FROM Kunder WHERE Id=:id");
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch();

?>
				<div class="well">
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Namn">Namn:</label>
						<div class="col-sm-8">
							<input id="Namn" type="text" class="form-control" name="Namn" placeholder="Kundens namn" value="<?php echo $row['Namn']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Faktura_Adress">Faktura Adress:</label>
						<div class="col-sm-8">
							<input id="Faktura_Adress" type="text" class="form-control" name="Faktura_Adress" placeholder="Kundens faktura adress" value="<?php echo $row['Faktura_Adress']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Faktura_PostNr">Faktura PostNr:</label>
						<div class="col-sm-8">
							<input id="Faktura_PostNr" type="text" class="form-control" name="Faktura_PostNr" placeholder="Kundens faktura post nummer" value="<?php echo $row['Faktura_PostNr']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Faktura_PostAdress">Faktura Postadress:</label>
						<div class="col-sm-8">
							<input id="Faktura_PostAdress" type="text" class="form-control" name="Faktura_PostAdress" placeholder="Kundens faktura post adress" value="<?php echo $row['Faktura_PostAdress']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Faktura_Land">Faktura land:</label>
						<div class="col-sm-8">
							<input id="Faktura_Land" type="text" class="form-control" name="Faktura_Land" placeholder="Kundens faktura land" value="<?php echo $row['Faktura_Land']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Telefon">Telefon:</label>
						<div class="col-sm-8">
							<input id="Telefon" type="text" class="form-control" name="Telefon" placeholder="Kundens Telefonnummer" value="<?php echo $row['Telefon']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Mobil">Mobil:</label>
						<div class="col-sm-8">
							<input id="Mobil" type="text" class="form-control" name="Mobil" placeholder="Kundens mobilnummer" value="<?php echo $row['Mobil']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="EPost">E-Post:</label>
						<div class="col-sm-8">
							<input id="EPost" type="text" class="form-control" name="EPost" placeholder="Kundens E-Post"  value="<?php echo $row['EPost']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="PersonNr">Person Nr:</label>
						<div class="col-sm-8">
							<input id="PersonNr" type="text" class="form-control" name="PersonNr" placeholder="Kundens personnummer" value="<?php echo $row['PersonNr']?>">
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label txt-right hidden-xs" for="Fastighetsbeteckning">Fastighetsbeteckning:</label>
						<div class="col-sm-8">
							<input id="Fastighetsbeteckning" type="text" class="form-control" name="Fastighetsbeteckning" placeholder="Kundens fastighetsbeteckning" value="<?php echo $row['Fastighetsbeteckning']?>">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<button type="submit" class="btn btn-success" name="submit_kund_change" value="<?php echo $row['Id']?>"><span class="glyphicon glyphicon-save"></span> Spara</button>
							<button type="submit" class="btn btn-success" name="submit_back"><span class="glyphicon glyphicon-arrow-left"></span> Tillbaka</button>
						</div>
					</div>
				</div>
<?php
}

function send_aorder($aorder)
{
	$astmt = DB::getInstance()->prepare("SELECT * FROM AOrder WHERE Id=:id");
	$astmt->bindParam(':id', $aorder, PDO::PARAM_INT);
	$astmt->execute();
	$adata = $astmt->fetch();

	$kstmt = DB::getInstance()->prepare("SELECT * FROM Kunder WHERE Id=:id");
	$kstmt->bindParam(':id', $adata['Kund_Id'], PDO::PARAM_INT);
	$kstmt->execute();
	$kdata = $kstmt->fetch();

	$mess = array();
	
	$mess[] = "<html>";
	$mess[] = "\t<head>";
	$mess[] = "\t\t<title>A-Order: ".htmlentities($adata['Id'])."</title>";
	$mess[] = "\t</head>";
	$mess[] = "\t<body>";
	$mess[] = "\t\t<p>";
	
	$mess[] = "\t\t\tBeskrivning: ".htmlentities($adata['Beskrivning'])."<br />";
	$mess[] = "\t\t\tExtra Info: ".htmlentities($adata['Info'])."<br />";
	$mess[] = "\t\t\tInl&auml;mnad av: ".htmlentities($_SESSION['sess_fullname'])."<br />";
	$mess[] = "\t\t\tNamn: ".htmlentities($kdata['Namn'])."<br />";
	$mess[] = "\t\t\tTelefonn: ".htmlentities($kdata['Telefon'])."<br />";
	$mess[] = "\t\t\tMobil: ".htmlentities($kdata['Mobil'])."<br />";
	$mess[] = "\t\t\tE-Post: ".htmlentities($kdata['EPost'])."<br />";
	$mess[] = "\t\t\tPerson Nr: ".htmlentities($kdata['PersonNr'])."<br />";
	$mess[] = "\t\t\tFastighetsbeteckning: ".htmlentities($kdata['Fastighetsbeteckning'])."<br />";
	$mess[] = "\t\t</p>";
	
	$mess[] = "\t\t<p>";
	$mess[] = "\t\t\t*************** Faktura Adress ***************<br />";
	$mess[] = "\t\t\t".htmlentities($kdata['Namn'])."<br />";
	$mess[] = "\t\t\t".htmlentities($kdata['Faktura_Adress'])."<br />";
	$mess[] = "\t\t\t".htmlentities($kdata['Faktura_PostNr'])." ";
	$mess[] = "\t\t\t".htmlentities($kdata['Faktura_PostAdress'])."<br />";
	$mess[] = "\t\t\t".htmlentities($kdata['Faktura_Land'])."<br />";
	$mess[] = "\t\t</p>";

	$mess[] = "\t\t<p>";
	$mess[] = "\t\t\t*************** Bes&ouml;ks Adress ***************<br />";
	$mess[] = "\t\t\t".htmlentities($adata['Besoks_Namn'])."<br />";
	$mess[] = "\t\t\t".htmlentities($adata['Besoks_Adress'])."<br />";
	$mess[] = "\t\t\t".htmlentities($adata['Besoks_PostNr'])." ";
	$mess[] = "\t\t\t".htmlentities($adata['Besoks_PostAdress'])."<br />";
	$mess[] = "\t\t\t".htmlentities($adata['Besoks_Land'])."<br />";
	$mess[] = "\t\t</p>";
	
	$mess[] = "\t\t<p>";

	$tstmt = DB::getInstance()->prepare("SELECT * FROM Timraport WHERE AOrder_Id=:id ORDER BY Datum ASC");
	$tstmt->bindParam(':id', $aorder, PDO::PARAM_INT);
	$tstmt->execute();
	$tq = $tstmt->fetchAll(PDO::FETCH_ASSOC);

	$mess[] = "\t\t\t*************** Timmar ***************<br />";
	$mess[] = "\t\t</p>";
	$mess[] = "\t\t<table border=\"1\" style=\"width:100%\">";

	foreach ($tq as $data)
	{
		$ustmt = DB::getInstance()->prepare("SELECT * FROM users WHERE Id=:id");
		$ustmt->bindParam(':id', $data['User_Id'], PDO::PARAM_INT);
		$ustmt->execute();
		$udata = $ustmt->fetch();

		$mess[] = "\t\t\t<tr>";
		$mess[] = "\t\t\t\t<td>".htmlentities($udata['FullName'])."</td>";
		$mess[] = "\t\t\t\t<td>".htmlentities($data['Datum'])."</td>";
		$mess[] = "\t\t\t\t<td>".htmlentities($data['Tid'])."</td>";
		$mess[] = "\t\t\t\t<td>".htmlentities($data['Beskrivning'])."</td>";
		$mess[] = "\t\t\t</tr>";
	}
	$mess[] = "\t\t</table>";

	$mess[] = "\t\t<p>";
	$mess[] = "\t\t\t*************** Material ***************<br />";
	$mess[] = "\t\t</p>";
	$mess[] = "\t\t<table border=\"1\" style=\"width:100%\">";
	
	$mstmt = DB::getInstance()->prepare("SELECT * From Material WHERE AOrder_Id=:id ORDER BY E_Nummer,Datum ASC");
	$mstmt->bindParam(':id', $aorder, PDO::PARAM_INT);
	$mstmt->execute();
	$mrq = $mstmt->fetchAll(PDO::FETCH_ASSOC);

	foreach ($mrq as $data)
	{
		$ustmt = DB::getInstance()->prepare("SELECT * FROM users WHERE Id=:id");
		$ustmt->bindParam(':id', $data['User_Id'], PDO::PARAM_INT);
		$ustmt->execute();
		$udata = $ustmt->fetch();

		$mess[] = "\t\t\t<tr>";
		$mess[] = "\t\t\t\t<td>".htmlentities($data['E_Nummer'])."</td>";
		$mess[] = "\t\t\t\t<td>".htmlentities($data['Beskrivning'])."</td>";
		$mess[] = "\t\t\t\t<td>".htmlentities($data['Antal'])."</td>";
		$mess[] = "\t\t\t\t<td>".htmlentities($data['Enhet'])."</td>";
		$mess[] = "\t\t\t\t<td>".htmlentities($data['Datum'])."</td>";
		$mess[] = "\t\t\t\t<td>".htmlentities($udata['FullName'])."</td>";
		$mess[] = "\t\t\t</tr>";
	}
	
	$mess[] = "\t\t</table>";
	$mess[] = "\t</body>";
	$mess[] = "</html>";

	$ustmt = DB::getInstance()->prepare("SELECT * FROM users WHERE Id=:id");
	$ustmt->bindParam(':id', $_SESSION['sess_id'], PDO::PARAM_INT);
	$ustmt->execute();
	$udata = $ustmt->fetch();

	$headers   = array();
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-type: text/html; charset=iso-8859-1";
	$headers[] = "From: A-Order <order@stensel.se>";

	mail("order@stensel.se, sten@stensel.se, ".$udata['EPost'], "A-Order ".$aorder, implode("\r\n", $mess), implode("\r\n", $headers));

	$ustmt = DB::getInstance()->prepare("UPDATE AOrder SET Inlamnad=1 WHERE Id=:id");
	$ustmt->bindParam(':id', $aorder, PDO::PARAM_INT);
	$ustmt->execute();

}

?>