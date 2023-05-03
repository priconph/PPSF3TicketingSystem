<?php
require_once('./includes.php');
//SET IP RESTRICTIONS
/*
$PCIP = $_SERVER['REMOTE_ADDR'];
if($PCIP != '192.168.20.103') {
header('Location: ../');
}
*/
?>
<html>
<title>PPS Database System</title>
<head>
<link href="./css/ppsStyleSheet.css" rel="stylesheet">
<script>
$('document').ready(function(){
	$('#li_Ticketing').click(function(){
		$('#iFrm_Main').attr('src', "./Ticketing.php");
		$('#li_Ticketing').attr('class', "active");
	});
});

</script>

</head>
<body>
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="./index.php">PPS Ticketing</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="" id="li_Ticketing"><a href="#">LABEL STICKER</a></li>
        </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="">Reserved</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<iframe src="./Ticketing.php" id="iFrm_Main" style="padding:1%;"></iframe>

</body>
</html>