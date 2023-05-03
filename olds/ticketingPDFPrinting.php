
<?php 
require_once('fpdf.php'); 
require_once('./PPSTicketing-OOP.php');
error_reporting(E_ALL|E_STRICT);
define('FPDF_FONTPATH','font/');
session_start();
$LastUpdate = date("Y-m-d H-i-s");
$TransactionDate = date("Y-m-d");
$Username = $_SESSION['username'];
$lotNumber = $_POST["cmbLotNumber"];
$orderQty = PPST::getInstance()->computeTotalShipmentQty($lotNumber);
$query = PPST::getInstance()->loadPRInformation($_POST["txtPoNum"]);

if($_POST["txtNumOfStickers"] == ""){
	$poNum=$_POST["txtPoNum"];
	$qtyPerBox=$_POST["txtMaxQty"];
	$dateComment=$_POST["date"];
	$deliveryPlaceName=$_POST["selDeliveryPlaceName"];
	$shipNum=$_POST["txtShipQty"];
	$packingType=$_POST["cmbPackingType"];
	$deliveryDate=$_POST["deliveryDate"];
	$department="department";
	normalPrinting($query,$poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$deliveryDate,$shipNum,$department,$TransactionDate,$LastUpdate,$Username,$orderQty,$lotNumber,$packingType);
} 
else{
	//Variable post from input.php
	$dynamicQty = array(); //array declaration to explode values from qty perbox textbox
	$poNum=$_POST["txtPoNum"];
	$qtyPerBox=$_POST["txtMaxQty"];
	$numOfStickers2=$_POST["txtNumOfStickers"];//specified number of sticker per box
	$dynamicQty=$_POST["txtQtyPerBox"];//Quantity per box 2
	$dynamicQty=explode(",", $dynamicQty);
	$dateComment=$_POST["date"];
	$deliveryPlaceName=$_POST["selDeliveryPlaceName"];
	$shipNum=$_POST["txtShipQty"];
	//$department=$_POST["department"];
	otherPrinting($query,$poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$TransactionDate,$LastUpdate,$Username,$numOfStickers2,$dynamicQty);
}

function normalPrinting($query,$poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$deliveryDate,$shipNum,$department,$TransactionDate,$LastUpdate,$Username,$orderQty,$lotNumber,$packingType){
	class Table extends FPDF{
		var $Buyer;
		var $Vendor;
		var $dateComment;
		var $DeliveryPlaceName;
		var $DeliveryDate;
		var $DeliveryKeyNum;
		var $ProductCodeB;
		var $BarcodeValue;
		var $ProductName;
		var $DrawingNumber;
		var $RevNo;
		var $LotNo;
		var $Comments;
		var $numOfStickers=4;
		var $PackingType;
		var $Company="PRICON MICROELECTRONICS INC.";
		var $Qty;
		var $TotalQty;
		var $QtyPerBox;
		

		//function setFields($buyer,$vendor,$date,$dpn,$dkn,$procodeA,$procodeB,$proname,$qty,$qtypb,$unit,$comments)
		function setFields($buyer,$vendor,$dateComment,$dpn,$dd,$dkn,$productCode,$barcode,$prodName,$drawNum,$revNo,$lotNo,$comments,$packingType,$qty,$qtypb)
		{
			/*$this->Buyer=$buyer;
			$this->Vendor=$vendor;
			$this->dateComment=$date;
			$this->DeliveryPlaceName=$dpn;
			$this->DeliveryKeyNum=$dkn;
			$this->ProductCodeA=$procodeA;
			$this->ProductCodeB=$procodeB;
			$this->ProductName=$proname;
			$this->Qty=$qty;
			$this->TotalQty=$qty;
			$this->QtyPerBox=$qtypb;
			$this->Unit=$unit;
			$this->Comments=$comments;
			$this->numOfStickers=ceil($qty/$qtypb);*/
	
			$this->Buyer=$buyer;
			$this->Vendor=$vendor;
			$this->dateComment=$dateComment;
			$this->DeliveryPlaceName=$dpn;
			$this->DeliveryDate=$dd;
			$this->DeliveryKeyNum=$dkn;
			$this->ProductCodeB=$productCode;
			$this->BarcodeValue=$barcode;
			$this->ProductName=$prodName;
			$this->DrawingNumber=$drawNum;
			$this->RevNo=$revNo;
			$this->LotNo=$lotNo;
			$this->Comments=$comments;
			$this->numOfStickers=ceil($qty/$qtypb);
			$this->PackingType=$packingType;
			$this->Qty=$qty;
			$this->TotalQty=$qty;
			$this->QtyPerBox=$qtypb;
		}
		
		function mkSticker()
		{
			$converter=1.069767441860465;
			$orgX=-190*$converter;
			$orgY=-270*$converter;
			$orgX=1.5*$converter;
			$orgY=2*$converter;
			$x=$orgX;
			$y=$orgY;
			$ctr2=-1;
			$ctr=0;
			for($i=0;$i<$this->numOfStickers;$i++){
				if($i%2==0)
				{
					$ctr2++;
					$ctr=0;
					$x=$orgX;
					$y=$orgY;
					$y+=(($converter*55)*$ctr2);
				}
				else
				{
					$ctr=1;
				}
				$this->generateSticker($x+(($converter*99)*$ctr),$y,$i+1);
				//$y+=59*$converter;
				$this->Qty-=$this->QtyPerBox;
				if($i+1==10&&$i+1!=$this->numOfStickers)
				{
					$ctr2=-1;
					$this->AddPage();
				}
				
				
			}
		}
		
		function Footer() { 
			
		} 

		function generateSticker($x2,$y2,$num)
		{
			$x=$x2;
			$y=$y2;
			$nums=$num;
			$width=0;
			$height=0;

			$space=3;

			$converter=1.069767441860465;

			//Buyer Cell
			$this->SetY($y);
			$this->SetX($x);
			$width=56.5*$converter;
			$height=7.5*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetFont('Arial','',6);
			$this->SetY($y-($converter));
			$this->SetX($x-.5);
			$this->Cell(11,6,"Buyer");

			if($this->Buyer != "YAMAICHI ELECTRONICS CO., LTD"){
				$this->SetFont('Times','',7.2);
			}else{
				$this->SetFont('Times','',9);
			}
			$this->SetY($y+$converter*2);
			$this->SetX($x+($converter*4));
			$this->Cell(11,6,$this->Buyer);

			//Vendor
			$origX=$x;
			$origY=$y;
			$origHeight=$height;
			$x+=$width;
			$this->SetY($y);
			$this->SetX($x);
			$width=38*$converter;
			$height=31.5*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetFont('Arial','',6);
			$this->SetY($y-$converter);
			$this->SetX($x-.5);
			$this->Cell(11,6,"Vendor");

			$this->SetFont('Times','',7);
			$this->SetY($y+(2*$converter));
			$this->SetX($x-.5);
			$this->Cell(11,6,$this->Vendor);

			$this->SetFont('Arial','',6);
			$this->SetY($y-1.6+(12*$converter));
			$this->SetX($x-.5);
			$this->Cell(11,6,"Comments for vendor");
			//$this->Cell(7,35,"Made in the Philippines");
			
			$this->SetFont('Times','',9);
			$this->SetY($y+(15*$converter));
			$this->SetX($x);
			$this->Cell(11,6,$this->dateComment);

			$this->SetFont('Arial','',6);
			$x=$origX;
			$y+=$origHeight;

			//Delivery place name
			$this->SetY($y);
			$this->SetX($x);
			$width=56.5*$converter;
			$height=7.5*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-($converter));
			$this->SetX($x-.5);
			$this->Cell(11,6,"Delivery place name");

			$this->SetFont('Times','',7);
			$this->SetY($y-($converter));
			$this->SetX($x+(28*$converter));
			$this->Cell(11,6,$this->DeliveryPlaceName);
			
			$this->SetY($y-1.6+(4*$converter));
			$this->SetX($x-.5);
			$this->Cell(11,6,"Delivery date");
			
			$this->SetFont('Times','',7);
			$this->SetY($y-1.6+(4*$converter));
			$this->SetX($x+(28*$converter));
			$this->Cell(11,6,$this->DeliveryDate);

			$this->SetFont('Arial','',6);
			$y+=$height;

			//Delivery Key No.
			$this->SetY($y);
			$this->SetX($x);
			$width=56.5*$converter;
			$height=7.5*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.8);
			$this->SetX($x-.5);
			$this->Cell(11,6,"Delivery Key No.");

			$this->SetFont('Times','',9);
			$this->SetY($y+(2));
			$this->SetX($x+(5*$converter));
			$this->Cell(11,6,$this->DeliveryKeyNum);

			$this->SetFont('Arial','',6);
			$y+=$height;

			//Product code Buyer
			$this->SetY($y);
			$this->SetX($x);
			$width=56.5*$converter;
			$height=9*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.5);
			$this->SetX($x-.5);
			$this->Cell(11,6,"Product Code (Buyer)");

			$this->SetFont('Times','',9);
			$this->SetY($y+2.5);
			$this->SetX($x+(1*$converter));
			$this->Cell(11,6,$this->ProductCodeB);
			
			$this->AddFont('Bar-Code-39','','code_39.php');
			$this->SetFont('Bar-Code-39','',7);
			$this->SetY($y-1);
			$this->SetX($x+(57*$converter));
			$this->Cell(0,0,$this->BarcodeValue);
			
			$this->SetFont('Times','',8);
			$this->SetY($y+(1.5*$converter));
			$this->SetX($x+(57*$converter));
			$this->Cell(11,6,$this->BarcodeValue);

			$this->SetFont('Arial','',6);
			$y+=$height;

			//Product name
			$this->SetY($y);
			$this->SetX($x);
			$width=50*$converter;
			$height=7*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.8);
			$this->SetX($x-.5);
			$this->Cell(11,6,"Product name");
			
			$this->SetFont('Times','',8);
			$this->SetY($y+(1.5*$converter));
			$this->SetX($x+(5*$converter));
			$this->Cell(11,6,$this->ProductName);

			$this->SetFont('Arial','',6);

			//Drawing #
			$origX=$x;
			$origY=$y;
			$x+=$width;
			$this->SetY($y);
			$this->SetX($x);
			$width=24.5*$converter;
			$height=7*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.8);
			$this->SetX($x-.3);
			$this->Cell(11,6,"Drawing #");

			$this->SetFont('Times','',9);
			$this->SetY($y+(1.5*$converter));
			$this->SetX($x+(.3*$converter));
			$this->Cell(11,6, $this->DrawingNumber);

			$this->SetFont('Arial','',6);
			
			//Rev #
			$origY=$y;
			$x+=$width;
			$this->SetY($y);
			$this->SetX($x);
			$width=20*$converter;
			$height=7*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.8);
			$this->SetX($x-.3);
			$this->Cell(11,6,"Rev #");

			$this->SetFont('Times','',9);
			$this->SetY($y+(1.5*$converter));
			$this->SetX($x+(.3*$converter));
			$this->Cell(11,6,$this->RevNo);

			$this->SetFont('Arial','',6);
			$x=$origX;
			$y+=$height;
		
			//QTY
			$this->SetY($y);
			$this->SetX($x);
			$width=50*$converter;
			$height=6.5*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.8);
			$this->SetX($x-.5);
			$this->Cell(11,6,"QTY/ Total QTY");
			
			$this->SetFont('Times','',9);
			$this->SetY($y+(1.5*$converter));
			$this->SetX($x+(5*$converter));
			$this->Cell(11,6,($this->Qty>=$this->QtyPerBox ? $this->QtyPerBox: $this->Qty)."/".$this->TotalQty);

			$this->SetFont('Arial','',6);

			//Lot #
			$origX=$x;
			$origY=$y;
			$x+=$width;
			$this->SetY($y);
			$this->SetX($x);
			$width=44.5*$converter;
			$height=6.5*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.8);
			$this->SetX($x-.5);
			$this->Cell(11,6,"Lot #");

			$this->SetFont('Times','',9);
			$this->SetY($y+(1.5*$converter));
			$this->SetX($x+(.3*$converter));
			$this->Cell(11,6,$this->LotNo);

			$this->SetFont('Arial','',6);
			$x=$origX;
			$y+=$height;

			//comments
			$this->SetY($y);
			$this->SetX($x);
			$width=50*$converter;
			$height=8*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-1.6);
			$this->SetX($x-.5);
			$this->Cell(11,6,"Comments for buyer");
			
			$this->SetFont('Times','',9);
			$this->SetY($y+$converter);
			$this->SetX($x+(15*$converter));
			$this->Cell(11,6,$this->Comments);

			$this->SetFont('Arial','',6);

			//Package Count
			$origX=$x;
			$origY=$y;
			$x+=$width;
			$this->SetY($y);
			$this->SetX($x);
			$width=24.5*$converter;
			$height=8*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.8);
			$this->SetX($x-.3);
			$this->Cell(11,6,"Package Count");

			$this->SetFont('Times','',9);
			$this->SetY($y+(1.5*$converter));
			$this->SetX($x+(.3*$converter));
			$this->Cell(11,6, $nums." / ".$this->numOfStickers);

			$this->SetFont('Arial','',6);
			
			//Packing Type
			$origY=$y;
			$x+=$width;
			$this->SetY($y);
			$this->SetX($x);
			$width=20*$converter;
			$height=8*$converter;
			$this->Cell($width,$height,"",1);
			$this->SetY($y-.8);
			$this->SetX($x-.3);
			$this->Cell(11,6,"Packing Type");

			$this->SetFont('Times','',9);
			$this->SetY($y+(1.5*$converter));
			$this->SetX($x+(.3*$converter));
			$this->Cell(11,6,$this->PackingType);

			$this->SetFont('Arial','',6);
			$x=$origX;
			$y+=$height;
		}
			
	}

		
	if($row=mysqli_fetch_array($query)){
		//FOR DATABASE UPDATE
		//FOR SAVING//
		
		$prodCode			=$row["ItemCode"];
		$poNumber = $row['OrderNo'];
		$OrderNo = str_split($poNumber);
		$CheckIntExt = $OrderNo[0].$OrderNo[1]; //po starting 2 digit/letter
		
		if($CheckIntExt == "PR" || $CheckIntExt == "AD" || $CheckIntExt == "SM"){		
			$BuyerSticker = "PRICON MICROELECTRONICS INC. - (Internal)";
		}else{
			$BuyerSticker = "YAMAICHI ELECTRONICS CO., LTD";
		}
		$vendor = "PRICON MICROELECTRONICS INC.";
		$barcodeValue 		= "*".$prodCode."LOT".$lotNumber."*";
		$prodName			=$row["ItemName"];
		$drawingNumber		=$row["DrawingNo"];
		$revNo = $row['Rev'];
		$buyerComment		="Leave as blank - No Data"; 
		$numberOfStickers	=ceil($shipNum/$qtyPerBox);
		$converter			=1.069767441860465;
		$OnHand 			=$orderQty - $shipNum;
		$orientation		=($numberOfStickers>1)?'P':'L';
		$table				=new Table();
		
		//VALIDATE VALUES HERE
		if($orderQty-$shipNum < 0){
			validate(2,$orderQty);
		}else if($shipNum == "" || $shipNum == "0" || $qtyPerBox == "" || $qtyPerBox == "0"){
			validate(3,"Shipment Number and Quantity per box cannot be empty!");	
		}
		else{
			//QUERY TO INSERT IN tbl_Sticker
			$result = PPST::getInstance()->saveTicketingTransaction($TransactionDate,$poNumber,$lotNumber,$orderQty,$shipNum,$OnHand,$dateComment,$LastUpdate,$Username);
			if($result != "OK"){
				validate(4,$result);
				//echo $result;
			}else{
				//setFields($buyer,$vendor,$dpn,$dkn,$procodeA,$procodeB,$proname,$qty,$qtypb,$unit,$comments)
				
				$table->setFields($BuyerSticker,$vendor,$dateComment,$deliveryPlaceName,$deliveryDate,$poNumber,$prodCode,$barcodeValue,$prodName,$drawingNumber,$revNo,$lotNumber,$buyerComment,$packingType,$shipNum,$qtyPerBox);
				$table->SetAutoPageBreak(0);
				$table->AddPage();

				$table->mkSticker();
				$table->Output();
			}
		}
	}
	else
	{
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


function otherPrinting($query,$poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$TransactionDate,$LastUpdate,$Username,$numOfStickers2,$dynamicQty){
class Table extends FPDF{
	
	var $Buyer;
	var $Vendor;
	var $dateComment;
	var $DeliveryPlaceName;
	var $DeliveryKeyNum;
	var $DrawingNumber;
	var $ProductCodeA;
	var $ProductCodeB;
	var $ProductName;
	var $Qty;
	var $TotalQty;
	var $QtyPerBox;
	var $numOfStickers2;
	var	$dynamicQty;
	var $Unit;
	var $Comments;
	var $Company="PRICON MICROELECTRONICS INC.";
	var $numOfStickers=4;

	
	function setFields($buyer,$vendor,$date,$dpn,$dkn,$procodeA,$procodeB,$proname,$qty,$qtypb,$numOfStickers2,$dynamicQty,$unit,$comments)
	{
		$this->Buyer=$buyer;
		$this->Vendor=$vendor;
		$this->dateComment=$date;
		$this->DeliveryPlaceName=$dpn;
		$this->DeliveryKeyNum=$dkn;
		$this->ProductCodeA=$procodeA;
		$this->ProductCodeB=$procodeB;
		$this->ProductName=$proname;
		$this->Qty=$qty;
		$this->TotalQty=$qty;
		$this->QtyPerBox=$qtypb;
		$this->numOfStickers2=$numOfStickers2;
		$this->dynamicQty=$dynamicQty;		
		$this->Unit=$unit;
		$this->Comments=$comments;
		$this->numOfStickers=ceil($qty/$qtypb); //Change this into user input
	}
	
	function mkSticker()
	{
		$converter=1.069767441860465;
		$orgX=-190*$converter;
		$orgY=-270*$converter;
		$orgX=1.5*$converter;
		$orgY=2*$converter;
		$x=$orgX;
		$y=$orgY;
		$ctr2=-1;
		$ctr=0;
		$dynamicQtyCtr=0;
		for($i=0;$i<$this->numOfStickers2;$i++){
			if($i%2==0)
			{
				$ctr2++;
				$ctr=0;
				$x=$orgX;
				$y=$orgY;
				$y+=(($converter*55)*$ctr2);
			}
			else
			{
				$ctr=1;
			}
			$this->generateSticker($x+(($converter*99)*$ctr),$y,$i+1,$dynamicQtyCtr);
			$dynamicQtyCtr++;
			//$y+=59*$converter;
			$this->Qty-=$this->QtyPerBox;
			if($i+1==10&&$i+1!=$this->numOfStickers)
			{
				$ctr2=-1;
				$this->AddPage();
			}
			
			
		}
	}
	
	function Footer() { 
		
	  } 

	function generateSticker($x2,$y2,$num,$dynamicQtyCtr)
	{
		$x=$x2;
		$y=$y2;
		$nums=$num;
		$width=0;
		$height=0;

		$space=3;

		$converter=1.069767441860465;

		//Buyer Cell
		$this->SetY($y);
		$this->SetX($x);
		$width=56.5*$converter;
		$height=7.5*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetFont('Arial','',6);
		$this->SetY($y-($converter));
		$this->SetX($x-.5);
		$this->Cell(11,6,"Buyer");

		$this->SetFont('Times','',9);
		$this->SetY($y+$converter*2);
		$this->SetX($x+($converter*6));
		$this->Cell(11,6,$this->Buyer);

		//Unit
		$origX=$x;
		$origY=$y;
		$origHeight=$height;
		$x+=$width;
		$this->SetY($y);
		$this->SetX($x);
		$width=38*$converter;
		$height=31.5*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetFont('Arial','',6);
		$this->SetY($y-$converter);
		$this->SetX($x-.5);
		$this->Cell(11,6,"Vendor");

		$this->SetFont('Times','',7);
		$this->SetY($y+(2*$converter));
		$this->SetX($x-.5);
		$this->Cell(11,6,$this->Vendor);

		$this->SetFont('Arial','',6);
		$this->SetY($y-1.6+(12*$converter));
		$this->SetX($x-.5);
		$this->Cell(11,6,"Comments for vendor");
		//$this->Cell(7,35,"Made in the Philippines");

		$this->SetFont('Times','',9);
		$this->SetY($y+(19.5*$converter));
		$this->SetX($x);
		// $this->Cell(11,6,"04032014");
		$this->Cell(11,6,$this->dateComment);

		$this->SetFont('Arial','',6);
		$x=$origX;
		$y+=$origHeight;

		//Delivery place name
		$this->SetY($y);
		$this->SetX($x);
		$width=56.5*$converter;
		$height=7.5*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetY($y-($converter));
		$this->SetX($x-.5);
		$this->Cell(11,6,"Delivery place name");

		$this->SetFont('Times','',7);
		$this->SetY($y-($converter));
		$this->SetX($x+(35*$converter));
		$this->Cell(11,6,$this->DeliveryPlaceName);

		$this->SetFont('Arial','',6);
		$y+=$height;

		//Delivery Key No.
		$this->SetY($y);
		$this->SetX($x);
		$width=56.5*$converter;
		$height=7.5*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetY($y-1.6);
		$this->SetX($x-.5);
		$this->Cell(11,6,"Delivery Key No.");

		$this->SetFont('Times','',9);
		$this->SetY($y+(.5));
		$this->SetX($x+(15*$converter));
		$this->Cell(11,6,$this->DeliveryKeyNum);

		$this->SetFont('Arial','',6);
		$y+=$height;

		//Product code Buyer
		$this->SetY($y);
		$this->SetX($x);
		$width=56.5*$converter;
		$height=9*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetY($y-1.6);
		$this->SetX($x-.5);
		$this->Cell(11,6,"Product Code (Buyer)");

		$this->SetFont('Times','',8);
		$this->SetY($y-($converter));
		$this->SetX($x+(20*$converter));
		$this->Cell(11,6,$this->ProductCodeA);

		$this->SetFont('Times','',8);
		$this->SetY($y+(3*$converter));
		$this->SetX($x+($converter));
		$this->SetFont('Times','',9);
		$this->Cell(11,6,$this->ProductCodeB);

		$this->SetFont('Arial','',6);
		$y+=$height;

		//Prod name
		$this->SetY($y);
		$this->SetX($x);
		$width=94.5*$converter;
		$height=7*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetY($y-1.6);
		$this->SetX($x-.5);
		$this->Cell(11,6,"Product name");

		$this->SetFont('Times','',8);
		$this->SetY($y+(1*$converter));
		$this->SetX($x+($converter));
		$this->Cell(11,6,$this->ProductName);

		$this->SetFont('Arial','',6);
		$y+=$height;

		//QTY
		$this->SetY($y);
		$this->SetX($x);
		$width=78*$converter;
		$height=6.5*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetY($y-1.6);
		$this->SetX($x-.5);
		$this->Cell(11,6,"QTY/ Total QTY");
		
		$this->SetFont('Times','',9);
		$this->SetY($y);
		$this->SetX($x+(35*$converter));
		//$this->Cell(11,6,($this->Qty>=$this->QtyPerBox ? $this->QtyPerBox: $this->Qty)."/".$this->TotalQty);
		$this->Cell(11,6,$this->dynamicQty[$dynamicQtyCtr]."/".$this->TotalQty);

		$this->SetFont('Arial','',6);

		//Unit
		$origX=$x;
		$origY=$y;
		$x+=$width;
		$this->SetY($y);
		$this->SetX($x);
		$width=16.5*$converter;
		$height=6.5*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetY($y-1.6);
		$this->SetX($x-.5);
		$this->Cell(11,6,"Unit");

		$this->SetFont('Times','',9);
		$this->SetY($y);
		$this->SetX($x+(5*$converter));
		$this->Cell(11,6,$this->Unit);

		$this->SetFont('Arial','',6);
		$x=$origX;
		$y+=$height;

		//comments
		$this->SetY($y);
		$this->SetX($x);
		$width=64.5*$converter;
		$height=8*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetY($y-1.6);
		$this->SetX($x-.5);
		$this->Cell(11,6,"Comments for buyer");
		
		$this->SetFont('Times','',9);
		$this->SetY($y+$converter);
		$this->SetX($x+(15*$converter));
		$this->Cell(11,6,$this->Comments);

		$this->SetFont('Arial','',6);

		//Unit
		$origX=$x;
		$origY=$y;
		$x+=$width;
		$this->SetY($y);
		$this->SetX($x);
		$width=30*$converter;
		$height=8*$converter;
		$this->Cell($width,$height,"",1);
		$this->SetY($y-1.6);
		$this->SetX($x-.5);
		$this->Cell(11,6,"Package Count");

		$this->SetFont('Times','',8);
		$this->SetY($y+(3*$converter));
		$this->SetX($x+(10*$converter));
		$this->Cell(11,6,$nums." / ".$this->numOfStickers2);

		$this->SetFont('Arial','',6);
		$x=$origX;
		$y+=$height;
	}
	

}


if($row=mysqli_fetch_array($query)){
		//FOR DATABASE UPDATE
		//FOR SAVING//
		$poNumber = $row['OrderNo'];
		$OrderNo = str_split($poNumber);
		$CheckIntExt = $OrderNo[0].$OrderNo[1]; //po starting 2 digit/letter
		
		if($CheckIntExt == "PR" || $CheckIntExt == "AD" || $CheckIntExt == "SM"){		
			$BuyerSticker = "PRICON MICROELECTRONICS INC. - (Internal)";
		}else{
			$BuyerSticker = "YAMAICHI ELECTRONICS CO., LTD";
		}
		//FOR PRINTING
		$drawingNumber		=$row["DrawingNo"];
		$prodCode			=$row["ItemCode"];
		$prodName			=$row["ItemName"];
		$orderQty			=$row["OrderQty"] - $row['POBalance'];
		//$buyerComment=$row["BUYERS_CODE"];
		$buyerComment		="Leave as blank - No Data"; 
		$converter			=1.069767441860465;
		$numberOfStickers	=ceil($shipNum/$qtyPerBox);
		$OnHand 			=$orderQty - $shipNum;
		$orientation		=($numberOfStickers>1)?'P':'L';
		$table				=new Table();
		
		$Ctr_DynamicQty = count($dynamicQty);
		$Sum_DynamicQty = 0;
		$Begin_Count = 0;
		while ($Begin_Count < $Ctr_DynamicQty){
			$Sum_DynamicQty = $Sum_DynamicQty + $dynamicQty[$Begin_Count];
			$Begin_Count++;
		}
		//VALIDATE VALUES HERE
		if($orderQty-$Sum_DynamicQty < 0){
			validate(2,$orderQty-$Sum_DynamicQty);
		}else if($Sum_DynamicQty == "" || $Sum_DynamicQty == "0" || $numOfStickers2 == "" || $numOfStickers2 == "0"){
			validate(3,"Number of stickers and Quantity per box cannot be empty!".$Sum_DynamicQty);	
		}
		else{
			//QUERY TO INSERT IN tbl_Sticker
			$result = PPST::getInstance()->saveTicketingTransaction($TransactionDate,$poNumber,$orderQty,$Sum_DynamicQty,$OnHand,$dateComment,$LastUpdate,$Username);
			if($result != "OK"){
				validate(4,$result);
				//echo $result;
			}else{
				//setFields($buyer,$vendor,$dpn,$dkn,$procodeA,$procodeB,$proname,$qty,$qtypb,$unit,$comments)
				$table->setFields(	$BuyerSticker,
									"PRICON MICROELECTRONICS INC.",
									$dateComment,
									$deliveryPlaceName,
									$poNum."-003+001",
									$drawingNumber,
									$prodCode,
									$prodName,
									$shipNum,
									$qtyPerBox,
									$numOfStickers2,
									$dynamicQty,
									"PC",
									$buyerComment);
				$table->SetAutoPageBreak(0);
				$table->AddPage();

				$table->mkSticker();
				$table->Output();
			}
		}
	}
	else
	{
		validate(1,$poNum);
	}
}

?>