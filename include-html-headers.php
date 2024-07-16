<?php


?>

<?php if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "provident-funds.test"){ ?>
		
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
		<meta http-equiv="encoding" content="utf-8" >	
		<meta name="keywords" content="<?php if(isset($domain_name)) echo $domain_name?>, Get Rich, Best Foundation, Climbing up financial ladder, Financial freedom, Donation, Fund raising, Giving and Receiving, Charity, Community, Wealth Creation, Time Freedom, Making Money, Money Making Machine"/>
		<meta name="description" content="<?php if(isset($domain_name)) echo $domain_name?> fast peer-to-peer donation platform, make 200% on or before 21 days as your return on investment on any of our packages. At <?php if(isset($domain_name)) echo $domain_name?> we help you climb up the financial freedom ladder.">
		<meta name="author" content="<?php if(isset($domain_name)) echo $domain_name?> Nigeria">
		<meta  name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" type="text/css" href="http://provident-funds.test/wealth-island_style_sheet.css"></link>
		<link rel="stylesheet" type="text/css" href="http://provident-funds.test/pf-main-style-resp.css"></link>
		<link rel="stylesheet" type="text/css" href="http://provident-funds.test/pf-poli-style-resp.css"></link>
		<link rel="shortcut icon" type="image/png" href="http://provident-funds.test/wealth-island-images/icons/pf-fav.png" />
		<script   type="text/javascript" src="http://provident-funds.test/jquery-v3.2.0-min.js" ></script>
		<!-- <script   type="text/javascript" src="http://provident-funds.test/jquery.geo.rc1.1.js" ></script> -->
		<script   type="text/javascript" src="http://provident-funds.test/wealth-island_scripts.js" ></script> 

		<script>


		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-83881732-1', 'auto');
		  ga('send', 'pageview');
		  ga('set', 'userId', {{USER_ID}}); // Set the user ID using signed-in user_id.

		</script>

<?php }else{ ?>

		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
		<meta http-equiv="encoding" content="utf-8" >	
		<meta name="keywords" content="<?php if(isset($domain_name)) echo $domain_name?>, Get Rich, Best Foundation, Climbing up financial ladder, Financial freedom, Donation, Fund raising, Giving and Receiving, Charity, Community, Wealth Creation, Time Freedom, Making Money, Money Making Machine"/>
		<meta name="description" content="<?php if(isset($domain_name)) echo $domain_name?> fast peer-to-peer donation platform, make 200% on or before 21 days as your return on investment on any of our packages. At <?php if(isset($domain_name)) echo $domain_name?> we help you climb up the financial freedom ladder.">
		<meta name="author" content="<?php if(isset($domain_name)) echo $domain_name?> Nigeria">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="Content-Security-Policy" content="block-all-mixed-content">
		<?php if(connectionSecured() == "https"){ ?>		
		<link rel="stylesheet" type="text/css" href="https://www.provident-funds.com/styles/main/css/wealth-island_style_sheet.css" />
		<link rel="stylesheet" type="text/css" href="https://www.provident-funds.com/styles/main/css/pf-main-style-resp.css" />
		<link rel="stylesheet" type="text/css" href="https://www.provident-funds.com/styles/main/css/pf-poli-style-resp.css" />
		<link rel="shortcut icon" type="image/png" href="https://www.provident-funds.com/wealth-island-images/icons/pf-fav.png" />
		<script   type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js" ></script>
		<!--<script   type="text/javascript" src="http://www.provident-funds.com/js/main/jquery-v3.2.0-min.js" ></script>-->
		<script   type="text/javascript" src="https://www.provident-funds.com/js/main/wealth-island_scripts.js" ></script>

		<?php  }elseif(connectionSecured() == "http"){ ?>		
		<link rel="stylesheet" type="text/css" href="http://www.provident-funds.com/styles/main/css/wealth-island_style_sheet.css" />
		<link rel="stylesheet" type="text/css" href="http://www.provident-funds.com/styles/main/css/pf-main-style-resp.css" />
		<link rel="stylesheet" type="text/css" href="http://www.provident-funds.com/styles/main/css/pf-poli-style-resp.css" />
		<link rel="shortcut icon" type="image/png" href="http://www.provident-funds.com/wealth-island-images/icons/pf-fav.png" />
		<script   type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js" ></script>
		<!--<script   type="text/javascript" src="http://www.provident-funds.com/js/main/jquery-v3.2.0-min.js" ></script>-->
		<script   type="text/javascript" src="http://www.provident-funds.com/js/main/wealth-island_scripts.js" ></script>

		<?php  } ?>




<?php } ?>