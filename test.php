
<?php
$str = explode(';','rona');
echo count($str);
/*
require_once('fpdf.php'); 
require_once('./PPSTicketing-OOP.php');
error_reporting(E_ALL|E_STRICT);
define('FPDF_FONTPATH','font/');
session_start();
$LastUpdate = date("Y-m-d H-i-s");
$TransactionDate = date("Y-m-d");
$Username = $_SESSION['username'];
$query = PPST::getInstance()->loadPRInformation($_POST["txtPoNum"]);

if($_POST["txtNumOfStickers"] == ""){
	$poNum=$_POST["txtPoNum"];
	$qtyPerBox=$_POST["txtMaxQty"];
	$dateComment=$_POST["date"];
	$deliveryPlaceName=$_POST["selDeliveryPlaceName"];
	$shipNum=$_POST["txtShipQty"];
	$department="department";
	normalPrinting($query,$poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$TransactionDate,$LastUpdate,$Username);
}else{
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
	otherPrinting();
}

function normalPrinting($query,$poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$TransactionDate,$LastUpdate,$Username){
	class Table extends FPDF{
		var $Buyer;
		var $Vendor;
		var $dateComment;
		var $DelivaryPlaceName;
		var $DeliveryKeyNum;
		var $DrawingNumber;
		var $ProductCodeA;
		var $ProductCodeB;
		var $ProductName;
		var $Qty;
		var $TotalQty;
		var $QtyPerBox;
		var $Unit;
		var $Comments;
		var $Company="PRICON MICROELECTRONICS INC.";
		var $numOfStickers=4;

		function setFields($buyer,$vendor,$date,$dpn,$dkn,$procodeA,$procodeB,$proname,$qty,$qtypb,$unit,$comments)
		{
			
			$this->Buyer=$buyer;
			$this->Vendor=$vendor;
			$this->dateComment=$date;
			$this->DelivaryPlaceName=$dpn;
			$this->DeliveryKeyNum=$dkn;
			$this->ProductCodeA=$procodeA;
			$this->ProductCodeB=$procodeB;
			$this->ProductName=$proname;
			$this->Qty=$qty;
			$this->TotalQty=$qty;
			$this->QtyPerBox=$qtypb;
			$this->Unit=$unit;
			$this->Comments=$comments;
			$this->numOfStickers=ceil($qty/$qtypb);
	
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
			$this->Cell(11,6,$this->DelivaryPlaceName);

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
			$this->Cell(11,6,($this->Qty>=$this->QtyPerBox ? $this->QtyPerBox: $this->Qty)."/".$this->TotalQty);

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
			$this->Cell(11,6,$nums." / ".$this->numOfStickers);

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
		
		//VALIDATE VALUES HERE
		if($orderQty-$shipNum < 0){
			validate(2,$orderQty-$shipNum);
		}else if($shipNum == "" || $shipNum == "0" || $qtyPerBox == "" || $qtyPerBox == "0"){
			validate(3,"Shipment Numeber and Quantity per box cannot be empty!");	
		}
		else{
			//QUERY TO INSERT IN tbl_Sticker
			$result = PPST::getInstance()->saveTicketingTransaction($TransactionDate,$poNumber,$orderQty,$shipNum,$OnHand,$dateComment,$LastUpdate,$Username);
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

/*
function otherPrinting(){
class Table extends FPDF{
	
	var $Buyer;
	var $Vendor;
	var $dateComment;
	var $DelivaryPlaceName;
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
		$this->DelivaryPlaceName=$dpn;
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
		$this->Cell(11,6,$this->DelivaryPlaceName);

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


if($row=mssql_fetch_array($query)){
	$drawingNumber=$row["DRAWING_NUM"];
	$prodCode=$row["Prodcode"];
	$prodName=$row["Prodname"];
	$orderQty=$row["OrderQty"];
	$buyerComment=$row["BUYERS_CODE"];
	$converter=1.069767441860465;
	//echo ceil($shipNum/$qtyPerBox);
	$numberOfStickers=ceil($shipNum/$qtyPerBox);
	$orientation=($numberOfStickers>1)?'P':'L';
	$table=new Table();

	// setFields($buyer,$vendor,$dpn,$dkn,$procodeA,$procodeB,$proname,$qty,$qtypb,$unit,$comments)
	$table->setFields("YAMAICHI ELECTRONICS CO., LTD",
						"PRICON MICROELECTRONICS INC.",
						$dateComment,
						$deliveryPlaceName,
						$poNum."-003+001",
						$drawingNumber,
						$prodCode,
						$prodName,
						$shipNum,
						$qtyPerBox, //MJ FUTURE CHANGES: Explode Value into an array and add a num of stickers variable here.
						$numOfStickers2,
						$dynamicQty,
						"PC",
						$buyerComment);
	$table->SetAutoPageBreak(0);
	$table->AddPage();

	$table->mkSticker();
	$table->Output();
}
else
{
?>
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
			<p align="center">The Product with product number <?php echo $poNum."00010"; ?> could not be found.</p>
		</div>
	</body>
</html>
<?php

}
}
*/
?>