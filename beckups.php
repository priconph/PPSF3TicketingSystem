<?php
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
	$data  = "Buyer;Vendor;DeliverPlaceName;DeliveryDate;DeliveryKeyNo;CommentsForVendor;ProductCode;BarCode;ProductName;DrawingNo;RevNo;Qty;LotNo;CommentsForBuyer;PackageCount;PackingType;\n";
	//$data .= $Buyer.";".$Vendor.";".$DeliverPlaceName.";".$DeliveryDate.";".$DeliveryKeyNo.";".$CommentsForVendor.";".$ProductCode.";".$BarCode.";".$ProductName.";".$DrawingNo.";".$RevNo.";".$Qty.";".$LotNo.";".$CommentsForBuyer.";".$PackageCount.";".$PackingType.";\n";
	fwrite($myfile, $data);
	fclose($myfile);

	$file = './printjob/startprint.dd';
	$newfile = './printjob/scan/startprint.dd';
	if (!copy($file, $newfile)) {
		echo "ERROR: the .dd trigger is missing from the printjob folder";
	}
?>