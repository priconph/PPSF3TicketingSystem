<?php
//Set Class name here, it's a good practice to Capitalize the first letter, I've just read it somewhere on the internet... so its up to you if will follow...
class PPST extends mysqli
{
	private static $instance = null;
	
	//db connection config variables
	private $user = "root";	//Username
	private $pass = "r@p1d0nl1n3"; //Password
	private $dbName = "db_pps"; //db_name Note: if you are going to use multiple database just place one here.. use sql to call the other one just in case, for example : database2.tablename
	private $dbHost = "localhost"; //host
	
	//NO NEED TO CHANGE HERE 
	public static function getInstance() {
		if(!self::$instance instanceof self)
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __clone()
	{
		trigger_error('Clone is not allowed.',E_USER_ERROR);
	}
	
	public function __wakeup()
	{
		trigger_error('Deserializing is not allowed.',E_USER_ERROR);
	}
	
	private function __construct() {
		parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
		if(mysqli_connect_error())
		{
			exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}
		parent::set_charset('utf-8');
	}

	////////////////////////////////////UP TO HERE////////////////////////////////////////
	
	////////////////////////////////////PUBLIC FUNCTIONS STARTS HERE//////////////////////////////////////////////
	//ADD A FUNCTION HERE
	public function loadPR(){
		//query then return
		$query = "SELECT * FROM tbl_POReceived";
		return $this->query($query);
	}
	
	public function loadPRInformation($PRNo){
		//query then return
		//check ko muna qng existing sya sa tbl_dieset_history
		//$check = $this->query("SELECT a.OrderNo,b.DrawingNo,a.ItemCode,a.ItemName,b.Rev FROM tbl_POReceived a INNER JOIN tbl_dieset_history b ON a.ItemCode = b.R3Code WHERE `OrderNo` = '$PRNo'");
		//if($check->num_rows == 0) {
			$query = $this->query("SELECT a.OrderNo,b.DrawingNo,a.ItemCode,a.ItemName,b.Rev FROM tbl_POReceived a LEFT JOIN tbl_dieset b ON a.ItemCode = b.R3Code WHERE `OrderNo` = '$PRNo'");
			return $query;
		//} else {
		//	return $check;
		//}
	}
	
	public function loadLotNumber($OrderNo) {
		$query = "SELECT DISTINCT(LotNumber) FROM tbl_DeliveryUpdateRecord a INNER JOIN tbl_DeliveryConfirmation b ON a.pkid=b.id WHERE b.OrderNo='$OrderNo'";
		return $this->query($query);
	}
	
	public function computeTotalShipmentQty($orderNo) {
		//$query = $this->query("SELECT SUM(a.ActualSO) AS ActualSO FROM tbl_DeliveryUpdateRecord a INNER JOIN tbl_DeliveryConfirmation b ON a.pkid=b.id WHERE a.LotNumber='$lotNumber' AND OrderNo =  '$orderNo'");
		$query = $this->query("SELECT SUM(a.ActualSO) AS ActualSO FROM tbl_DeliveryUpdateRecord a INNER JOIN tbl_DeliveryConfirmation b ON a.pkid=b.id WHERE OrderNo =  '$orderNo'");
		if($query->num_rows == 0) {
			return 0;
		} else {
			while($row = mysqli_fetch_array($query)) {
				return $row['ActualSO'];
			}
		}
			
	}
	
	public function saveTicketingTransaction($TransactionDate,$PONumber,$LotNumber,$OriginalQty,$ShippedQty,$OnHand,$ShipmentDate,$LastUpdate,$Username){
		//$checkOnhand = $this->query("SELECT SUM(`ShippedQty`) as ShippedQty FROM tbl_Ticketing WHERE `PONumber` = '$PONumber' AND LotNumber='$LotNumber'");
		$checkOnhand = $this->query("SELECT SUM(`ShippedQty`) as ShippedQty FROM tbl_Ticketing WHERE `PONumber` = '$PONumber'");
		if($checkOnhand->num_rows == 0){
			$query = "INSERT INTO `tbl_Ticketing` (`TransactionDate`, `PONumber`, `LotNumber` , `OriginalQty`, `ShippedQty`, `OnHand`, `ShipmentDate`, `LastUpdate`, `Username`) VALUES ('$TransactionDate','$PONumber','$LotNumber','$OriginalQty','$ShippedQty','$OnHand','$ShipmentDate','$LastUpdate','$Username')";
			return $this->query($query);
		} else{
			while($row = mysqli_fetch_array($checkOnhand)){
				$shippedqtyonDB = $row['ShippedQty'];
			}
			$count = $shippedqtyonDB + $ShippedQty;
			if($count > $OriginalQty){
				return "Error: OriginalQty = ".$OriginalQty." Total Shipped Qty: ".$shippedqtyonDB." + ".$ShippedQty." = ".$count;
			}else{
				$OnHand = $OnHand - $shippedqtyonDB;
				$loadLastID = $this->query("SELECT id FROM `tbl_Ticketing` WHERE `PONumber` = '$PONumber'");
				if($row = mysqli_fetch_array($loadLastID)){
					$lastID = $row['id'];
				} else {
					$lastID = 1;
				}
				$onHand = $this->query("SELECT OnHand FROM tbl_Ticketing WHERE PONumber='$PONumber' ORDER BY id DESC");
				if($rowOnHand = mysqli_fetch_array($onHand)) {
					$OriginalQty = $rowOnHand['OnHand'];
				}
				$this->query("UPDATE `tbl_Ticketing` SET `IsEditable` = '1' WHERE id = '$lastID'");
				//echo "INSERT INTO `tbl_Ticketing` (`TransactionDate`, `PONumber`, `LotNumber`, `OriginalQty`, `ShippedQty`, `OnHand`, `ShipmentDate`, `LastUpdate`, `Username`) VALUES ('$TransactionDate','$PONumber','$LotNumber','$OriginalQty','$ShippedQty','$OnHand','$ShipmentDate','$LastUpdate','$Username')<br>";
				$query = $this->query("INSERT INTO `tbl_Ticketing` (`TransactionDate`, `PONumber`, `LotNumber`, `OriginalQty`, `ShippedQty`, `OnHand`, `ShipmentDate`, `LastUpdate`, `Username`) VALUES ('$TransactionDate','$PONumber','$LotNumber','$OriginalQty','$ShippedQty','$OnHand','$ShipmentDate','$LastUpdate','$Username')");
				return "OK";
			}
		}
	}
	public function checkTicketingTransaction($TransactionDate,$PONumber,$LotNumber,$OriginalQty,$ShippedQty,$OnHand,$ShipmentDate,$LastUpdate,$Username){
		//$checkOnhand = $this->query("SELECT SUM(`ShippedQty`) as ShippedQty FROM tbl_Ticketing WHERE `PONumber` = '$PONumber' AND LotNumber='$LotNumber'");
		$checkOnhand = $this->query("SELECT SUM(`ShippedQty`) as ShippedQty FROM tbl_Ticketing WHERE `PONumber` = '$PONumber'");
		if($checkOnhand->num_rows > 0){
			while($row = mysqli_fetch_array($checkOnhand)){
				$shippedqtyonDB = $row['ShippedQty'];
			}
			$count = $shippedqtyonDB + $ShippedQty;
			if($count > $OriginalQty){
				return "Error: OriginalQty = ".$OriginalQty." Total Shipped Qty: ".$shippedqtyonDB." + ".$ShippedQty." = ".$count;
			} else{
				$OnHand = $OnHand - $shippedqtyonDB;
				$loadLastID = $this->query("SELECT * FROM `tbl_Ticketing` WHERE `PONumber` = '$PONumber'");
				while($row = mysqli_fetch_array($loadLastID)){
					$lastID = $row['id'];
				}
				return "OK";
			}
		}
	}
	
	public function loadTicketingInfo($POSearch,$startDate,$endDate){
		$query = "SELECT * FROM tbl_Ticketing WHERE `PONumber` LIKE '%$POSearch%' AND `TransactionDate` BETWEEN '$startDate' AND '$endDate' ORDER by `id`";
		return $this->query($query);
		//return $query;
	}
	
	public function loadTicketingInfoPO($POSearch){
		$query = "SELECT * FROM tbl_Ticketing WHERE `PONumber` LIKE '%$POSearch%' ORDER by `id`";
		return $this->query($query);
		//return $query;
	}
	
	public function DeleteSticker($id,$PONumber,$OnHand,$ShippedQty){
		$DeleteThis = $this->query("SELECT * FROM tbl_Ticketing WHERE id='$id'");
		while($row = mysqli_fetch_array($DeleteThis)){
			if($row['IsEditable'] == 0){
				$this->query("DELETE FROM tbl_Ticketing WHERE id='$id'");
				$query = $this->query("SELECT * FROM tbl_Ticketing WHERE PONumber = '$PONumber' ORDER BY id");
				while($row = mysqli_fetch_array($query)){
					$lastID = $row['id'];
					$ShippedQtyD = $row['ShippedQty'];
					$OnHandD = $row['OnHand'];
				}
				if(isset($lastID)){
					$OnHandD 	 = $OnHandD + $ShippedQty;
					$ShippedQtyD = $OnHandD - $ShippedQty;
					$this()->query("UPDATE tbl_Ticketing SET `OnHand` = '$OnHandD', `ShippedQty` = '$ShippedQtyD', IsEditable = '0'  WHERE id='$LastID'");
				}
				return "Data deleted!";
			}else{
				return "Data cannot be deleted! Please delete the most recent record";
			}
		}
		
		
	}
	
	
}
?>
