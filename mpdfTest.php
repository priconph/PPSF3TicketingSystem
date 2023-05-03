<?php
require_once('../mpdf/mpdf.php');

$mpdf = new mPDF('c', 'A4-L');
$header 	= '
<html>
<head>
	<style>
		table 
		{
			border-collapse: collapse;
		}
		table td, table th 
		{
			border: 1px solid black;
			padding: 5px;
		}       
		table tr:first-child th 
		{
			border-top: 0;
		}
		table tr:last-child td 
		{
			border-bottom: 0;
		}
		table tr td:first-child,
		table tr th:first-child 
		{
			border-left: 0;
		}
		table tr td:last-child,
		table tr th:last-child 
		{
			border-right: 0;
		}
		body 
		{
			font-size: 10px;
		}
		#title {
			font-size: 8px;
		}
		#value {
			font-size: 10px;
			text-indent: -25px; 
		}
	</style>
</head>
<body>
	<div style="position:relative;">
	';
$content 	= "
		<div style='position:relative;float:left;border:1px solid black;margin-bottom:50px;padding:10px;height:15%;width:40%;'>
			<table border=1 name='box' class='box' id='box' style='width:100%'>
				<tr>
					<td colspan=4 style='width:55%;'><div id='title'>Buyer</div><div id='value' style=''>PRICON MICROELECTRONICS, INC. - (Internal)</div></td>
					<td colspan=2 style='width:45%;'><div id='title'>Vendor</div><div id='value'>PRICON MICROELECTRONICS, INC.</div></td>
				</tr>
				<tr>
					<td colspan=4 style='width:55%;'><div id='title'>Delivery place name <font size='12'>W102</font></div><div id='title'>Delivery date 2015-01-21</div></td>
					<td colspan=2 style='width:40%;'><div id='title'>Comments for vendor</div><div id='value'>aaa</div></td>
				</tr>
				<tr>
					<td colspan=4 style='width:55%;'><div id='title'>Delivery Key No.</div><div id='value'>PR1520000008</div></td>
					<td colspan=2 rowspan=2 style='width:45%;'><barcode code='020388202LOT12345' type='C128A' size='0.3' height='1'/><center>020388202LOT12345</center></td>
				</tr>
				<tr>
					<td colspan=4 style='width:55%;'><div id='title'>Product Code (Buyer)</div><div id='value'>020388202</div></td>	
				</tr>
				<tr>
					<td colspan=2 style='width:50%;'><div id='title'>Product name</div><div id='value'>aaaaaa</div></td>
					<td colspan=3 style='width:30%;'><div id='title'>Drawing #</div><div id='value'>aaaaa</div></td>
					<td colspan=1 style='width:20%;'><div id='title'>Rev #</div><div id='value'>aaaaa</div></td>
				</tr>
				<tr>
					<td style='width:30%;'><div id='title'>Qty/Total Qty</div><div id='value'>aa</div></td>
					<td colspan=5 style='width:70%;'><div id='title'>Lot #</div><div id='value'>aaa</div></td>
				</tr>
				<tr>
					<td colspan=4 style='width:60%;'><div id='title'>Comments for buyer</div><div id='value'>a</div></td>
					<td style='width:20%;'><div id='title'>Package Count</div><div id='value'>a</div></td>
					<td style='width:20%;'><div id='title'>Packing Type</div><div id='value'>aa</div></td>
				</tr>
			</table>
		</div>";	
$footer 	= '
	</div>
</body>
</html>';

$ctr = 0;
$html = $header;
while($ctr < 8){
	$html .= $content;
	$ctr++;
}
$html .= $footer;

$mpdf->WriteHTML($html);
$mpdf->Output();
exit;
?>