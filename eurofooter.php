<?php



?>



<div class="footer" id="footer_1">
	
	<div class="view_mode"> <span id="res_a"></span><span id="res"></span></div>
	<footer class="clear">
		
		<h2><?php if(isset($domain_name)) echo strtoupper($domain_name); ?> - HELP AND BE HELPED <a href="register" class="links all_abtn">JOIN US NOW</a></h2><br/><br/>		
		<div class="qcklnks">
			<h3>QUICK LINKS</h2>
			<a class="links" href="register">REGISTER</a> |
			<a class="links" href="login">LOGIN</a> |
			<a class="links" href="how-it-works">HOW IT WORKS</a> |
			<a class="links" href="testimonials">TESTIMONIES</a> |
			<a class="links" href="contact-support">CONTACT US</a> |
			<a class="links" href="about">ABOUT US</a> |
			<a class="links" href="faq">FAQ</a> |
			<a class="links" href="policies">POLICIES</a>
		</div>	
		<div class="qcklnks">
			<h3>COMODO SECURED</h3>
			<img alt="icon" class="img_type6" src="wealth-island-images/icons/comodo_secure.png" />
		</div>
	</footer>
</div>
<div class="footer" id="footer_2">
	
	<div class="view_mode"> <span id="res_a"></span><span id="res"></span></div>
	<footer >
		
		<a href="<?php if(isset($domain_name)) echo $getdomain; ?>"  class="links"><?php if(isset($domain_name)) echo $domain_name; ?></a>-Copyright &copy <?php echo Date('Y')  ?> All Rights Reserved
		<br/>Please see <a class="links" href="terms-and-condition">Terms and Conditions</a>
	
	</footer>
</div>
