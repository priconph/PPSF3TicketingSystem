<?php
require_once('./includes.php');
?>
<html>
<title>PPS Database System</title>
<head>
<link href="./css/ppsStyleSheet.css" rel="stylesheet">
<style>
#div-1 {
 position:relative;
}
#div-1a {
 float:left;
 top:0;
 right:0;
 width:33%;
 height:80%;
 border: 2px solid;
 border-radius:3%;
}
#div-1b {
 float:left;
 top:0;
 left:0;
 width:33%;
 height:80%;
 border: 2px solid;
 border-radius:3%;
 }
#div-1c {
 float:left;
 top:0;
 left:0;
 width:34%;
 height:80%;
 border: 2px solid;
 border-radius:3%;	
}
</style>
<script>
$('document').ready(function(){
	var oTable = $('#TableID HERE').dataTable({
			paginate:false,
			"autoWidth": false
	});
});
</script>
</head>
<body>
	
<div id="div-1">
	HOME PAGE! RESERVED FOR FUTURE DEVELOPMENT
</div>

<!--Modal-->
<div id="modalNameHere" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true" style="overflow:inherit">
	 <div class="modal-dialog">
		<div class="modal-content" style="width:400px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="ModalDeleteLabel">TITLE</h4>
			</div>
			<div class="modal-body">
				<p>CONTENT</p>
			</div>
			<div class="modal-footer">
			   <a href="#" id="btnYes" class="btn btn-success">YES</a>
			   <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">NO</a>
			</div>
		</div>
	</div>
</div>
</body>
</html>