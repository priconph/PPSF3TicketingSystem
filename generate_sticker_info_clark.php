<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Specify allowed HTTP methods
header("Content-Type: application/json; charset=UTF-8");
require_once('./PPSTicketing-OOP.php');
session_start();

$post_data = json_decode(file_get_contents("php://input"), true);

// echo $post_data['print_delivery_key_no'];
// return;

$LastUpdate 		= date("Y-m-d H:i s");
$TransactionDate 	= date("Y-m-d");
$Username 			= isset($post_data['print_username']) ? $post_data['print_username'] : "";
$LotNo 			    = isset($post_data['print_lot_no']) ? $post_data['print_lot_no'] : "";
$OrderQty 			= PPST::getInstance()->computeTotalShipmentQty($post_data['print_delivery_key_no']);
$query 				= PPST::getInstance()->loadPRInformation($post_data['print_delivery_key_no']);
$packedby			= isset($post_data['print_packed_by']) ? $post_data['print_packed_by'] : "";
$poNum       		= $post_data["print_delivery_key_no"];
$DateComment 		= $post_data["print_date"];
$department  		= "department";

$DeliveryPlaceName  = $post_data["print_delivery_place"];
$DeliveryDate       = $post_data["print_delivery_date"];
$PackingType        = $post_data["print_package_category"];

/* Added by Nessa 05272025 */
if ($PackingType == '1'){
	$PackingType = 'Bundle';
}else if ($PackingType == '2'){
	$PackingType = 'Box';
}else if ($PackingType == '3'){
	$PackingType = 'Tray';
}else if ($PackingType == '4'){
	$PackingType = 'Polybag';
}else if ($PackingType == '5'){
	$PackingType = 'Reel';
}

$CommentsForBuyer	= "";

	if($post_data['print_dynamic_sticker_count'] == NULL && $post_data['print_custom_package_count'] == NULL){	//for normal printing
		$check_reprint = $post_data['check_reprint'];
		$Qty           = $post_data["print_normal_qty"];
		$QtyPerBox     = $post_data["print_normal_total_qty"];

		//Insert Data to Table "tbl_Ticketing"
        normalPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$Qty,$QtyPerBox,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$LastUpdate,$Username,$packedby,$check_reprint);
        
	}else if($post_data["print_dynamic_sticker_count"] != ""){
		$NumberOfStickers = $post_data["print_dynamic_sticker_count"];
        
        //Insert Data to Table "tbl_Ticketing"
        dynamicPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$NumberOfStickers,$LastUpdate,$Username,$packedby);
	}else{
		$Qty          = $post_data["print_custom_qty"];
		$TotalQty     = $post_data["print_custom_total_qty"];
		$PackageCount = $post_data["print_custom_package_count"];
		 
		customizePrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$Qty,$TotalQty,$LotNo,$CommentsForBuyer,$PackingType,$PackageCount,$DateComment,$LastUpdate,$Username,$packedby);
	}

	//OLD CODE for reference
	// $LastUpdate 		= date("Y-m-d H:i s");
	// $TransactionDate 	= date("Y-m-d");
	// $Username 			= $_SESSION['username'];
	// $LotNo	 			= $post_data["print_lot_no"];
	// $OrderQty 			= PPST::getInstance()->computeTotalShipmentQty($post_data["print_delivery_key_no"]);
	// $query 				= PPST::getInstance()->loadPRInformation($post_data["print_delivery_key_no"]);
	// $packedby			= $post_data["print_packed_by"];

	// if($post_data["num_of_stickers"] == "" && $post_data["package_count"] == ""){	//for normal printing
	// 	$check_reprint = $post_data['check_reprint'];
	// 	$poNum=$post_data["print_delivery_key_no"];
	// 	$DateComment=$post_data["date"];
	// 	$department="department";
		
	// 	$DeliveryPlaceName=$post_data["print_delivery_place"];
	// 	$DeliveryDate=$post_data["print_delivery_date"];
	// 	$PackingType=$post_data["print_package_category"];
	// 	$Qty=$post_data["max_qty"];
	// 	$QtyPerBox=$post_data["ship_qty"];
	// 	$CommentsForBuyer	= ""; 
	// 	normalPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$Qty,$QtyPerBox,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$LastUpdate,$Username,$packedby,$check_reprint);
	// } 
	// else if($post_data["num_of_stickers"] != "") {	//for dynamic printing
	// 	$poNum=$post_data["print_delivery_key_no"];
	// 	$DateComment=$post_data["date"];
	// 	$department="department";
		
	// 	$DeliveryPlaceName=$post_data["print_delivery_place"];
	// 	$DeliveryDate=$post_data["print_delivery_date"];
	// 	$PackingType=$post_data["print_package_category"];
	// 	$NumberOfStickers=$post_data["num_of_stickers"];
	// 	$CommentsForBuyer	= ""; 
	// 	dynamicPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$NumberOfStickers,$LastUpdate,$Username,$packedby);
	// } 
	// else {		//for customize printing
	// 	$poNum=$post_data["print_delivery_key_no"];
	// 	$DateComment=$post_data["date"];
	// 	$department="department";
		
	// 	$DeliveryPlaceName=$post_data["print_delivery_place"];
	// 	$DeliveryDate=$post_data["print_delivery_date"];
	// 	$PackingType=$post_data["print_package_category"];
	// 	$Qty=$post_data["customized_qty"];
	// 	$TotalQty=$post_data["customized_ship_qty"];
	// 	$PackageCount=$post_data["package_count"];
	// 	$CommentsForBuyer	= ""; 
	// 	customizePrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$Qty,$TotalQty,$LotNo,$CommentsForBuyer,$PackingType,$PackageCount,$DateComment,$LastUpdate,$Username,$packedby);
	// }

	function normalPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$Qty,$QtyPerBox,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$LastUpdate,$Username,$packedby,$check_reprint){	
		if($row = mysqli_fetch_array($query)){
			$OrderNo 			= 	$row['OrderNo'];
			$ProductCode 		= 	$row['ItemCode'];
			$BarCode			=	$ProductCode . "LOT" . $LotNo;
			$ProductName 		= 	$row['ItemName'];		
			$BuyerChecker 		= 	strtoupper($OrderNo[0].$OrderNo[1]);	//Check the OrderNo first 2 index. 
			if($BuyerChecker == "PR" || $BuyerChecker == "AD" || $BuyerChecker == "SM" || $BuyerChecker == "TS" || $BuyerChecker == "WH" || $BuyerChecker == "CN") {
				$Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
				$newfile = './printjob_f3/scan/internal.dd';
			} else if($BuyerChecker == "PP"){
				$Buyer = "PRICON MICROELECTRONICS INC. - (TOKAI)";
				$newfile = './printjob_f3/scan/internal.dd';
			}else if($BuyerChecker == "SP"){
				$Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
				$newfile = './printjob_f3/scan/internal.dd';
			}else if($BuyerChecker == "RE" && strpos($OrderNo,'PP') !== false){
				$Buyer = "PRICON MICROELECTRONICS INC. - (TOKAI)";
				$newfile = './printjob_f3/scan/internal.dd';
			}else {
				$Buyer = "YAMAICHI ELECTRONICS CO., LTD";
				$newfile = './printjob_f3/scan/external.dd';
			}
			$Vendor 			= 	"PRICON MICROELECTRONICS INC.";
			$NumberOfStickers 	= 	ceil($QtyPerBox/$Qty);
			
			$DeliveryKeyNo		=	$OrderNo;
			
			if(isset($post_data['print_stamping'])){
				$query_stamping = SDMCMS::getInstance()->select_stamping_data($ProductCode);
				$DrawingNo = $query_stamping['drawing_no'];
				$RevNo = $query_stamping['rev'];
			}else{
				$DrawingNo 			= 	$row['DrawingNo'];
				$RevNo 				= 	$row['Rev'];	
			}
			
			if($OrderQty-$Qty < 0){
				validate(2,$OrderQty);
			}else if($Qty == "" || $Qty == "0" || $QtyPerBox == "" || $QtyPerBox == "0"){
				validate(3,"Shipment Quantity and Quantity per box cannot be empty!");
			}else{
				$check = PPST::getInstance()->checkTicketingTransaction($TransactionDate,$OrderNo,$LotNo,$OrderQty,$QtyPerBox,$LastUpdate,$Username);
				// if($check != "OK"){	//the total shipment quantity is greater than actual quantity
					// validate(4,$check);
				// }else{
					//save the transaction to the database and ready for print na :)
					ini_set('display_errors', 1); 
					error_reporting(E_ALL);

					$filename = "./printjob_f3/data/data.txt";
					if(!file_exists($filename)){
						echo "ERROR: the data text file is missing in the printjob_f3/data/directory";
					} else{
						echo "DONE PRINTING";
					}
					echo "<br>";

					$myfile = fopen($filename, "w") or die("Unable to open file!");
					$tempQtyPerBox = $QtyPerBox;
					$tempQty = 0;
					$data = "";
					$data  = "Buyer;Vendor;DeliverPlaceName;DeliveryDate;DeliveryKeyNo;CommentsForVendor;ProductCode;BarCode;ProductName;DrawingNo;RevNo;Qty;LotNo;CommentsForBuyer;PackageCount;PackingType;packedby;preshipment;\n";
					$save_trigger = $NumberOfStickers - 1;
					$qty_all = 0;
					
					for($i = 0; $i < $NumberOfStickers; $i++) {
						$TotalQty = $tempQtyPerBox - $tempQty;
						if($Qty > $TotalQty) {
							$Qty = $tempQtyPerBox - $tempQty;
						} else {
							$tempQty += $Qty;
						}
						$OnHand					= $OrderQty -	$Qty;
						$QtyDisplay 			= $Qty ."/".$QtyPerBox;
						$PackageCountDisplay 	= ($i + 1)."/".$NumberOfStickers;
						
						//QTY OF ALL TICKET
						$qty_all += $Qty;
						
						if($save_trigger == $i){
							// $result = PPST::getInstance()->saveTicketingTransaction($TransactionDate,$OrderNo,$LotNo,$OrderQty,$qty_all,$OnHand,$DateComment,$PackingType,$packedby,$LastUpdate,$Username);
						}
						$preshipment_data ="";
						if($DrawingNo == ""){
							$DrawingNo = "-";
						}
						if($RevNo == ""){
							$RevNo = "-";
						}
						$preshipment_data = $DeliveryKeyNo.", ".$ProductCode.", ".$ProductName.", ".$LotNo.", ".$QtyPerBox.", ".$PackingType.", ".$PackageCountDisplay.", ".$DrawingNo.", ".$RevNo;

						$data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCountDisplay.";".$PackingType.";".$packedby.";".$preshipment_data.";\n";;
					}

					if($data != ""){
						fwrite($myfile, $data);
						fclose($myfile);
						$file = './printjob_f3/startprint.dd';
						
						if (!copy($file, $newfile)) {
							echo "ERROR: the .dd trigger is missing from the printjob_f3 folder";
						}
					}
				// }
			}
		}else{
			validate(1,$poNum);
		}	
	}

    function dynamicPrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$LotNo,$CommentsForBuyer,$PackingType,$DateComment,$StickersValue,$LastUpdate,$Username,$packedby){	
        if($row = mysqli_fetch_array($query)) {
            $OrderNo 			= 	$row['OrderNo'];
            $ProductCode 		= 	$row['ItemCode'];
            $BarCode			=	$ProductCode . "LOT" . $LotNo;
            $ProductName 		= 	$row['ItemName'];
            $BuyerChecker 		= 	strtoupper($OrderNo[0].$OrderNo[1]);	//Check the OrderNo first 2 index. 
            if($BuyerChecker == "PR" || $BuyerChecker == "AD" || $BuyerChecker == "SM" || $BuyerChecker == "TS" || $BuyerChecker == "WH" || $BuyerChecker == "CN") {	
                $Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
                $newfile = './printjob_f3/scan/internal.dd';
            } else if($BuyerChecker == "PP"){
                $Buyer = "PRICON MICROELECTRONICS INC. - (TOKAI)";
                $newfile = './printjob_f3/scan/internal.dd';
            }else if($BuyerChecker == "SP"){
                $Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
                $newfile = './printjob_f3/scan/internal.dd';
            }else {
                $Buyer = "YAMAICHI ELECTRONICS CO., LTD";
                $newfile = './printjob_f3/scan/external.dd';
            }
            $Vendor 			= 	"PRICON MICROELECTRONICS INC.";
            $Stickers			=	explode(',',$StickersValue);
            $NumberOfStickers 	= 	count($Stickers);
            $DeliveryKeyNo		=	$OrderNo;
            for($x = 0; $x < $NumberOfStickers; $x++) {
                $QtyPerBox += $Stickers[$x];
            }
            
            if(isset($post_data['print_stamping'])){
                $query_stamping = SDMCMS::getInstance()->select_stamping_data($ProductCode);
                $DrawingNo = $query_stamping['drawing_no'];
                $RevNo = $query_stamping['rev'];
            }else{
                $DrawingNo 			= 	$row['DrawingNo'];
                $RevNo 				= 	$row['Rev'];	
            }
            
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

                    $filename = "./printjob_f3/data/data.txt";
                    if(!file_exists($filename)){
                        echo "ERROR: the data text file is missing in the printjob_f3/data/ directory";
                    }else{
                        echo "DONE PRINTING";
                    }
                    echo "<br>";

                    $myfile = fopen($filename, "w") or die("Unable to open file!");
                    $tempQtyPerBox = $QtyPerBox;
                    $tempQty = 0;
                    $data = "";
                    $data  = "Buyer;Vendor;DeliverPlaceName;DeliveryDate;DeliveryKeyNo;CommentsForVendor;ProductCode;BarCode;ProductName;DrawingNo;RevNo;Qty;LotNo;CommentsForBuyer;PackageCount;PackingType;packedby;preshipment;\n";
                    
                    for($i = 0; $i < $NumberOfStickers; $i++) {		
                        $QtyDisplay 			= 	$Stickers[$i] ."/".$QtyPerBox;
                        $PackageCountDisplay 	= 	($i + 1)."/".$NumberOfStickers;
                        $OnHand				=	$OrderQty -	$Stickers[$i];
                        // $result = PPST::getInstance()->saveTicketingTransaction($TransactionDate,$OrderNo,$LotNo,$OrderQty,$Stickers[$i],$OnHand,$DateComment,$PackingType,$packedby,$LastUpdate,$Username);
                        
                        $preshipment_data ="";
                        if($DrawingNo == ""){
                            $DrawingNo = "-";
                        }

                        if($RevNo == ""){
                            $RevNo = "-";
                        }

                        $preshipment_data = $DeliveryKeyNo.", ".$ProductCode.", ".$ProductName.", ".$LotNo.", ".$QtyPerBox.", ".$PackingType.", ".$PackageCountDisplay.", ".$DrawingNo.", ".$RevNo;
                        $data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCountDisplay.";".$PackingType.";".$packedby.";".$preshipment_data.";\n";
                    }
                    fwrite($myfile, $data);
                    fclose($myfile);

                    $file = './printjob_f3/startprint.dd';
                    $newfile = './printjob_f3/scan/startprint.dd';
                    if (!copy($file, $newfile)) {
                        echo "ERROR: the .dd trigger is missing from the printjob_f3 folder";
                    }
                }
            }
        } else {
            validate(1,$poNum);
        }	
    }

    function customizePrinting($query,$TransactionDate,$DeliveryPlaceName,$DeliveryDate,$CommentsForVendor,$OrderQty,$Qty,$TotalQty,$LotNo,$CommentsForBuyer,$PackingType,$PackageCount,$DateComment,$LastUpdate,$Username,$packedby){	
        if($row = mysqli_fetch_array($query)) {
            $OrderNo 			= 	$row['OrderNo'];
            $ProductCode 		= 	$row['ItemCode'];
            $BarCode			=	$ProductCode . "LOT" . $LotNo;
            $ProductName 		= 	$row['ItemName'];		
            $BuyerChecker 		= 	strtoupper($OrderNo[0].$OrderNo[1]);	//Check the OrderNo first 2 index. 
            if($BuyerChecker == "PR" || $BuyerChecker == "AD" || $BuyerChecker == "SM" || $BuyerChecker == "TS" || $BuyerChecker == "WH" || $BuyerChecker == "CN") {	
                $Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
                $newfile = './printjob_f3/scan/internal.dd';
            } else if($BuyerChecker == "SP"){
                $Buyer = "PRICON MICROELECTRONICS INC. - (Internal)";
                $newfile = './printjob_f3/scan/internal.dd';
            }else if($BuyerChecker == "PP"){
                $Buyer = "PRICON MICROELECTRONICS INC. - (TOKAI)";
                $newfile = './printjob_f3/scan/internal.dd';
            }else{
                $Buyer = "YAMAICHI ELECTRONICS CO., LTD";
                $newfile = './printjob_f3/scan/external.dd';
            }

            $Vendor 			= 	"PRICON MICROELECTRONICS INC.";
            $Stickers			=	explode(',',$StickersValue);
            $NumberOfStickers 	= 	count($Stickers);
            $OnHand				=	$OrderQty -	$Qty;
            $DeliveryKeyNo		=	$OrderNo;
            
            if(isset($post_data['print_stamping'])){
                $query_stamping = SDMCMS::getInstance()->select_stamping_data($ProductCode);
                $DrawingNo = $query_stamping['drawing_no'];
                $RevNo = $query_stamping['rev'];
            }else{
                $DrawingNo 			= 	$row['DrawingNo'];
                $RevNo 				= 	$row['Rev'];	
            }

            //VALIDATE VALUES HERE
            if($NumberOfStickers == 0) {
                validate(3,'Invalid sticker quantity!');
            }else{
                //ready to print
                ini_set('display_errors', 1); 
                error_reporting(E_ALL);

                $filename = "./printjob_f3/data/data.txt";
                if(!file_exists($filename)){
                    echo "ERROR: the data text file is missing in the printjob_f3/data/ directory";
                }else{
                    echo "DONE PRINTING";
                }
                echo "<br>";

                $myfile = fopen($filename, "w") or die("Unable to open file!");;
                $data = "";
                $data  = "Buyer;Vendor;DeliverPlaceName;DeliveryDate;DeliveryKeyNo;CommentsForVendor;ProductCode;BarCode;ProductName;DrawingNo;RevNo;Qty;LotNo;CommentsForBuyer;PackageCount;PackingType;packedby;preshipment;\n";
                    
                $QtyDisplay 			= 	$Qty ."/".$TotalQty;
                $preshipment_data ="";
                if($DrawingNo == ""){
                    $DrawingNo = "-";
                }

                if($RevNo == ""){
                    $RevNo = "-";
                }

                $preshipment_data = $DeliveryKeyNo.", ".$ProductCode.", ".$ProductName.", ".$LotNo.", ".$TotalQty.", ".$PackingType.", ".$PackageCount.", ".$DrawingNo.", ".$RevNo;

                $data .= $Buyer.";".$Vendor.";".$DeliveryPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$QtyDisplay.";".$LotNo.";".$CommentsForBuyer.";".$PackageCount.";".$PackingType.";".$packedby.";".$preshipment_data.";\n";;
                
                fwrite($myfile, $data);
                fclose($myfile);

                $file = './printjob_f3/startprint.dd';

                if (!copy($file, $newfile)) {
                    echo "ERROR: the .dd trigger is missing from the printjob_f3 folder";
                }
            }
        }else{
            validate(1,$poNum);
        }	
    }

    function validate($Category,$passedValue){
        echo $Category .'false';
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