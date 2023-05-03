<?php
	if(is_ajax()) {
		if(isset($_POST["action"]) && !empty($_POST["action"])) {
			$action = $_POST["action"];
			switch($action) {
				case "loadPRInformation" : loadPRInformation(); break;
				case "DeleteSticker" : DeleteSticker(); break;
				case "loadLotNumber" : loadLotNumber(); break;
				case "fetch_ticketing_transaction" : fetch_ticketing_transaction(); break;
				case "delete_sticker_trans" : delete_sticker_trans(); break;
				
				/* Revisions as of Jan 8, 2019 - Start */
				case "get_PR_lists" 				: get_PR_lists(); break;
				case "get_package_category_list" 	: get_package_category_list(); break;
				case "get_packedby_name_list" 		: get_packedby_name_list(); break;
				/* Revisions as of Jan 8, 2019 - End */
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
	
	function fetch_ticketing_transaction(){
		require_once('PPSTicketing-OOP.php'); //OOP Path
		$return = $_POST;
		$result = "";
		$result = PPST::getInstance()->fetch_ticketing_transaction($return['po']);
		$ctr = 0;
		while($row = mysqli_fetch_array($result)){
			$return['TransactionDate'][$ctr] 	= $row['TransactionDate'];
			$return['ShippedQty'][$ctr] 		= $row['ShippedQty'];
			$return['LastUpdate'][$ctr] 		= $row['LastUpdate'];
			$ctr++;
		}
		$return['ctr'] = $ctr;
		$return["result"] = json_encode($result); //return the the serialize values
		echo json_encode($return); //json encode since I used a dataType json before passing the serialized values here
	}
	
	function delete_sticker_trans(){
		require_once('PPSTicketing-OOP.php'); //OOP Path
		$return = $_POST;
		$result = "";
		$result = PPST::getInstance()->delete_sticker_trans($return['po_number'],$return['last_update']);
		$return["result"] = json_encode($result); //return the the serialize values
		echo json_encode($return); //json encode since I used a dataType json before passing the serialized values here
	}

	function get_PR_lists(){
		require_once('PPSTicketing-OOP.php'); 
		$html = "";
		$result = "";
		$result = PPST::getInstance()->get_PR_lists($return['pattern']);
		while($row = mysqli_fetch_array($result)){
			$html .= '<option>'.$row['OrderNo'].'</option>';
		}
		$return["html"] = $html; //return the the serialize values
		$return["result"] = json_encode($result); //return the the serialize values
		echo json_encode($return); //json encode since I used a dataType json before passing the serialized values here
	}
	
	function get_package_category_list(){
		require_once('PPSTicketing-OOP.php'); 
		$html = "";
		$result = "";
		$result = PPST::getInstance()->get_package_category_list();
		while($row = mysqli_fetch_array($result)){
			// $html .= '<option>'.$row['package_category'].'</option>';
			$html .= '<option value="'.$row['package_category'].'">'.$row['package_category'].'</option>';
		}
		$return["html"] = $html; //return the the serialize values
		$return["result"] = json_encode($result); //return the the serialize values
		echo json_encode($return); //json encode since I used a dataType json before passing the serialized values here
	}
	
	function get_packedby_name_list(){
		require_once('PPSTicketing-OOP.php'); 
		$html = "";
		$result = "";
		$result = PPST::getInstance()->get_packedby_name($return['pattern']);
		while($row = mysqli_fetch_array($result)){
			$html .= '<option value="'.$row['name'].'">'.$row['name'].'</option>';
		}
		$return["html"] = $html; //return the the serialize values
		$return["result"] = json_encode($result); //return the the serialize values
		echo json_encode($return); //json encode since I used a dataType json before passing the serialized values here
	}
	
	
	?>