<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
		<link rel="stylesheet" href="__PUBLIC__/css/errorCss.css" />
		<title>Something went wrong</title>
	</head>
	<body>
		<div class="con">
	    	<div class="con_cn">
	        	<h2><img src="__PUBLIC__/images/error.png"><?php echo($error); ?></h2>
	            <p>page auto<a href="<?php echo($jumpUrl); ?>" id="href">Jump </a> wait time，<strong id="wait"><?php echo($waitSecond); ?></strong>seconds</p>
	            <div class="but">
	            	<a href="<?php echo($jumpUrl); ?>" class="btn-success btn-minw">jump now</a>
	                <a href="javascript:noJump();" class="btn-warning btn-minw">No jumps</a>
	            </div>
	       </div>
	    </div>
	</body>
	<script type="text/javascript">
	var interval;
	(function(){
		var wait = document.getElementById('wait'),href = document.getElementById('href').href;
		interval = setInterval(function(){
			var time = --wait.innerHTML;
			if(time <= 0) {
				location.href = href;
				clearInterval(interval);
			};
		}, 1000);
	})();
	function noJump(){
		clearInterval(interval);
	}
	</script>
</html>
