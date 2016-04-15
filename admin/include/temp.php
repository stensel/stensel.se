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

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

if (!isset(	$_SESSION['sess_user']) && ($file != "login.php"))
{
	header("Location: login.php");
}

include "../include/conn.php";

init();

?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>Johan Svendsrud EL AB / Stens EL - Administrations sida</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/bootstrap-theme.css">
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function()
			{
				$(window).keydown(function(event)
				{
					if(event.keyCode == 13)
					{
						event.preventDefault();
						return false;
					};
				});
				
				$("#ksearch").on('keydown', function (event)
				{
					 if(event.which === 13)
					 {
						$("#Btn_ksearch").click();
					 };
			   });
				
				$("#Antal").on('keydown', function (event)
				{
					 if(event.which === 13)
					 {
						$("#Btn_Save").click();
					 };
				});
				
				$("#inputUser").on('keydown', function (event)
				{
					if(event.which === 13)
					{
						$("#inputPassword").focus();
					}
				});

				$("#inputPassword").on('keydown', function (event)
				{
					if(event.which === 13)
					{
						$("#login").click();
					}
				});

				$("#tBeskrivning").on('keydown', function (event)
				{
					if(event.which === 13)
					{
						$("#Tid").focus();
					}
				});

				$("#Tid").on('keydown', function (event)
				{
					if(event.which === 13)
					{
						$("#Btn_save_timmar").click();
					}
				});

				$("#E-Nummer").on('keydown', function (event)
				{
					if(event.which === 13)
					{
						$("#Beskrivning").focus();
					}
				});

				$("#Beskrivning").on('keydown', function (event)
				{
					if(event.which === 13)
					{
						$("#Antal").focus();
					}
				});

				$("#Ar").change(function()
				{
					$("#Btn_TimSel").click();
				});
				
				$("#Manad").change(function()
				{
					$("#Btn_TimSel").click();
				});

				$("#Kategori").change(function()
				{
					$("#Btn_Kat").click();
				});
			});
		</script>
	</head>
	
	<body class="body-custom">
		<div class="container-fluid hidden-xs hidden-print">
			<div class="page-header page-header-custom">
					<div class="pull-left logo-txt">
						<h1>Johan Svendsrud EL AB / Stens EL</h1>
						<h3>Administrations sida</h3>
					</div>
					<div class="pull-right logo-img">
						<img src="../images/logo_s.png" alt="Logo">
					</div>
					<div class="clearfix"></div>
			</div>
		</div>

		<nav class="navbar navbar-inverse hidden-print">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div class="collapse navbar-collapse" id="myNavbar">
					<ul class="nav navbar-nav">
					<?php if (isset($localnav)) {?><li><a href="<?php echo $localnav?>"><?php echo $localnavname?></a></li><?php }

	$result = DB::getInstance()->query("SELECT * FROM menu_admin ORDER BY Id ASC");

    /*** loop over the results ***/
    foreach($result as $row)
	{
		if ((intval($_SESSION['userpriv']) & intval($row['Priv'])))
		{
			if ($file == $row['File'])
			{
?>
						<li class="active"><a href="<?php echo $row['File']?>"><?php echo $row['Name']?></a></li>
<?php
			}
			else
			{
?>
						<li><a href="<?php echo $row['File']?>"><?php echo $row['Name']?></a></li>
<?php
			}
		}
	}
?>
					</ul>
					<?php if (isset($_SESSION['sess_user'])) {?><ul class="nav navbar-nav navbar-right">
						<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout (<?php echo $_SESSION['sess_user']?>)</a></li>
					</ul><?php }?>
				</div>
			</div>
		</nav>

		<div class="container-fluid custom-ct">
<?php page();?>
		</div>
	</body>
</html>

