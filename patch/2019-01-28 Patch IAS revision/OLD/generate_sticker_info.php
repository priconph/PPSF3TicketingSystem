<?php
require_once('./PPSTicketing-OOP.php');
session_start();
$LastUpdate 		= date("Y-m-d H:i s");
$TransactionDate 	= date("Y-m-d");
$Username 			= $_SESSION['username'];
$LotNo	 			= $_POST["cmbLotNumberSATO"];
$OrderQty 			= PPST::getInstance()->computeTotalShipmentQty($_POST["txtPoNumSATO"]);
$query 				= PPST::getInstance()->loadPRInformation($_POST["txtPoNumSATO"]);
$packedby			= $_POST["cmbPackby"];


if($_POST["txtNumOfStickers"] == "" && $_POST["txtPackageCount"] == ""){	//for normal printing
	$check_reprint = $_POST['txt_reprint'];
	$poNum=$_POST["txtPoNum"];
	$DateComment=$_POST["date"];
	$department="department";
	
	$DeliveryPlaceName=$_POST["selDeliveryPlaceName"];
	$DeliveryDate=$_POST["deliveryDate"];
	$PackingType=$_POST["cmbPackingType"];
	$Qty=$_POST["txtMaxQty"];
	$QtyPerBox=$_POST["txtShipQty"];
	$CommentsForBuyer	= ""; 
	normalPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$Qty,$QtyPerBox,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$LastUpdate,$Username,$packedby,$check_reprint);
} 
else if($_POST["txtNumOfStickers"] != "") {	//for dynamic printing
	$poNum=$_POST["txtPoNum"];
	$DateComment=$_POST["date"];
	$department="department";
	
	$DeliveryPlaceName=$_POST["selDeliveryPlaceName"];
	$DeliveryDate=$_POST["deliveryDate"];
	$PackingType=$_POST["cmbPackingType"];
	$NumberOfStickers=$_POST["txtNumOfStickers"];
	$CommentsForBuyer	= ""; 
	dynamicPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$NumberOfStickers,$LastUpdate,$Username,$packedby);
} 
else {		//for customize printing
	$poNum=$_POST["txtPoNum"];
	$DateComment=$_POST["date"];
	$department="department";
	
	$DeliveryPlaceName=$_POST["selDeliveryPlaceName"];
	$DeliveryDate=$_POST["deliveryDate"];
	$PackingType=$_POST["cmbPackingType"];
	$Qty=$_POST["txtQuantityCustomized"];
	$TotalQty=$_POST["txtShipQtyCustomize"];
	$PackageCount=$_POST["txtPackageCount"];
	$CommentsForBuyer	= ""; 
	customizePrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$Qty,$TotalQty,$LotNo,$CommentsForBuyer,$PackingType,$PackageCount,$DateComment,$LastUpdate,$Username,$packedby);
}

function normalPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$Qty,$QtyPerBox,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$LastUpdate,$Username,$packedby)
{	
	if($row = mysqli_fetch_array($query)) {
		$OrderNo 			= 	$row['OrderNo'];
		$ProductCode 		= 	$row['ItemCode'];
		$BarCode			=	$ProductCode . "LOT" . $LotNo;
		$ProductName 		= 	$row['ItemName'];
		$DrawingNo 			= 	$row['DrawingNo'];
		$RevNo 				= 	$row['Rev'];		
		$BuyerChecker 		= 	strtoupper($OrderNo[0].$OrderNo[1]);	//Check the OrderNo first 2 index. 
		if($BuyerChecker == "PR" || $BuyerChecker == "AD" || $BuyerChecker == "SM") {
			$Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
			$newfile = './printjob/scan/internal.dd';
		} else if($BuyerChecker == "PP"){
			$Buyer = "PRICON MICROELECTRONICS INC. - (TOKAI)";
			$newfile = './printjob/scan/internal.dd';
		}else if($BuyerChecker == "SP"){
			$Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
			$newfile = './printjob/scan/internal.dd';
		}else if($BuyerChecker == "RE" && strpos($OrderNo,'PP') !== false){
			$Buyer = "PRICON MICROELECTRONICS INC. - (TOKAI)";
			$newfile = './printjob/scan/internal.dd';
		}else {
			$Buyer = "YAMAICHI ELECTRONICS CO., LTD";
			$newfile = './printjob/scan/external.dd';
			//$newfile = './printjob/scan/internal.dd';
		}
		$Vendor 			= 	"PRICON MICROELECTRONICS INC.";
		$NumberOfStickers 	= 	ceil($QtyPerBox/$Qty);
		
		$DeliveryKeyNo		=	$OrderNo;
		//VALIDATE VALUES HERE
		if($OrderQty-$Qty < 0){
			validate(2,$OrderQty);
		}else if($Qty == "" || $Qty == "0" || $QtyPerBox == "" || $QtyPerBox == "0"){
			validate(3,"Shipment Quantity and Quantity per box cannot be empty!");
		}
		else{
				$check = PPST::getInstance()->checkTicketingTransaction($TransactionDate,$OrderNo,$LotNo,$OrderQty,$QtyPerBox,$LastUpdate,$Username);
				if($check != "OK") {	//the total shipment quantity is greater than actual quantity
					validate(4,$check);
				} else {
					//save the transaction to the database and ready for print na :)
					ini_set('display_errors', 1); 
					error_reporting(E_ALL);

					$filename = "./printjob/data/data.txt";
					if(!file_exists($filename)){
						echo "ERROR: the data text file is missing in the printjob/data/ directory";
					} else{
						echo "DONE PRINTING";
					}
					echo "<br>";

					$myfile = fopen($filename, "w") or die("Unable to open file!");
					$tempQtyPerBox = $QtyPerBox;
					$tempQty = 0;
					$data = "";
					$data  = "Buyer;Vendor;DeliverPlaceName;DeliveryDate;DeliveryKeyNo;CommentsForVendor;ProductCode;BarCode;ProductName;DrawingNo;RevNo;Qty;LotNo;CommentsForBuyer;PackageCount;PackingType;packedby;\n";
					$save_trigger = $NumberOfStickers - 1;
					$qty_all = 0;
					
					for($i = 0; $i < $NumberOfStickers; $i++) {
						$TotalQty = $tempQtyPerBox - $tempQty;
						if($Qty > $TotalQty) {
							//echo "[".$Qty = $tempQtyPerBox - $tempQty."]<br>";
							$Qty = $tempQtyPerBox - $tempQty;
						} else {
							$tempQty += $Qty;
						}
						//echo $Qty."<br>";
						$OnHand					= $OrderQty -	$Qty;
						$QtyDisplay 			= $Qty ."/".$QtyPerBox;
						$PackageCountDisplay 	= ($i + 1)."/".$NumberOfStickers;
						
						//QTY OF ALL TICKET
						$qty_all += $Qty;
						
						if($save_trigger == $i){
							//$result = PPST::getInstance()->saveTicketingTransaction($TransactionDate,$OrderNo,$LotNo,$OrderQty,$Qty,$OnHand,$DateComment,$LastUpdate,$Username);
							$result = PPST::getInstance()->saveTicketingTransaction($TransactionDate,$OrderNo,$LotNo,$OrderQty,$qty_all,$OnHand,$DateComment,$LastUpdate,$Username);
						}
						/* if($result != 'OK') {
							validate(4,$result);
							$data = "";
						} else { */
							$data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCountDisplay.";".$PackingType.";".$packedby.";\n";;
						/* } */
						
					}
					if($data != "") {
						fwrite($myfile, $data);
						fclose($myfile);

						$file = './printjob/startprint.dd';
						
						if (!copy($file, $newfile)) {
							echo "ERROR: the .dd trigger is missing from the printjob folder";
						}
					}
				}
				//ready to print
				/*ini_set('display_errors', 1); 
				error_reporting(E_ALL);

				$filename = "./printjob/data/data.txt";
				if(!file_exists($filename)){
					echo "ERROR: the data text file is missing in the printjob/data/ directory";
				} else{
					echo "DONE PRINTING";
				}
				echo "<br>";

				$myfile = fopen($filename, "w") or die("Unable to open file!");
				$tempQtyPerBox = $QtyPerBox;
				$tempQty = 0;
				$data = "";
				$data  = "Buyer;Vendor;DeliverPlaceName;DeliveryDate;DeliveryKeyNo;CommentsForVendor;ProductCode;BarCode;ProductName;DrawingNo;RevNo;Qty;LotNo;CommentsForBuyer;PackageCount;PackingType;\n";
				for($i = 0; $i < $NumberOfStickers; $i++) {					
					$TotalQty = $tempQtyPerBox - $tempQty;
					if($Qty > $TotalQty) {
						//echo "[".$Qty = $tempQtyPerBox - $tempQty."]<br>";
						$Qty = $tempQtyPerBox - $tempQty;
					} else {
						$tempQty += $Qty;
					}
					$QtyDisplay 			= $Qty ."/".$QtyPerBox;
					$PackageCountDisplay 	= ($i + 1)."/".$NumberOfStickers;
					$data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCountDisplay.";".$PackingType.";\n";
					//$data .= $Buyer.";".$Vendor.";".$DeliverPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCountDisplay.";".$PackingType.";\n";
				}
				
				//$data .= $Buyer.";".$Vendor.";".$DeliverPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$Qty.";".$LotNo.";".$CommentsForBuyer.";".$PackageCount.";".$PackingType.";\n";
				fwrite($myfile, $data);
				fclose($myfile);

				$file = './printjob/startprint.dd';
				$newfile = './printjob/scan/startprint.dd';
				if (!copy($file, $newfile)) {
					echo "ERROR: the .dd trigger is missing from the printjob folder";
				}
			*/
		}
		
	} else {
		validate(1,$poNum);
	}	
}
function dynamicPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$StickersValue,$LastUpdate,$Username,$packedby)
{	
	if($row = mysqli_fetch_array($query)) {
		$OrderNo 			= 	$row['OrderNo'];
		$ProductCode 		= 	$row['ItemCode'];
		$BarCode			=	$ProductCode . "LOT" . $LotNo;
		$ProductName 		= 	$row['ItemName'];
		$DrawingNo 			= 	$row['DrawingNo'];
		$RevNo 				= 	$row['Rev'];		
		$BuyerChecker 		= 	strtoupper($OrderNo[0].$OrderNo[1]);	//Check the OrderNo first 2 index. 
		if($BuyerChecker == "PR" || $BuyerChecker == "AD" || $BuyerChecker == "SM") {	
			$Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
			$newfile = './printjob/scan/internal.dd';
		} else if($BuyerChecker == "PP"){
			$Buyer = "PRICON MICROELECTRONICS INC. - (TOKAI)";
			$newfile = './printjob/scan/internal.dd';
		}else if($BuyerChecker == "SP"){
			$Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
			$newfile = './printjob/scan/internal.dd';
		}else {
			$Buyer = "YAMAICHI ELECTRONICS CO., LTD";
			$newfile = './printjob/scan/external.dd';
		}
		$Vendor 			= 	"PRICON MICROELECTRONICS INC.";
		$Stickers			=	explode(',',$StickersValue);
		$NumberOfStickers 	= 	count($Stickers);
		$DeliveryKeyNo		=	$OrderNo;
		for($x = 0; $x < $NumberOfStickers; $x++) {
			$QtyPerBox += $Stickers[$x];
		}
		
		//echo "TOTAL".$QtyPerBox;
		//VALIDATE VALUES HERE
		if($NumberOfStickers == 0) {
			validate(3,'Invalid sticker quantity!');
		} else {
			$check = PPST::getInstance()->checkTicketingTransaction($TransactionDate,$OrderNo,$LotNo,$OrderQty,$QtyPerBox,$LastUpdate,$Username);
			if($check != "OK") {	//the total shipment quantity is greater than actual quantity
				validate(4,$check);
			} else {
				//ready to print
				ini_set('display_errors', 1); 
				error_reporting(E_ALL);

				$filename = "./printjob/data/data.txt";
				if(!file_exists($filename)){
					echo "ERROR: the data text file is missing in the printjob/data/ directory";
				}else{
					echo "DONE PRINTING";
				}
				echo "<br>";

				$myfile = fopen($filename, "w") or die("Unable to open file!");
				$tempQtyPerBox = $QtyPerBox;
				$tempQty = 0;
				$data = "";
				$data  = "Buyer;Vendor;DeliverPlaceName;DeliveryDate;DeliveryKeyNo;CommentsForVendor;ProductCode;BarCode;ProductName;DrawingNo;RevNo;Qty;LotNo;CommentsForBuyer;PackageCount;PackingType;packedby;\n";
				
				for($i = 0; $i < $NumberOfStickers; $i++) {		
					$QtyDisplay 			= 	$Stickers[$i] ."/".$QtyPerBox;
					$PackageCountDisplay 	= 	($i + 1)."/".$NumberOfStickers;
					$OnHand				=	$OrderQty -	$Stickers[$i];
					$result = PPST::getInstance()->saveTicketingTransaction($TransactionDate,$OrderNo,$LotNo,$OrderQty,$Stickers[$i],$OnHand,$DateComment,$LastUpdate,$Username);
					$data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCountDisplay.";".$PackingType.";".$packedby.";\n";
					//echo $data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCountDisplay.";".$PackingType.";\n<br>";
				}
				
				//$data .= $Buyer.";".$Vendor.";".$DeliverPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$Qty.";".$LotNo.";".$CommentsForBuyer.";".$PackageCount.";".$PackingType.";\n";
				fwrite($myfile, $data);
				fclose($myfile);

				$file = './printjob/startprint.dd';
				$newfile = './printjob/scan/startprint.dd';
				if (!copy($file, $newfile)) {
					echo "ERROR: the .dd trigger is missing from the printjob folder";
				}
			}
		}
	} else {
		validate(1,$poNum);
	}	
}
function customizePrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$Qty,$TotalQty,$LotNo,$CommentsForBuyer,$PackingType,$PackageCount,$DateComment,$LastUpdate,$Username,$packedby)
{	
	if($row = mysqli_fetch_array($query)) {
		$OrderNo 			= 	$row['OrderNo'];
		$ProductCode 		= 	$row['ItemCode'];
		$BarCode			=	$ProductCode . "LOT" . $LotNo;
		$ProductName 		= 	$row['ItemName'];
		$DrawingNo 			= 	$row['DrawingNo'];
		$RevNo 				= 	$row['Rev'];		
		$BuyerChecker 		= 	strtoupper($OrderNo[0].$OrderNo[1]);	//Check the OrderNo first 2 index. 
		if($BuyerChecker == "PR" || $BuyerChecker == "AD" || $BuyerChecker == "SM") {	
			$Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
			$newfile = './printjob/scan/internal.dd';
		} else if($BuyerChecker == "SP"){
			$Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
			$newfile = './printjob/scan/internal.dd';
		}else if($BuyerChecker == "PP"){
			$Buyer = "PRICON MICROELECTRONICS INC. - (TOKAI)";
			$newfile = './printjob/scan/internal.dd';
		}else {
			$Buyer = "YAMAICHI ELECTRONICS CO., LTD";
			$newfile = './printjob/scan/external.dd';
		}
		$Vendor 			= 	"PRICON MICROELECTRONICS INC.";
		$Stickers			=	explode(',',$StickersValue);
		$NumberOfStickers 	= 	count($Stickers);
		$OnHand				=	$OrderQty -	$Qty;
		$DeliveryKeyNo		=	$OrderNo;
		//VALIDATE VALUES HERE
		if($NumberOfStickers == 0) {
			validate(3,'Invalid sticker quantity!');
		} else {
			//ready to print
			ini_set('display_errors', 1); 
			error_reporting(E_ALL);

			$filename = "./printjob/data/data.txt";
			if(!file_exists($filename)){
				echo "ERROR: the data text file is missing in the printjob/data/ directory";
			}else{
				echo "DONE PRINTING";
			}
			echo "<br>";

			$myfile = fopen($filename, "w") or die("Unable to open file!");;
			$data = "";
			$data  = "Buyer;Vendor;DeliverPlaceName;DeliveryDate;DeliveryKeyNo;CommentsForVendor;ProductCode;BarCode;ProductName;DrawingNo;RevNo;Qty;LotNo;CommentsForBuyer;PackageCount;PackingType;packedby;\n";
				
			$QtyDisplay 			= 	$Qty ."/".$TotalQty;
			// $data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCount.";".$PackingType.";".$packedby.";\n";
			
			$data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCount.";".$PackingType.";".$packedby.";\n";;
			
			//$data .= $Buyer.";".$Vendor.";".$DeliverPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$Qty.";".$LotNo.";".$CommentsForBuyer.";".$PackageCount.";".$PackingType.";\n";
			fwrite($myfile, $data);
			fclose($myfile);

			$file = './printjob/startprint.dd';
			//$newfile = './printjob/scan/startprint.dd';
			if (!copy($file, $newfile)) {
				echo "ERROR: the .dd trigger is missing from the printjob folder";
			}
		}
	} else {
		validate(1,$poNum);
	}	
}
function validate($Category,$passedValue)
{
	echo '
	<html>
		<head>
			<style type="text/css">
				body{
					//background-color:#ED3939;
					background-color:#3B5998;
					color:#ffffff;
					font-family:"Franklin Gothic Demi";
				}
				#wrapper{
					margin: 0 auto;
					width: 800px;
					border-color:#000000;
				}
			</style>
		</head>
		<body>
			<div id="wrapper">
				';
				switch($Category){
					case 	1	:	echo '<p align="center">The Product with product number '.$passedValue.' could not be found.</p>'; break;
					case 	2	:	echo '<p align="center">ERROR: The Original Quantity is = '.$passedValue.'</p>'; break;
					case 	3	:	echo '<p align="center">'.$passedValue.'</p>'; break;
					case 	4	:	echo '<p align="center">'.$passedValue.'</p>'; break;
				}
			echo '</div>
		</body>
	</html>';
}


?>