<?php
//require_once("./includes.php");
//Gets the mode of the transaction. Either "Edit" or "New" or "Delete"
	$mode=$_GET["Mode"];
	$edit_mode=($mode=="Edit");
	$department=$_GET["department"];//Department 1=TS 2=CN 3=YF
	
	$PONum="";//Product Number
	$ShipQty="";//Shipment quantity
	$MaxQty="";//Quantity per box
	$DPN="";//Delivery Place name
	$ShipDate="";//Date of shipment
	
	if($edit_mode)//If mode is "Edit"
	{
		$PONum=$_GET["PONum"];//Product Number
		$ShipQty=$_GET["SQty"];//Shipment QTY
		$MaxQty=$_GET["MQty"];//QTY per Box
		$DPN=$_GET["DPN"];//Delivery Place Name
		$date=$_GET["ShipDate"];//Shipment date
		$transNum=$_GET["Trans"];//Transaction number
		$ShipDate=date_parse_from_format('M-d-Y',$date );//Formats the shipment date
	}
	
	
	
	
$dbName="CN_YPICS1";//MSSQL database name
$user="builder";//MSSQL username
$pass="";//MSSQL password
$host="192.168.3.251\MSSQLSERVER";//MSSQL host
$viewName="vwDLabel";//MSSQL table/view
if($department==1)
{
	$viewName="vwDLabel_TS";
}
else if($department==2)
{
	$viewName="vwDLabel_CN";
}
else
{
	$viewName="vwDLabel_YF";
}
$conn=mssql_connect($host,$user,$pass);//MSSQL Connection
$db_select=mssql_select_db($dbName,$conn) or die("Could not connect to DB");//Selection of database
$query=mssql_query("select * from ".$viewName);//MSSQL Query
?>
<html>
	<head>
		<title>DLabel Sticker</title>
		<style type="text/css">
			body{
				background-color:#1D3800;
				color:#ffffff;
				font-family:"Franklin Gothic Demi";
			}
			#wrapper{
				/*margin: 0 auto;
				width: 800px;
				border-color:#000000;*/
			}
			table{
				padding-top:50px;
				/*align:center;
				margin:0 auto;
				margin-bottom: 10px;
				border-color:#000000;
				border-collapse: collapse;
				border-spacing: 0;
				font-size:20px;*/
			}
			input[type="text"],select{
				width:200px;
				border:1px solid #ffffff;
				font-size:20px;
			}
			input[type="submit"],input[type="button"]{
				font-family:"Franklin Gothic Demi";
				background-color:#ffffff;
				border:1px solid #ffffff;
				font-size:20px;
			}
			
			#PONUMS
			{
				overflow:scroll;
				height:100px;
			}
			
			#loading{position:fixed;top:50%;left:50%;z-index:1104;}
		</style>
		
		<link rel="stylesheet" href="DatePicker/jquery-ui.css">
		<script type="text/javascript" src="js/deployJava.js"></script>
		<script src="DatePicker/jquery-1.10.2.js"></script>
		<script src="DatePicker/jquery-ui.js"></script>
		<!--<script src="./media/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="./media/css/bootstrap.css">-->
		<script>
		
		//MJ added features for the revision
		function validateNumOfStickers(){
			
		}
		
		//MJ Added Function
		function ModalForDelete(){
			if (confirm('Delete this data?')) {
				delete_sticker();
			} else {
				//alert("CANCELLED!");
			}
		}
		
		function delete_sticker(){
			var poNum = "<?php echo $PONum;  ?>";
			//alert(poNum + "will be deleted!");
			var data = {
			"action":"deleteSticker",
			"PoNum":poNum
			}
			//alert(data['action']);
			data = $.param(data);
			$.ajax({
				type:"post",
				dataType:"json",
				data:data,
				url:"addedFeature.php",
				success: function(data){
					alert("file has been deleted");
					document.getElementById("txtPoNum").value="";
					document.getElementById("txtMaxQty").value="";
					document.getElementById("txtShipQty").value="";
					window.top.close();
				}
			});
		}
		/////////////////////////////////

		$(document).ready(function(){
			$("#loading").hide();//hides the loading gif if the page is read
		});
			
		
		$(function() {//sets the date picker
			$( "#datepicker" ).datepicker({
			dateFormat: 'ddmmyy',
			showButtonPanel: true,
			minDate: 0,
			maxDate: '+30Y'}).datepicker("setDate", <?php echo ($edit_mode)?'new Date("'.$date.'")':'"0"';?>);
		});
		
		$(document).on("keypress", ".TabOnEnter" , function(e){
			//Only do something when the user presses enter
			if( e.keyCode ==  13 )
			{
			   var nextElement = $('[tabindex="' + (this.tabIndex+1)  + '"]');
			   console.log( this , nextElement ); 
			   if(nextElement.length )
				 nextElement.focus()
			   else
				 $('[tabindex="1"]').focus();  
			}   
		  });
		
		function isNumberKey(evt){//checks if the pressed key is a number
			var charCode = (evt.which) ? evt.which : event.keyCode
			if (charCode > 31 && (charCode < 48 || charCode > 57))
				return false;
			
			return true;
		}
		
		function goNext(evt){
			var charCode = (evt.which) ? evt.which : event.keyCode
			if (charCode == 13) {
				evt.target.nextSibling.focus();
				evt.preventDefault();
			}
		}
		
		</script>
		<script>
		var stickers=new Array();//creates an array to store the sticker ZPL string codes
		deployQZ();//deploys the QZapplet for printing
		
		function validateInput(numberIdentifier){//input validation
			var eachQty = $('#txtQtyPerBox').val().split(',');
			var shipmentQty = parseInt($('#txtShipQty').val());
			var customizedQty = parseInt($('#txtQuantityCustomized').val());
			var totalQty = 0;
			var ctr = 0;
			while (ctr < eachQty.length)
			{
				totalQty = parseInt(totalQty) + parseInt(eachQty[ctr]) ;
				ctr++;
			}
			
			if($('#txtNumOfStickers').val() != eachQty.length && $('#txtNumOfStickers').val() != ""){
				$("#messenger").html("Please Check \n Num of Stickers = "+ $('#txtNumOfStickers').val() + "\n Number of assigned stickers = "+eachQty.length);
				return;
			}
			
			if(totalQty > shipmentQty || totalQty < shipmentQty && $('#txtNumOfStickers').val() != ""){
				$("#messenger").html("Please check total quantity per box = " +totalQty +" Shipment Quantity = " + shipmentQty);
				$('#txtQtyPerBox').setFocus();
				return;
			}
			if(customizedQty > shipmentQty && $('#txtPackageCount').val() != ""){
				$("#messenger").html("Please check quantity= " +customizedQty +" Shipment Quantity = " + shipmentQty);
				$('#txtQtyPerBox').setFocus();
				return;
			}
			
				var txtPoNum=document.getElementById("txtPoNum").value;
				var txtMaxQty=document.getElementById("txtMaxQty").value;
				var txtNumOfStickers=document.getElementById("txtNumOfStickers").value;
				//var txtQtyPerBox=document.getElementById("txtQtyPerBox").value;
				var txtQtyPerBox=$('#txtQtyPerBox').val();
				//customized printing
				var txtPackageCount=$('#txtPackageCount').val();
				var txtPackageQty=$('#txtQuantityCustomized').val();
				///////////////////
				var txtShipQty=document.getElementById("txtShipQty").value;
				var txtDate=document.getElementById("datepicker").value;
				var txtDPN=document.getElementById("dpn").value;
				var isOk=false;//so it's not ok.....so don't mess with it
				//MJ Revision
				//alert(txtQtyPerBox + " " +txtNumOfStickers);
				//if(txtPoNum==""||txtPoNum.length!=10)//if the product number is not equal to 10 digits. //reserve
				if(txtPoNum=="")//if the po is blank
				{
					document.getElementById("txtPoNum").focus();
					$("#messenger").html("Not a valid PO#.Please enter a valid PO#.");
					return false;
				}
				else if(txtMaxQty==""||txtShipQty==""){//if the fields Max qty and Ship qty is blank
					$("#messenger").html("Please fill in all fields correctly.");
					return false;
				 }
				 
					 //ajax get (this ajax goes to the validateData.php)
					 $.get("validateData.php",{mode:<?php echo '"'.$mode.'"';?>,department:<?php echo '"'.$department.'"';?>,Trans:<?php echo '"'.$transNum.'"';?>,PONum:txtPoNum,MaxQty:txtMaxQty,NumOfStickers:txtNumOfStickers,QtyPerBox:txtQtyPerBox,date:txtDate,dpn:txtDPN,ShipQty:txtShipQty},function(data){
						$("#messenger").html(data);
						if(data=="Done."||data=="Updated."){//if the data returned by ajax is equal to "Done." or "Updated."
						
						if(numberIdentifier==0){
						$("#messenger").html("printing...");
								//ajax get (this ajax gets the sticker ZPL code from getStickerDetails.php)
								$.get("getStickerDetails.php",{txtPoNum:txtPoNum,department:<?php echo '"'.$department.'"';?>,txtMaxQty:txtMaxQty,date:txtDate,selDeliveryPlaceName:txtDPN,txtShipQty:txtShipQty,txtNumOfStickers:txtNumOfStickers,txtQtyPerBox:txtQtyPerBox,txtPackageCount:txtPackageCount,txtPackageQty:txtPackageQty},function(data2){
								//alert(data2);
								stickers=data2.split(":-:");//the data returned by this ajax would be the sticker ZPL codes. If the sticker is more than one, the stickers are separated by the delimeter ":-:"
								//$("#messenger").html(stickers.length);
								//$("#messenger").html(data2);
								//alert(data2);
								$("#submitButton").removeAttr("disabled");
								findPrinter("ZPL");//calls the findprinter function passing the string "ZPL"
								window.top.close();//calls the close function from the top window...or the index.php
								document.getElementById("txtPoNum").value="";
								document.getElementById("txtMaxQty").value="";
								document.getElementById("txtShipQty").value="";
							});
						}else
						 {
							//alert("here");
							$("#frmInput").submit();
							document.getElementById("txtPoNum").value="";
							document.getElementById("txtMaxQty").value="";
							document.getElementById("txtShipQty").value="";
							window.top.close();//calls the close function from the top window...or the index.php
						 }
						}
						$("#submitButton").removeAttr("disabled");
					 });
					 $("#messenger").html("please wait...");
					 $("#submitButton").attr("disabled", "disabled");
				  
				return true;
			
		}
		
		
			/**
			* Deploys different versions of the applet depending on Java version.
			* Useful for removing warning dialogs for Java 6.  This function is optional
			* however, if used, should replace the <applet> method.  Needed to address 
			* MANIFEST.MF TrustedLibrary=true discrepency between JRE6 and JRE7.
			*/
			function deployQZ() {
				var attributes = {id: "qz", code:'qz.PrintApplet.class', 
				archive:'LabelPrinter/qz-print.jar', width:1, height:1};
				var parameters = {jnlp_href: 'LabelPrinter/qz-print_jnlp.jnlp', 
					cache_option:'plugin', disable_logging:'false', 
					initial_focus:'false'};
				if (deployJava.versionCheck("1.7+") == true) {}
				else if (deployJava.versionCheck("1.6+") == true) {
					attributes['archive'] = 'LabelPrinter/jre6/qz-print.jar';
					parameters['jnlp_href'] = 'LabelPrinter/jre6/qz-print_jnlp.jnlp';
				}
				deployJava.runApplet(attributes, parameters, '1.5');
			}
			/**
			* Automatically gets called when applet has loaded.
			*/
			function qzReady() {
				// Setup our global qz object
				window["qz"] = document.getElementById('qz');
				var title = document.getElementById("title");
				if (qz) {
					try {
						title.innerHTML = title.innerHTML + " " + qz.getVersion();
						document.getElementById("content").style.background = "#F0F0F0";
					} catch(err) { // LiveConnect error, display a detailed meesage
						document.getElementById("content").style.background = "#F5A9A9";
						alert("ERROR:  \nThe applet did not load correctly.  Communication to the " + 
							"applet has failed, likely caused by Java Security Settings.  \n\n" + 
							"CAUSE:  \nJava 7 update 25 and higher block LiveConnect calls " + 
							"once Oracle has marked that version as outdated, which " + 
							"is likely the cause.  \n\nSOLUTION:  \n  1. Update Java to the latest " + 
							"Java version \n          (or)\n  2. Lower the security " + 
							"settings from the Java Control Panel.");
				  }
			  }
			}
			
			/**
			* Returns whether or not the applet is not ready to print.
			* Displays an alert if not ready.
			*/
			function notReady() {
				// If applet is not loaded, display an error
				if (!isLoaded()) {
					return true;
				}
				// If a printer hasn't been selected, display a message.
				else if (!qz.getPrinter()) {
					$("#messenger").html('Please select a printer first by using the "Detect Printer" button.');
					return true;
				}
				return false;
			}
			
			/**
			* Returns is the applet is not loaded properly
			*/
			function isLoaded() {
				if (!qz) {
					$("#messenger").html('Error:\n\n\tPrint plugin is NOT loaded!');
					return false;
				} else {
					try {
						if (!qz.isActive()) {
							$("#messenger").html('Error:\n\n\tPrint plugin is loaded but NOT active!');
							return false;
						}
					} catch (err) {
						$("#messenger").html('Error:\n\n\tPrint plugin is NOT loaded properly!');
						return false;
					}
				}
				return true;
			}
			
			/**
			* Automatically gets called when "qz.print()" is finished.
			*/
			function qzDonePrinting() {
				// Alert error, if any
				if (qz.getException()) {
					alert('Error printing:\n\n\t' + qz.getException().getLocalizedMessage());
					qz.clearException();
					return; 
				}
				
				// Alert success message
				//alert('Successfully sent print data to "' + qz.getPrinter() + '" queue.');
			}
			
			/***************************************************************************
			* Prototype function for finding the closest match to a printer name.
			* Usage:
			*    qz.findPrinter('zebra');
			*    window['qzDoneFinding'] = function() { alert(qz.getPrinter()); };
			***************************************************************************/
			
			//WHAT I CHANGED.....what I changed from this function is that after isLoaded() returns true and finding the printer the printZPL() gets called
			function findPrinter(name) {
			
				if (isLoaded()) {
					// Searches for locally installed printer with specified name
					qz.findPrinter(name);
					
					// Automatically gets called when "qz.findPrinter()" is finished.
					window['qzDoneFinding'] = function() {
						var printer = qz.getPrinter();
						
						printZPL();//printing ZPL
						// Remove reference to this function
						window['qzDoneFinding'] = null;
					};
				}
			}
			
			function printZPL() {//function for printing
				
				if (notReady()) { return; }
				$("#messenger").html("printing...");
				for(var i=0;i<stickers.length;i++)
				{
					
					qz.append(stickers[i]);//sets which printer to print
					qz.print();//prints the stickers
				}
				$("#messenger").html("Done.");
			}
			
			/***************************************************************************
			* Gets the current url's path, such as http://site.com/example/dist/
			***************************************************************************/
			function getPath() {
				var path = window.location.href;
				return path.substring(0, path.lastIndexOf("/")) + "/";
			}
			
			/**
			* Equivelant of VisualBasic CHR() function
			*/
			function chr(i) {
				return String.fromCharCode(i);
			}
			
		</script>
	</head>
	<body>
		<img id="loading" alt="" src="images/ajax.gif"/>
		<div id="wrapper">
			<?php 
				//echo ($edit_)?"<h1>EDIT</h1>":"<h1>NEW</h1>";
				echo "<h1>".$mode."</h1>";
			?>
			<form action="PDFViewer.php" method="post" target="_blank" name="frmInput" id="frmInput">
				<input type="hidden" name="department" value="<?php echo $department;?>" >
				<table>
					<tr>
						<td align="right">
							PO#:
						</td>
						<td>
							<!--onkeypress="return isNumberKey(event);" used for blocking alphanumeric inputs--> 
							<input type="text"  list="PONUMS" value=<?php echo $edit_mode ? '"'.$PONum.'" readonly':'""';?> name="txtPoNum" id="txtPoNum"  maxlength="10" class='TabOnEnter' tabindex="1" autocomplete="off">
							
							<datalist id="PONUMS" >
								<?php
									while($row=mssql_fetch_array($query))
									{
										echo '<option value="'.str_replace("00010","",$row["PONum"]).'"> ';echo "\r\n";
									}
								?>
							</datalist>
							
						</td>
						
					</tr>
					<tr>
						<td align="right">
							Shipment Qty:
						</td>
						<td>
							<input type="text"  value="<?php echo $edit_mode ? $ShipQty:"";?>" name="txtShipQty" id="txtShipQty"  onkeypress="return isNumberKey(event);" class='TabOnEnter' tabindex="2">
						</td>
					</tr>
					<tr>
						<td align="right">
							Maximum Qty:
						</td>
						<td>
							<input type="text"  value="<?php echo $edit_mode ? $MaxQty:"";?>" name="txtMaxQty" id="txtMaxQty"  onkeypress="return isNumberKey(event);" class='TabOnEnter' tabindex="3">
						</td>
					</tr>
					<tr>
						<td align="right">Delivery place name:</td>
						<td><select name="selDeliveryPlaceName" id="dpn" class='TabOnEnter' tabindex="4" ><option value="W021">W021</option><option value="W001" <?php echo $edit_mode&&$DPN=="W001" ? "Selected":""; ?>>W001</option></select></td>
					</tr>
					<tr>
						<td align="right">
							Shipment Date:
						</td>
						<td>
							<input type="text" name="date" id="datepicker" readonly='true' class='TabOnEnter' tabindex="5">
						</td>
					</tr>
					<tr>
					<td><center>OPTIONAL</center></td>
					</tr>
					<tr>
						<td align="right">
							Number Of Stickers:
						</td>
						<td>
							<input type="text"  value="" name="txtNumOfStickers" id="txtNumOfStickers"  onkeypress="return isNumberKey(event);" class='TabOnEnter' tabindex="6">
						</td>
					</tr>
					<tr>
						<td align="right">
							Quantity Per Box:
						</td>
						<td>
							<input type="text"  value="" name="txtQtyPerBox" id="txtQtyPerBox"  onchange="validateNumOfStickers();" class='TabOnEnter' tabindex="7">
						</td>
					</tr>
					<td><center>Customized</center></td>
					</tr>
					<tr>
						<td align="right">
							Package Count:
						</td>
						<td>
							<input type="text"  value="" name="txtPackageCount" id="txtPackageCount"  onkeypress="" class='TabOnEnter' tabindex="8">
						</td>
					</tr>
					<tr>
						<td align="right">
							Qty:
						</td>
						<td>
							<input type="text"  value="" name="txtQuantityCustomized" id="txtQuantityCustomized"  onchange="" class='TabOnEnter' tabindex="9">
						</td>
					</tr>
					<tr>
						<td align="right">
							Print:
						</td>
						<td align="left">
							<!--<input type="button" value="Save" id="submitButton" name="btnSearch" onclick="return validateInput();" class='TabOnEnter' tabindex="6">-->
							<input type="button" value="ZPL" id="submitButton" name="btnSearch" onclick="return validateInput(0);" class='TabOnEnter' tabindex="10">
							<input type="button" value="Others" id="submitButton" name="btnSearch" onclick="return validateInput(1);" class='TabOnEnter' tabindex="10">
							
						</td>
					</tr>
				</table>
			</form>
							
							
							<?php
								//Delete Function added by MJ
								if($edit_mode == 'Edit'){
									echo '<input style="margin-left:150px;" type="button" value="Delete" onclick="return ModalForDelete()" id="submitButton" name="btnSearch" class="TabOnEnter" tabindex="9" disable>';
								}
							?>
			
			
			<div id="messenger"></div>
			<applet id="qz" archive="LabelPrinter/qz-print.jar" name="QZ Print Plugin" code="qz.PrintApplet.class" width="0" height="0" >
			<param name="jnlp_href" value="LabelPrinter/qz-print_jnlp.jnlp">
			<param name="cache_option" value="plugin">
			<param name="disable_logging" value="false">
			<param name="initial_focus" value="false">
			</applet>
		</div>
	</body>
</html>
