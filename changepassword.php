<?php  
 
session_start();
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
$getdomain = getDomain();
$domain_name = getDomainName();

setPageTimeZone();

$newpass1_no_sha1="";$newpass2_no_sha1="";$oldpass_no_sha1=""; $incnpass="";$ioldpass="";$blanksnewpwd1="";
$blanksnewpwd2="";$blanksoldpwd="";$incnpass_space="";$nullFields="";$inc_newpass_tshort="";
$oldpwd_field_err="";$npwd1_field_err="";$npwd2_field_err="";

$username = $_SESSION['username'];

		
	$page_self = getReferringPage("qstr url");
	//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////
	
	if(isset($_COOKIE["form_gate_response"])){
		
		$pchangesucc = $_COOKIE["form_gate_response"];
		
		
	/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

			setcookie("form_gate_response", "", (time() -  1800));

	}



////////////////HANDLE FOR IDENTIFYING FORGOT PASSWORD RESET LINK//////////////////////////////////////////////////////////////////
if((isset($_GET["cpi"]) && isset($_GET["email"])) || isset($_POST["oldpass_cpi"])){
	
	if(isset($_GET["cpi"]) && isset($_GET["email"])){
			
		$holdoldpwd = protect($_GET["cpi"]);
		$token = protect($_GET["email"]);
		$oldpass_cpi = true;
	}
	
	
	if(isset($_POST["oldpass_cpi"])){
		$holdoldpwd = protect($_POST["oldpass_cpi"]);
		$token = protect($_POST["token"]);
		$oldpass_cpi = true;
		
	}
	
	///////////PDO QUERY////////////////////////////////////	

	$sql =  "SELECT USERNAME FROM members WHERE EMAIL=? LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);
	$stmt->execute(array($token));
						
	 $rowz = $stmt->fetch(PDO::FETCH_ASSOC);
	 $username = $rowz['USERNAME'];
	 
}

////////ON CHANGEPASSWORD FORM SUBMIT///////////////////////////////////////
 
 if(isset($_POST['changepass'])){
		
	 $newpass1=$_POST['newpass1'];
	 
	 $newpass1_no_sha1=$newpass1;

	 $newpass2=$_POST['newpass2'];
	 
	 $newpass2_no_sha1=$newpass2;
	if(isset($_POST["oldpass"]))
		$oldpass=protect($_POST['oldpass']);
	if(isset($_POST["oldpass_cpi"]))
		$oldpass=protect($_POST['oldpass_cpi']);

	$oldpass_no_sha1=$oldpass;
	 
	 /*
	 $newpass1=trim(" ","",$newpass1);
	 $newpass2=trim($newpass2);
	 */

	 //////CHECK FOR SPACES IN PASSWORD/////////////////////////////
	 $spacechkpwd1 = strpos($newpass1," ");
	 $spacechkpwd2 = strpos($newpass2," ");
	 $chkindexpwd1 = substr($newpass1,0,1);
	 $chkindexpwd2 = substr($newpass1,0,1);
	 if($chkindexpwd1 == " " || $chkindexpwd2 == " " || $spacechkpwd1 || $spacechkpwd2){
		 $spacechkpwd = true;
	 }
	 
	 
	 
	 if($username){
		 			 
			 if($newpass1   && $newpass2  && $oldpass ){
			 
					if($spacechkpwd == false){
					 					 
							
						///////////PDO QUERY////////////////////////////////////	
			
						$sql =  "SELECT * FROM members WHERE USERNAME=? LIMIT 1";

						$stmt1 = $pdo_conn_login->prepare($sql);
						$stmt1->execute(array($username));
											
						 $row = $stmt1->fetch(PDO::FETCH_ASSOC);
						 $dboldpass=$row['PASSWORD'];
						 $useremail=$row['EMAIL'];


						////////////ENCRYPT PASSWORD

						 $newpass1mailed=$newpass1;
						 $newpass1=sha1( $newpass1);
						 $newpass2=sha1( $newpass2);
						 
						 if(!isset($oldpass_cpi))
							$oldpass=sha1($oldpass);
						
						 else{/////DONT USE SHA1 AGAIN SINCE CPI PASSED IS ALREADY ON SHA1////////////
							
							$oldpass = $oldpass;
						 }

						
						///////////////////////////////////

						  if($newpass1==$newpass2){
							  
							if(strlen($newpass1_no_sha1) >= 6){
								 
								if($oldpass==$dboldpass){	


						///////////PDO QUERY////////////////////////////////////	
								
											$sql = "UPDATE members SET PASSWORD=? WHERE USERNAME=? ";

											$stmt2 = $pdo_conn_login->prepare($sql);
											
											

						
							 if($stmt2->execute(array($newpass1,$username))){
								 
								 $to=$useremail;
								 $subject = $getdomain."[PASSWORD RESET]";
								 
								 $message="Hello ".$username."\n your password has been successfully reset.\n\n your new password is: ".$newpass1mailed
													."\n you can now proceed to  <a class='links' href='".$getdomain."/login'>Login</a> \n\nPlease do keep your details safe\nThank you\n\n\n\n\n";
											
								 $footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ."  All Rights Reserved.
													 NOTE: This email was sent to you because your account password registered with this Email at ".$getdomain." was reset. If you
													  did not request for a password reset kindly ignore this message.\n\n\n please do not reply to this email.";
								 $headers="from: webmaster@".$getdomain."\r\n";
								 sendHTMLMail($to,$subject,$message,$footer,$headers);
								 
								 
								 
								 
								 $pchangesucc="<span style=color:cyan>".$username."</span><span class=blue> your password has been changed successfully.
								<a class=links href='".$getdomain."'>click here to go back to Home</a><br/>Also An email containing your new details has been dispatched
								 to your email address:<br/><a class='links' href='mailto:".$useremail."'> ".$useremail."</a>, it will arrive shortly.</br/> please do keep your details safe<br/>Thank you</span>";
								 
															 
								////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
								echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($pchangesucc)."')</script>";
									
								 
								 
							 }	 


						}
						 
						 
						///////////IF OLD PASSWORD ENTERED IS NOT EQUAL TO OLD PASSWORD IN DB/////////////////////////////////////////////////////////////////////////////////////////////////////////  
						 
						 else{
							 
							 
							 $oldpwd_field_err = "field_err";
							 $ioldpass="<span class=asterix>*</span>";
							 
							 $holdnewpwd1=$newpass1_no_sha1;
							 $holdnewpwd2=$newpass2_no_sha1;

						/////////////////////FOCUS ON OLD PASSWORD FIELD//////////////////////////////////////////////////////////////
							 

						echo "<script> location.assign('#op')</script>";

							 
							 
							}
						 
						 }
						 
						//////////IF NEW PASSWORD LENGTH IS TOO SHORT//////////////////////////////////////////////////////////////////////////////////////////////////////
						  
						  else{
							  
							  $npwd1_field_err = "field_err";
							  $npwd2_field_err = "field_err";
							  $holdoldpwd=$oldpass_no_sha1;
							  $inc_newpass_tshort="<span class=asterix>*</span>";

						/////////////////////FOCUS ON FIRST NEW PASSWORD FIELD//////////////////////////////////////////////////////////////
							 
							 echo "<script> location.assign('#np1')</script>";
						 

						  }

						 
						 
						  }
						  
						//////////IF NEW PASSWORD FIELDS DID NOT MATCH//////////////////////////////////////////////////////////////////////////////////////////////////////
						  
						  else{
							  
							  $npwd1_field_err = "field_err";
							  $npwd2_field_err = "field_err";
							  $holdoldpwd=$oldpass_no_sha1;
							  $incnpass="<span class=asterix>*</span>";

						/////////////////////FOCUS ON FIRST NEW PASSWORD FIELD//////////////////////////////////////////////////////////////
							 
							 echo "<script> location.assign('#np1')</script>";
						 

						  }

					  
					}

					/////////IF THERE ARE SPACES IN THE NEW PASSWORD//////////////////////////////////////////////////////////////////////////////////////////////////////

					  else{
						  
						$npwd1_field_err = "field_err";
						$npwd2_field_err = "field_err";
						$incnpass_space="<span style=color:red>*</span>";  
						
						$holdoldpwd=$oldpass_no_sha1;
						
						
					/////////////////////FOCUS ON FIRST NEW PASSWORD FIELD//////////////////////////////////////////////////////////////
						 
						 echo "<script> location.assign('#np1')</script>";
					 
						
					  }
					  
					  


		}

		///////////IF ANY OF THE FIELDS ARE BLANK/////////////////////////////////////////////////////////////////////////////////////////////////////


		else{
			if(!$oldpass && !$newpass1 && !$newpass2){
				
				$npwd1_field_err = "field_err";
				$npwd2_field_err = "field_err";
				$oldpwd_field_err = "field_err";
				$nullFields="<span class=asterix>*</span>";
				
				echo "<script> location.assign('#op')</script>";
				
			}
			
		else	
			if((!$oldpass || !$newpass1 || !$newpass2) && ($oldpass || $newpass1 || $newpass2)){
				
				//handle when null
				
				if(!$oldpass){
					
					$oldpwd_field_err = "field_err";
					$blanksoldpwd="<span class=asterix>*</span>";
					
		///////////////FOCUS ON OLD PASSWORD FIELD////////////////////////////////////////////////////////////////////////////////////			

				
				echo "<script> location.assign('#op')</script>";
				
				}
				
				
				if(!$newpass1){
					
					$npwd1_field_err = "field_err";
					$blanksnewpwd1="<span class=asterix>*</span>";

				
		///////////////FOCUS ON FIRST NEW PASSWORD FIELD////////////////////////////////////////////////////////////////////////////////////			
				
				echo "<script> location.assign('#np1')</script>";	
				
				}
				
				if(!$newpass2){
					
					$npwd2_field_err = "field_err";
					$blanksnewpwd2="<span class=asterix>*</span>";

				
		///////////////FOCUS ON SECOND NEW PASSWORD FIELD////////////////////////////////////////////////////////////////////////////////////			
				
				if($newpass1  && $oldpass )
				
				echo "<script> location.assign('#np2')</script>";
				
				}
				
		//////////////////////////HANDLES FOR WHEN ANY FIELD IS NOT NULL//////////////////////////////////////////////////////////////////////////////////
				
				if($oldpass){
				$holdoldpwd=$oldpass;	
				}
				
				if($newpass1){
					$holdnewpwd1=$newpass1;
				}
				
				if($newpass2){
				$holdnewpwd2=$newpass2;	
				}
				
				
				
			}
			
			

			
				
		}
		 
		 
	 
	}

	else{
	
	$not_logged="<span class='red'>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

	}
		


 }
?>

<!DOCTYPE HTML>
<html>
<head>

<title>Change Password</title>
<?php require_once('include-html-headers.php')   ?>


<style>

</style>


</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')     ?>

	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		echo "<a href='changepassword' title=>Change Password</a> "  ;
				
		?>
	</header>

	<div class="view_user_wrapper" id="hide_vuwbb">
		<h1 class="h_bkg">CHANGE PASSWORD</h1>
		
		<div id="loginformcontainer">
			<?php if(isset($pchangesucc)) echo $pchangesucc; ?><br/>
			<?php if(isset($not_logged)) echo $not_logged; ?>



			<div class="red">
				<?php
				 echo "<span class=asterix>"; 
				 if($ioldpass != "") echo " Old password is incorrect !<br/>"; 
				 if($incnpass != "") echo "New password fields did not match !"; 
				 if($inc_newpass_tshort) echo "The new password you entered was too short, mininmum length allowed is 6 !<br/>"; 
				 if($blanksnewpwd1 != ""  || $blanksnewpwd2 != "" ) echo "New password fields cannot be blank !<br/>";

				 if($blanksoldpwd != "")  echo " Old password field cannot be blank !<br/>";  
				 if($nullFields != "") echo "Fields marked * cannot be blank !"; 
				 if($incnpass_space != "") echo "Spaces are not allowed in the new password !<br/>"; 
				 echo "</span>";  
				 ?>
			 </div>
			<ul>
				<form name="changepass" method="POST" action="changepassword">
					<fieldset>
						<li>
						<?php if(!isset($oldpass_cpi)){ ?>
							<label>Enter old password:</label>
							<input maxlength="50" id="op" class="lpw only_form_textarea_inputs <?=$oldpwd_field_err ?>"  value="<?php if(isset($holdoldpwd))echo $holdoldpwd; ?>"  type="password" name="oldpass" />
						<?php }else{ ?>
							<input id="op" style="display:none;" class="lpw only_form_textarea_inputs <?=$oldpwd_field_err ?>"  value="<?php if(isset($holdoldpwd))echo $holdoldpwd; ?>"  type="password" name="oldpass_cpi" />
							<input  style="display:none;" class="only_form_textarea_inputs"  value="<?php if(isset($token))echo $token; ?>"  type="hidden" name="token" />
						<?php 
							}
						if(isset($nullFields)) echo $nullFields; if(isset($ioldpass)) echo $ioldpass; 
						if(isset($blanksoldpwd)) echo $blanksoldpwd; 

						?>
						</li>

						<label>Enter new password:</label>
						<li>
						<input  maxlength="50" id="np1" class="lpw only_form_textarea_inputs <?=$npwd1_field_err ?>"  value="<?php if(isset($holdnewpwd1))echo $holdnewpwd1; ?>" type="password" name="newpass1" />
						<?php  
						if(isset($nullFields)) echo $nullFields; if(isset($incnpass_space)) echo $incnpass_space;    
						if(isset($blanksnewpwd1)) echo $blanksnewpwd1;  if(isset($incnpass)) echo $incnpass; 
						if(isset($inc_newpass_tshort)) echo $inc_newpass_tshort; 
						?>
						</li>

						<label>Confirm new password:</label>
						<li>
						<input  maxlength="50" id="np2" class="lpw only_form_textarea_inputs <?=$npwd2_field_err ?>"  value="<?php   if(isset($holdnewpwd2))echo $holdnewpwd2; ?>" type="password" name="newpass2" />
						<?php 
						if(isset($nullFields)) echo $nullFields;  if(isset($incnpass_space)) echo $incnpass_space;   
						if(isset($blanksnewpwd2)) echo $blanksnewpwd2;   if(isset($incnpass)) echo $incnpass; 
						if(isset($inc_newpass_tshort)) echo $inc_newpass_tshort; 
						?>
						</li>
					</fieldset>
					<label class='show_pwd_chkbox_txt_wrapper'>show all password fields<input onclick="showpassword()"  id="login_checkbox" type="checkbox" name="showpass" /></label>				
					<li><button name="changepass"  class="formButtons">change password</button></li><br/>

				</form>
			</ul>
		</div>

	</div>
	<?php require_once('eurofooter.php')     ?>
</div>
</body>

</html>


