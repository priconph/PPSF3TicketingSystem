<?php
require_once('./includes.php');
require_once('./PPSTicketing-OOP.php');
session_start();
$dateToday = date('Y-m-d');
?>
<html>
<title>PPS Database System</title>
<head>
<style>
table#tbl_Ticketing tbody tr.selected{
	color : teal;
}
</style>
</head>
<body>
<div class="panel panel-primary">
	<div class="panel-heading">PPS Label Sticker (Ticketing)</div>
	<div class="panel-body">
		<?php
		if($_SESSION['date']==""){
			//echo "ginawa nya to!";
			if(isset($_POST['txtStartDate'])){
				$_SESSION['date'] = $_POST['txtStartDate'];
				$_SESSION['date_out'] = $_POST['txtEndDate'];
			}else{
				$_SESSION['date'] = $dateToday;
				$_SESSION['date_out'] = $dateToday;
			}
		}else{
			if($_POST['txtStartDate']!=""){
				$_SESSION['date'] = $_POST['txtStartDate'];
				$_SESSION['date_out'] = $_POST['txtEndDate'];
			}else{
				
			}
		}
		?>
		<form class="form-inline" role="form" method="POST" action="Ticketing.php">
		 <div class="form-group">
			<label class="sr-only" for="txtStartDate">Start Date</label>
			<?php 
				echo '<input type="date" class="form-control" id="txtStartDate" name="txtStartDate" placeholder="Start Date" value="'.$_SESSION['date'].'">';	
			?>
		  </div>
		  <div class="form-group">
			<label class="sr-only" for="txtEndDate">End Date</label>
			<?php 
				echo '<input type="date" class="form-control" id="txtEndDate" name = "txtEndDate" placeholder="End Date" value="'.$_SESSION['date_out'].'">';
			?>
		  </div>
		  <div class="form-group">
			<label class="sr-only" for="txtPOSearch">PO Number</label>
			<input type="text" class="form-control" id="txt_PONumberSearch" name = "txtPOSearch" style="width:100%;" list="list_PRNo" value="<?php echo $_POST["txtPOSearch"]; ?>" placeholder="PO Number">
		  </div>
		 <button type="submit" class="btn btn-default" id="SetDateRange">SEARCH</button>
		</form>
		
		<div class="btn-group">
		  <!--<button id="btn_NewSticker" type="button" class="btn btn-default">NEW STICKER(PDF)</button>-->
		  <button id="btn_NewSatoSticker" type="button" class="btn btn-default">NEW STICKER(SATO)</button>
		  <button id="btn_DeleteSticker" type="button" class="btn btn-default">DETAILS</button>
		</div>
		<table id="tbl_Ticketing" border="1">
			<thead>
				<tr>
					<th>Transaction Date</th>
					<th>P.O. Number</th>
					<th>PO Qty</th>
					<th>Shipped Qty</th>
					<th>On Hand</th>
					<th>Shipment Date</th>
					<th style="display:none;"></th>
					<th style="display:none;"></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$result = PPST::getInstance()->loadTicketingInfo($_POST['txtPOSearch'],$_SESSION['date'],$_SESSION['date_out']);
					$new_po = "";
					while($row = mysqli_fetch_array($result)){
						echo "<tr id='".$row['id']."' class=''>";
						echo "<td>".$row["TransactionDate"]."</td>";
						echo "<td class='PONumber'>".$row["PONumber"]."</td>";
						$qty_for_shipment = PPST::getInstance()->computeTotalShipmentQty($row["PONumber"]);
						echo "<td>".$qty_for_shipment."</td>";
						$shipped_qty = PPST::getInstance()->get_shipped_qty($row["PONumber"]);
						echo "<td class='ShippedQty'>".$shipped_qty."</td>";
						$result_on_hand = $qty_for_shipment - $shipped_qty;
						echo "<td class='OnHand'>".$result_on_hand."</td>";
						echo "<td>".$row["ShipmentDate"]."</td>";
						echo "<td class='IsEditable' style='display:none;'>".$row["IsEditable"]."</td>";
						echo "<td class='id' style='display:none;'>".$row["id"]."</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<datalist id="list_PRNo">
<?php
	$result = PPST::getInstance()->loadPR();
	while($row = mysqli_fetch_array($result)){
		echo '<option>'.$row['OrderNo'].'</option>';
	}
?>
</datalist>
<!--Modal-->
<div id="modal_GenerateSticker" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="ModalDeleteLabel">NEW STICKER(PDF)</h4>
				  
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs" role="tablist">
					  <li class="active" id="li_NormalP"><a href="#">Normal</a></li>
					  <li class="" id="li_DynamicP"><a href="#">Dynamic</a></li>
					  <li class="" id="li_CustomizedP"><a href="#">Customized</a></li>
				</ul>
				<br>
				<div>
					<form method="POST" action="./ticketingPDFPrinting.php" autocomplete="off">
						<label style="width:43%;margin-right:2%;">P.O. #:</label><input type="text" id="txtPoNumPDF" name="txtPoNumPDF" style="width:50%;" list="list_PRNo">
						<label style="width:43%;margin-right:1.1%;">Lot #:</label>
						<select id='cmbLotNumberPDF' name='cmbLotNumberPDF' style="width:50%; height:28px;">
						</select>
						<label style="width:43%;margin-right:1.1%;">Packing Type:</label>
						<select id='cmbPackingType' name='cmbPackingType' style="width:50%; height:28px;">
							<option>Box</option>
							<option>Tray</option>
							<option>Polybag</option>
						</select>
						<!--<input type="text" id="txtLotNumber" name="txtLotNumber" style="width:50%;" list="list_LotNumber">
						<label style="width:43%;margin-right:1.1%;">Available Qty:</label> <input type='text' id = 'txt_AvailableQty' style="width:50%;" disabled>-->
						<div id="normalPrinting">
							<label style="width:43%;margin-right:2%;">Shipment Qty:</label><input onkeypress="return isNumberKey(event);" type="text" id="txtShipQty" name="txtShipQty" style="width:50%;">
							<label style="width:43%;margin-right:2%;">Maximum Qty:</label><input type="text" id="txtMaxQty" name="txtMaxQty" style="width:50%;">
						</div>
						<label style="width:43%;margin-right:1.1%;">Delivery Place Name:</label>
						<select name="selDeliveryPlaceName" id="dpn" style="width:50%;"><option value="W021">W021</option><option value="W001" <?php echo $edit_mode&&$DPN=="W001" ? "Selected":""; ?>>W001</option></select>
						<label style="width:43%;margin-right:2%;">Shipment Date:</label><input type="date" id="" name="date" style="width:50%;" value="<?php echo $dateToday; ?>">
						<label style="width:43%;margin-right:2%;">Delivery Date:</label><input type="date" id="deliveryDate" name="deliveryDate" style="width:50%;" value="<?php echo $dateToday; ?>">
						<div id="dynamicPrinting">
							<label style="width:43%;margin-right:2%;">Number of Stickers:</label><input type="text" id="" name="txtNumOfStickers" style="width:50%;">
							<label style="width:43%;margin-right:2%;">Qty per box:</label><input type="text" id="" name="txtQtyPerBox" style="width:50%;">
						</div>
						<div id="customizedPrinting">
							<label style="width:43%;margin-right:2%;">Shipment Qty:</label><input onkeypress="return isNumberKey(event);" type="text" id="txtShipQty" name="txtShipQty" style="width:50%;">
							<label style="width:43%;margin-right:2%;">Package Count(Example:32/55):</label><input type="text" id="" name="txtPackageCount" style="width:50%;">
							<label style="width:43%;margin-right:2%;">Qty:</label><input type="text" id="" name="txtQuantityCustomized" style="width:50%;">
						</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="submit" id="btn_PrintOthers" class="btn btn-success" value="Print PDF">
					</form>
					
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">CANCEL</a>
			</div>
		</div>
	</div>
</div>

<!--Modal-->
<div id="modal_GenerateSatoSticker" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="ModalDeleteLabel">NEW STICKER(SATO)</h4>
				  
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs" role="tablist">
					  <li class="active" id="li_NormalPSato"><a href="#">Normal</a></li>
					  <li class="" id="li_DynamicPSato"><a href="#">Dynamic</a></li>
					  <li class="" id="li_CustomizedPSato"><a href="#">Customized</a></li>
				</ul>
				<br>
				<div>
					<form method="POST" action="./generate_sticker_info.php" autocomplete="off">
						<input type="hidden" name="txt_reprint" id="txt_reprint" value="0">
						<label style="width:43%;margin-right:2%;">P.O. #:</label><input type="text" id="txtPoNumSATO" name="txtPoNumSATO" style="width:50%;" list="list_PRNo">
						<label style="width:43%;margin-right:1.1%;">Lot #:</label>
						<select id='cmbLotNumberSATO' name='cmbLotNumberSATO' style="width:50%; height:28px;">
						</select>
						<label style="width:43%;margin-right:1.1%;">Packing Type:</label>
						<select id='cmbPackingType' name='cmbPackingType' style="width:50%; height:28px;">
							<option>Box</option>
							<option>Tray</option>
							<option>Polybag</option>
						</select>
						<!--Temporary di sya naka database kase padagdag na lang naman eto...-->
						<label style="width:43%;margin-right:1.1%;">Packed By:</label>
						<select id='cmbPackby' name='cmbPackby' style="width:50%; height:28px;">
							<option>Mhalou</option>
							<option>Albie</option>
							<option>Angelo</option>
							<option>Noriel</option>
							<option>Carlo</option>
							<option>Jerome</option>
						</select>
						<!--<input type="text" id="txtLotNumber" name="txtLotNumber" style="width:50%;" list="list_LotNumber">
						<label style="width:43%;margin-right:1.1%;">Available Qty:</label> <input type='text' id = 'txt_AvailableQty' style="width:50%;" disabled>-->
						
						<div id="normalPrintingSato">
							<label style="width:43%;margin-right:2%;">Qty per box:</label><input type="text" id="txtMaxQty" name="txtMaxQty" style="width:50%;">
							<label style="width:43%;margin-right:2%;">Total Qty:</label><input onkeypress="return isNumberKey(event);" type="text" id="txtShipQty" name="txtShipQty" style="width:50%;">
						</div>
						<label style="width:43%;margin-right:1.1%;">Delivery Place Name:</label>
						<select name="selDeliveryPlaceName" id="dpn" style="width:50%;"><option value="W021">W021</option><option value="W001" <?php echo $edit_mode&&$DPN=="W001" ? "Selected":""; ?>>W001</option></select>
						<label style="width:43%;margin-right:2%;">Shipment Date:</label><input type="date" id="" name="date" style="width:50%;" value="<?php echo $dateToday; ?>">
						<label style="width:43%;margin-right:2%;">Delivery Date:</label><input type="date" id="deliveryDate" name="deliveryDate" style="width:50%;" value="<?php echo $dateToday; ?>">
						<div id="dynamicPrintingSato">
							<label style="width:43%;margin-right:2%;">Stickers Value:(separate by comma[,])</label><input type="text" name="txtNumOfStickers" style="width:50%;">
						</div>
						<div id="customizedPrintingSato">
							<label style="width:43%;margin-right:2%;">Qty:</label><input type="text" id="" name="txtQuantityCustomized" style="width:50%;">
							<label style="width:43%;margin-right:2%;">Total Qty:</label><input onkeypress="return isNumberKey(event);" type="text" id="txtShipQtyCustomize" name="txtShipQtyCustomize" style="width:50%;">
							<label style="width:43%;margin-right:2%;">Package Count(Example:32/55):</label><input type="text" id="txtPackageCount" name="txtPackageCount" style="width:50%;">
						</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="submit" id="btn_PrintSato" class="btn btn-success" value="PRINT">
					</form>					
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">CANCEL</a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">List of Transaction</h4>
      </div>
      <div class="modal-body">
		<!--Group mo ng last update para sa deletion pag nagkamali para rock and roll na hehe-->
        <p>List Of Transaction</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
</html>
<script>
$('document').ready(function(){
	
	$('#dynamicPrinting').hide(); //hide the tab contents on a modal
	$('#customizedPrinting').hide(); //hide the tab contents on a modal
		
	$('#dynamicPrintingSato').hide(); //hide the tab contents on a modal
	$('#customizedPrintingSato').hide(); //hide the tab contents on a modal
	
	var oTable = $('#tbl_Ticketing').dataTable({
			paginate:false,
			"autoWidth": false
	});
	
	$('#tbl_Ticketing').on('click',' tr', function(){
		$('#txt_reprint').val("1");
		//$('#modal_GenerateSatoSticker').modal('show');
	});
	
	$('#btn_NewSticker').click(function(){
		$('#modal_GenerateSticker').modal('show');
	});
	
	$('#btn_NewSatoSticker').click(function(){
		$('#txt_reprint').val("0");
		$('#modal_GenerateSatoSticker').modal('show');
	});
	
	$('#txtPoNumPDF').change(function(){ $('#cmbLotNumberPDF').empty(); loadPRInformationPDF($('#txtPoNumPDF').val()); });
	$('#txtPoNumSATO').change(function(){ $('#cmbLotNumberSATO').empty(); loadPRInformationSATO($('#txtPoNumSATO').val()); });
	$('#txtShipQty').change(function(){ /*validateShipmentQtyMaximumQty();*/ });
	$('#txtMaxQty').change(function(){ /*validateShipmentQtyMaximumQty();*/ });
	$('#txt_PONumberSearch').change(function(){ searchPO(); });
	
	$('#tbl_Ticketing tbody tr').click(function(){ 
		selectTR($(this).find('.id').text(),$(this).find('.PONumber').text(),$(this).find('.OnHand').text(),$(this).find('.ShippedQty').text());
	});
	$('#btn_DeleteSticker').click(function(){ deleteSticker(); });
	
	//Click Functions
	$('#li_NormalP').click(function(){
		$('#li_NormalP').attr('class','active');
		$('#li_DynamicP').attr('class','');
		$('#li_CustomizedP').attr('class','');
		$('#normalPrinting').show();
		$('#dynamicPrinting').hide();
		$('#customizedPrinting').hide();
	});
	$('#li_DynamicP').click(function(){
		$('#li_NormalP').attr('class','');
		$('#li_DynamicP').attr('class','active');
		$('#li_CustomizedP').attr('class','');
		$('#normalPrinting').hide();
		$('#dynamicPrinting').show();
		$('#customizedPrinting').hide();
	});
	$('#li_CustomizedP').click(function(){
		$('#li_NormalP').attr('class','');
		$('#li_DynamicP').attr('class','');
		$('#li_CustomizedP').attr('class','active');
		$('#normalPrinting').hide();
		$('#dynamicPrinting').hide();
		$('#customizedPrinting').show();
	});
	
	//Click Functions SATO
	$('#li_NormalPSato').click(function(){
		$('#li_NormalPSato').attr('class','active');
		$('#li_DynamicPSato').attr('class','');
		$('#li_CustomizedPSato').attr('class','');
		$('#normalPrintingSato').show();
		$('#dynamicPrintingSato').hide();
		$('#customizedPrintingSato').hide();
	});
	$('#li_DynamicPSato').click(function(){
		$('#li_NormalPSato').attr('class','');
		$('#li_DynamicPSato').attr('class','active');
		$('#li_CustomizedPSato').attr('class','');
		$('#normalPrintingSato').hide();
		$('#dynamicPrintingSato').show();
		$('#customizedPrintingSato').hide();
	});
	$('#li_CustomizedPSato').click(function(){
		$('#li_NormalPSato').attr('class','');
		$('#li_DynamicPSato').attr('class','');
		$('#li_CustomizedPSato').attr('class','active');
		$('#normalPrintingSato').hide();
		$('#dynamicPrintingSato').hide();
		$('#customizedPrintingSato').show();
	});
	
});

var idForDelete=0,PONumber,OnHand,ShippedQty;
function selectTR(id,PONumberG,OnHandG,ShippedQtyG){
	$('#tbl_Ticketing tbody tr').removeAttr('class','');
	$('#tbl_Ticketing tbody tr#'+id).attr('class','selected');
	idForDelete = id;
	PONumber 	= PONumberG;
	OnHand 		= OnHandG;
	ShippedQty 	= ShippedQtyG;
	//alert(idForDelete);
}

function deleteSticker(){
	$('#modal_delete .modal-body').empty();
	if(typeof PONumber === 'undefined'){
		$('#modal_delete .modal-body').append("<h1>No Record Selected</h1>");
		$('#modal_delete').modal('show');
		return false;
	}
	
	$('#modal_delete').modal('show');
	
	var data = {
		"action"	: "fetch_ticketing_transaction",
		"po"		: PONumber
	}
	
	data = $.param(data);
	
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "./PPSTicketing-Handler.php",
		success	: function(data){
			var html  = 'Transaction List: '+PONumber;
				html += '<table class="table" id="tbl_delete" border=1>';
				html += '	<thead>';
				html += '		<tr>';
				html += '			<th>Transaction Date</th>';
				html += '			<th>Shipped Qty</th>';
				html += '			<th>Delete</th>';
				html += '		</tr>';
				html += '	</thead>';
				html += '	<tbody>';
				html += '	</tbody>';
				html += '</table>';
				
			$('#modal_delete .modal-body').append(html);
			
			for(ctr=0;ctr<data['ctr'];ctr++){
				var html  = '<tr>';
					html += '	<td>'+data['TransactionDate'][ctr]+'</td>';
					html += '	<td>'+data['ShippedQty'][ctr]+'</td>';
					html += '	<td><button id="btn_delete_trans" class="btn btn-default glyphicon glyphicon-remove" value="'+data['LastUpdate'][ctr]+'" /></td>';
					html += '</tr>';
				$('#tbl_delete tbody').append(html);
			}
			
			$('#tbl_delete').on('click','tbody #btn_delete_trans',function(){
				var last_update = this.value;
				delete_sticker_trans(PONumber,last_update);
			});
		}
	});
}

function delete_sticker_trans(po_num,last_update){
	if(confirm("This transaction will be deleted!")){
		
	}else{
		return false;
	}
	
	var data = {
		"action"		: "delete_sticker_trans",
		"po_number"		: po_num,
		"last_update"	: last_update
	}
	data = $.param(data);
	
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "./PPSTicketing-Handler.php",
		success	: function(data){
			alert(data['result']);
			window.location.reload();
			//deleteSticker();
		}
	});
}


function loadPRInformationPDF(PRNo){
	var data = {
		//"action" 	: "loadPRInformation",
		"action" 	: "loadLotNumber",
		"PRNo"		: PRNo
	}
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "./PPSTicketing-Handler.php",
		success	: function(data){
			var options = $("#cmbLotNumberPDF");
			//options.append($("<option />").val(this.ImageFolderID).text(data));
			var ctr = 0;
			while(ctr < parseInt(data['ctr'])) {
				$('select#cmbLotNumberPDF').append('<option id="lotNumber">'+data['LotNumber'][ctr]+'</option>');
				//options.append(data['LotNumber'][ctr]);
				ctr++;
			}
		}
	});
}

function loadPRInformationSATO(PRNo){
	var data = {
		//"action" 	: "loadPRInformation",
		"action" 	: "loadLotNumber",
		"PRNo"		: PRNo
	}
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "./PPSTicketing-Handler.php",
		success	: function(data){
			var options = $("#cmbLotNumberSATO");
			//options.append($("<option />").val(this.ImageFolderID).text(data));
			var ctr = 0;
			while(ctr < parseInt(data['ctr'])) {
				$('select#cmbLotNumberSATO').append('<option id="lotNumber">'+data['LotNumber'][ctr]+'</option>');
				//options.append(data['LotNumber'][ctr]);
				ctr++;
			}
		}
	});
}

function isNumberKey(evt){ //checks if the pressed key is a number
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	
	return true;
}

function reprint_data(){
	//$('#modal_GenerateSatoSticker').modal('show');
}

/*
disableButton();
function disableButton(){
	$('#btn_PrintOthers').attr('disabled','');
}

function enableButton(){
	$('#btn_PrintOthers').removeAttr('disabled','');
}

function validateShipmentQtyMaximumQty(){
	var AvailableQty = $('#txt_AvailableQty').val();
	var ShipQty = $('#txtShipQty').val();
	var MaxQty = $('#txtMaxQty').val();
	
	if(ShipQty == "" || MaxQty=="" || ShipQty == "0" || MaxQty=="0" || parseFloat(MaxQty) > parseFloat(ShipQty)){
		disableButton();
	}else{
		enableButton();
	}
	
}
*/
</script>