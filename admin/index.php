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

$file="index.php";
include "include/temp.php";

function init()
{
}

function page()
{
?>
			<div class="well cust_well">
				<h3>V&auml;lkommen till Stens EL administrations sida</h3>
				<h3>Instruktioner.</h3>
				Skapa ny kund eller använd en befintlig för att skapa en ny A-order under (Kunder).<br>
				välj sedan A-orderen och klicka på &uml;visa A-order&uml; l&auml;gg sedan in Timmar och Material.<br>
				N&auml;r A-orderen är klar välj A-orderen och klicka på &uml;Inlämmna&uml;<br>
				<br>
			</div>
			<br>
			<div class="well cust_well">
				Business Management V0.9.4<br>
				Copyright (c) 2015-2016 "Morten Svendsen"<br>
				Released under the GPL
			</div>
<?php
}
?>
