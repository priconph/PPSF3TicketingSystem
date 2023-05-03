<?php
	if(is_ajax()) {
		if(isset($_POST["action"]) && !empty($_POST["action"])) {
			$action = $_POST["action"];
			switch($action) {
				case "loadPRInformation" : loadPRInformation(); break;
				case "DeleteSticker" : DeleteSticker(); break;
				case "loadLotNumber" : loadLotNumber(); break;
			}
		}
	}

	function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
		
	function loadPRInformation(){
		require_once('PPSTicketing-OOP.php'); //OOP Path
		//Set all Post to return example: instead of $_POST["PartsCode"] will be converted to this $return["PartsCode"]
		$return = $_POST;
		$result = "";
		//OOP_Class_Name::getInstance()->Function_Name_Here(Parameter,Parameter2);
		$result = PPST::getInstance()->loadPRInformation($return['PRNo']);
		while($row = mysqli_fetch_array($result)){
			$return['OrderQty'] = $row['OrderQty'] - $row['POBalance'];
		}
		$return["result"] = json_encode($result); //return the the serialize values
		echo json_encode($return); //json encode since I used a dataType json before passing the serialized values here
	}
	
	function DeleteSticker(){
		require_once('PPSTicketing-OOP.php'); //OOP Path
		//Set all Post to return example: instead of $_POST["PartsCode"] will be converted to this $return["PartsCode"]
		$return = $_POST;
		$result = "";
		$result = PPST::getInstance()->DeleteSticker($return['id'],$return['PONumber'],$return['OnHand'],$return['ShippedQty']);
		$return["result"] = json_encode($result); //return the the serialize values
		echo json_encode($return); //json encode since I used a dataType json before passing the serialized values here
	}
	
	function loadLotNumber() {
		require_once('PPSTicketing-OOP.php'); //OOP Path
		//Set all Post to return example: instead of $_POST["PartsCode"] will be converted to this $return["PartsCode"]
		$return = $_POST;
		$result = "";
		//OOP_Class_Name::getInstance()->Function_Name_Here(Parameter,Parameter2);
		$result = PPST::getInstance()->loadLotNumber($return['PRNo']);
		$ctr = 0;
		while($row = mysqli_fetch_array($result)) {
			$return['LotNumber'][$ctr] = $row['LotNumber'];
			$ctr++;
		}
		$return['ctr'] = $ctr;
		$return["result"] = json_encode($result); //return the the serialize values
		echo json_encode($return); //json encode since I used a dataType json before passing the serialized values here
	}
?>