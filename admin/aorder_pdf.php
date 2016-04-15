<?php
session_start(); // Alltid Överst på sidan 
include "include/default.php";

check_login();

$con = db_start();

if (isset($_POST['show']))
{
	if (isset($_POST['aorder']))
	{
		$aorder = $_POST['aorder'];

		$sqla = "SELECT * FROM AOrder WHERE Id=".$aorder;
		$qa = mysql_query($sqla);
		$adata = mysql_fetch_array($qa);
		
		$ksql = "SELECT * FROM Kunder WHERE Id=".$adata['Kund_Id'];
		$kq = mysql_query($ksql);
		$kdata = mysql_fetch_array($kq);
		
		try
		{
			$p = new PDFlib();

			/*  open new PDF file; insert a file name to create the PDF on disk */
			if ($p->begin_document("", "") == 0) {
				die("Error: " . $p->get_errmsg());
			}

			$p->set_info("Creator", "aorder_pdf.php");
			$p->set_info("Author", $_SESSION['sess_fullname']);
			$p->set_info("Title", "A-Order: ".$adata['Id']);

			$p->begin_page_ext(595, 842, "");

			$font = $p->load_font("Times New Roman", "winansi", "");

			$p->setfont($font, 24.0);
			$p->set_text_pos(76, 48);
			$p->show("A-Order Nr: ".$adata['Id']);
			$p->setfont($font, 12.0);
			$p->set_text_pos(76, 79);
			$p->lineto(241, 79);
			$p->lineto(241, 65);
			$p->lineto(76, 65);
			$p->lineto(76, 79);
			$p->set_text_pos(317, 79);
			$p->lineto(241, 79);
			$p->lineto(241, 112);
			$p->lineto(76, 112);
			$p->lineto(317, 79);
			
			
			$p->set_text_pos(82, 82);
			$p->show("Inlämnad av: ".$_SESSION['sess_fullname']);


			$p->end_page_ext("");

			$p->end_document("");

			$buf = $p->get_buffer();
			$len = mb_strlen($buf, 'ASCII');

			header("Content-type: application/pdf");
			header("Content-Length: $len");
			header("Content-Disposition: inline; filename=hello.pdf");
			print $buf;
		}
		catch (PDFlibException $e)
		{
			die("PDFlib exception occurred in hello sample:\n" .
			"[" . $e->get_errnum() . "] " . $e->get_apiname() . ": " .
			$e->get_errmsg() . "\n");
		}
		catch (Exception $e)
		{
			die($e);
		}
		/*
		$mes = $mes."Beskrivning: ".$adata['Beskrivning']."\n";
		$mes = $mes."Extra Info: ".$adata['Info']."\n";
		$mes = $mes."Namn: ".$kdata['Namn']."\n";
		$mes = $mes."Telefonn: ".$kdata['Telefon']."\nMobil: ".$kdata['Mobil']."\nE-Post: ".$kdata['EPost']."\n";
		$mes = $mes."Person Nr: ".$kdata['PersonNr']."\nFastighetsbeteckning: ".$kdata['Fastighetsbeteckning']."\n\n";

		$mes = $mes."*************** Faktura Adress ***************\n";
		$mes = $mes.$kdata['Namn']."\n".$kdata['Faktura_Adress']."\n".$kdata['Faktura_PostNr']." ".$kdata['Faktura_PostAdress']."\n".$kdata['Faktura_Land']."\n\n";

		$mes = $mes."*************** Besöks Adress ***************\n";
		$mes = $mes.$adata['Besoks_Namn']."\n".$adata['Besoks_Adress']."\n".$adata['Besoks_PostNr']." ".$adata['Besoks_PostAdress']."\n".$adata['Besoks_Land']."\n\n";
		
		$sqlt = "SELECT * FROM Timraport WHERE AOrder_Id=".db_escape($aorder)." ORDER BY Datum ASC";
		$tq = mysql_query($sqlt);

		$mes = $mes."*************** Timmar ***************\n";

		while($data = mysql_fetch_array($tq))
		{
			$usql = "SELECT * FROM users WHERE Id=".$data['User_Id'];
			$uq = mysql_query($usql);
			$udata = mysql_fetch_array($uq);
			
			$mes = $mes.str_pad($udata['FullName'], 20)." - ".$data['Datum']." - ".str_pad($data['Tid'], 4)." - ".$data['Beskrivning']."\n";
		}

		$mes = $mes."\n\n*************** Material ***************\n";
		
		$sqlar = "SELECT * From Artikel WHERE AOrder_Id=".db_escape($aorder)." ORDER BY E_Nummer,Datum ASC";
		$arq = mysql_query($sqlar);

		while($data = mysql_fetch_array($arq))
		{
			$usql = "SELECT * FROM users WHERE Id=".$data['User_Id'];
			$uq = mysql_query($usql);
			$udata = mysql_fetch_array($uq);

			$mes = $mes.str_pad(fix_enum($data['E_Nummer']), 8)." - ".str_pad($data['Beskrivning'], 20)." - ".str_pad($data['Antal'], 5).$data['Enhet']." - ".$data['Datum']." - ".$udata['FullName']."\n";
		}
		
		$usql = "SELECT * FROM users WHERE Id=".$_SESSION['sess_id'];
		$uq = mysql_query($usql);
		$udata = mysql_fetch_array($uq);
		
		if ($_SESSION['userpriv'] & 0x02)
		{
			mail($udata['EPost'], "Test A-Order ".$aorder, $mes);
		}
		else
		{
			mail("order@stensel.se,".$udata['EPost'], "A-Order ".$aorder, $mes);

			$sql = "UPDATE AOrder SET Inlamnad=1 WHERE Id=".db_escape($aorder);
			mysql_query($sql);
		}
		*/
	}
}

mysql_close($con);
?>
