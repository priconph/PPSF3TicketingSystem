<?php
viewStickers(); 
//viewStickers();
function viewStickers() {
//any errors would spoil the pdf
error_reporting(~E_NOTICE & ~E_WARNING);
 
//prepare the html
$header = <<<EOT
	<html>
<head>
<style>
div
{
   // margin:20px;
}
table {
    border-collapse: collapse;
}
table td, table th {
    border: 1px solid black;
}       
table tr:first-child th {
    border-top: 0;
}
table tr:last-child td {
    border-bottom: 0;
}
table tr td:first-child,
table tr th:first-child {
    border-left: 0;
}
table tr td:last-child,
table tr th:last-child {
    border-right: 0;
}
body {
	font-size: 8px;
}
</style>
<script>
</script>
</head>
    <body>
		<div style='position:relative;border: 1px solid green;' >sadasd
EOT;
$footer = <<<EOT
 </div>
</body>
</html>
EOT;
$htmlRight = <<<EOT
	<div style='float:left;position:relative;'>
		<table border=1 name='box' class='box' id='box' style='width:45%'>
			<!-- <tr style='display:none;'>
				<td style='width:20%;display:none;'>a</td>
				<td style='width:20%;display:none;'>b</td>
				<td style='width:10%;display:none;'>b</td>
				<td style='width:10%;display:none;'>b</td>
				<td style='width:20%;display:none;'>b</td>
				<td style='width:20%;display:none;'>b</td>
			</tr> -->
			<tr>
				<td colspan=3 style='width:50%;'>Buyer<br><label style='padding'>PRICON MICROELECTRONICS, INC. - (Internal)</label></td>
				<td colspan=3 style='width:50%;'>Vendor<br>PRICON MICROELECTRONICS, INC.</td>
			</tr>
			<tr>
				<td colspan=3 style='width:50%;'>Delivery place name W021<br>Delivery date\t 2015-01-21</td>
				<td colspan=3 style='width:50%;'>Comments for vendor<br>aaa</td>
			</tr>
			<tr>
				<td colspan=3 style='width:50%;'>Delivery Key No.<br>PR1520000008</td>
				<td colspan=3 rowspan=2 style='width:50%;'><barcode code="020388202LOT12345" type="C128A" size="0.4" height="1"/><center>020388202LOT12345</center></td>
			</tr>
			<tr>
				<td colspan=3 style='width:50%;'>Product Code (Buyer)<br>020388202</td>	
			</tr>
			<tr>
				<td colspan=2 style='width:40%;'>Product name<br>aaaaaa</td>
				<td colspan=3 style='width:40%;'>Drawing #<br>aaaaa</td>
				<td colspan=1 style='width:20%;'>Rev #<br>aaaaa</td>
			</tr>
			<tr>
				<td style='width:20%;'>Qty/Total Qty<br>aa</td>
				<td colspan=5 style='width:80%;'>Lot #<br>aaa</td>
			</tr>
			<tr>
				<td colspan=4 style='width:60%;'>Comments for buyer<br>a</td>
				<td style='width:20%;'>Package Count<br>a</td>
				<td style='width:20%;'>Packing Type<br>aa</td>
			</tr>
		</table>
   </div>
    
EOT;
//get the mpdf library
include("../mpdf/mpdf.php");
$mpdf = new mPDF('utf-8','A4','','' , 0 , 0 , 0 , 0 , 0 , 0); 
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list

$mpdf->WriteHTML($header);
$mpdf->WriteHTML($htmlRight);
$mpdf->WriteHTML($htmlRight);
$mpdf->WriteHTML($footer);
//render the pdf on the browser
$mpdf->Output();
}
?>