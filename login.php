<?php 
require_once('forumdb_conn.php');
require_once('phpfunctions.php');

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();
	
	setPageTimeZone();


$pincorrect="";$uincorrect="";$null_u="";$null_p="";$nullfields="";$ban_alert="";$pwd_field_err="";$uname_field_err="";
$fullname="";$fname="";$lname="";$pwd1="";$email="";$aboutyou="";$userdp="";
$status="";$timeedited="";$currentuser="";$dateedited="";$upload_time="";$rdr="";$ban_status="";$was_banned="";

/////////GET REDIRECT PASSED////////////////////////////////////////////////////////////////////////////////////

if(isset($_GET["rdr"]) && $_GET["rdr"])
	$rdr = urldecode($_GET["rdr"]);


////////////////////////////ON LOGIN///////////////////////////////////////////////////////////////////////////////////


if(isset($_POST['login'])){

	$lusername=protect($_POST['lname']);
	$lpassword=protect($_POST['lpass']);
	$lpasswordenc=sha1($lpassword);
	$lusernamelow=strtolower($lusername);
	$lusernamecapf=ucfirst($lusernamelow);
	$lusernamecap=strtoupper($lusernamelow);

		if($lusername && $lpassword){


/////////////////////////////////////////PDO QUERY////////////////////////////////////
				
						$sql = "SELECT * FROM members WHERE USERNAME=?  OR PASSWORD=? LIMIT 1";
						$stmt1 = $pdo_conn_login->prepare($sql);
						$stmt1->execute(array($lusername, $lpassword));
						
						$nrows = $stmt1->rowCount();

				if($nrows){

					while($rows=$stmt1->fetch(PDO::FETCH_ASSOC)){
						
						$dbuname=$rows['USERNAME'];
						$dbpass=$rows['PASSWORD'];
						$dp=$rows['AVATAR'];
						$confirmstatus=$rows['ACCOUNT_STATUS'];
						$suspension_stat = $rows["SUSPENSION_STATUS"];
						$comment1 = $rows["COMMENT1"]					;
						
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
						
						if(($lusername==$dbuname || $lusernamecapf==$dbuname || $lusernamecap==$dbuname  || $lusernamelow==$dbuname  ) && ($lpassword==$dbpass  || $lpasswordenc==$dbpass)){
							
							if($confirmstatus=="ACTIVATED"){									
								
								if($suspension_stat != "YES"){


//////////////////////////////////////////START A SESSION//////////////////////////////////////////////////////////

											//session_id(generateConfirmationCode());///PROBLEM WITH IE/////
											
											session_start();
											
////////////////VERY IMPORTANT- DESTROY ANY UNLOGGED SESSIONS BEFORE ASSIGNING A NEW SESSION USER///////////////////////////											
																
											//setcookie("username", "", (time() - 1814400));
																						
/////////////////////////////MAINTAIN USERNAME DISPLAY IN DATABASE////////////////////////////////////////////////////////

											
											$_SESSION['username']=$dbuname;
											
											
											$_SESSION['session'] = $dbuname;
																				
/////////////////////////////////////////// THEN REDIRECT TO DASHBOARD///////////////////		
										 
												header("location:dash-board");
												exit();											
									
								
								}
								else{
										
							
									$ban_alert = "<div class='red'><strong>Sorry <span class='blue'>".$dbuname." </span>, you are under an indefinite suspension and cannot login into 
									your account until your suspension is lifted.<br/>".$comment1."<br/> Please contact the <a href='contact-support' class='links'>support teams</a> for help</strong></div> ";
										
									
								}


							}
							else{
								
								$notconfirmed="<p class='red'>sorry your account has not been activated<br/>please click on the activation link that was sent to your E-mail to activate your account<br/>Thank you<br/>if you did not get your account activation E-mail, <a   href='activate-account?user=".$lusername."&rdr=login' onclick='return false;' class='resendcode links'   name='".$lusername."'  >please click on this link to resend your activation code</a></p>";
								
								 $uvalue=$lusername;
								 $pvalue=$lpassword;
																
								
							}

						
						}
						else{
							
							if(($lusername==$dbuname|| $lusernamecapf==$dbuname || $lusernamecap==$dbuname  || $lusernamelow==$dbuname ) && $lpassword!=$dbpass){
								 $pincorrect= "*";
								 $uvalue=$lusername;
								 
								 $pwd_field_err = "field_err";
								 
								 echo "<script>location.assign('?rdr=".urlencode($rdr)."#lpw')</script>";
							}

							 
							else{
								 
								$uincorrect= "*";
								$uname_field_err = "field_err";
								$pvalue=$lpassword;
								
								echo "<script>location.assign('?rdr=".urlencode($rdr)."#lun')</script>";
								
							}


						}


					}

				}

				else{
					
						$unfound='<span class="red">sorry!! Incorrect Login Details </span> ';
						$pvalue=$lpassword;
						$uvalue=$lusername;
						
				}



		}

		else{
				
				if(!$lusername && !$lpassword){
					$uname_field_err = "field_err";
					$pwd_field_err = "field_err";
					$nullfields='<span class=asterix>*</span>';
					echo "<script>location.assign('?rdr=".urlencode($rdr)."#lun')</script>";
				}
				
				if($lusername && !$lpassword){
					
					$pwd_field_err = "field_err";
					$uvalue=$lusername;
					$null_p="<span class=asterix>*</span>";
					echo "<script>location.assign('?rdr=".urlencode($rdr)."#lpw')</script>";
				}
				
				if(!$lusername && $lpassword){
					
					$uname_field_err = "field_err";
					$pvalue=$lpassword;
					$null_u="<span class=asterix>*</span>";
					echo "<script>location.assign('?rdr=".urlencode($rdr)."#lun')</script>";
					
				}	
				
			
		}
	
}



?>



<!DOCTYPE HTML>
<html>
<head>
<title>LOGIN</title>
<?php require_once("include-html-headers.php")   ?>

<script>

</script>


<style></style>
</head>

<body id="thebody" oload="document.getElementById('logusername').focus()">
<div class="wrapper">
	<?php if(isset($_SESSION["username"])) require_once('euromenunav.php') ?>

	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		$gettid="";$getq="";

		echo "<a href='#lun' title=>Login</a> "  ;
				
		?>
	</header>

	<div class="view_user_wrapper" id="hide_vuwbb">
		
		<h1 class="h_bkg">LOGIN</h1>

		<?php if($ban_alert !="") echo $ban_alert; 


		if(isset($_GET["code-resend"]))
			echo "<span class='black'>Your activation code has been resent !</span>";

		 ?>

		<div id="loginformcontainer">
			<p><?php  if(isset($notconfirmed)) echo $notconfirmed   ?></p>

			<p id="showcoderesentres" class="red"><?php if($rdr)  echo 'Please Login First'  ?></p>

			<form method="post" action="login?rdr=<?php if($rdr) echo urlencode($rdr); ?>">
			
				<span></span><?php  if(isset($unfound)) echo $unfound ; ?>
				<div class="errors blink">
					<?php 

					 if($nullfields !="") echo "Please enter your username and password!" ;
					 if($null_p !="") echo "Please enter your password ! "; 
					 if($null_u !="") echo "Please enter your username ! "; 
					 if($uincorrect !="") echo "Incorrect username ! ";  
					 if($pincorrect !="") echo "Incorrect password ! " 
					 
					?>
				</div>
				
				<p>USERNAME:<input maxlength="50" autocomplete="off"  class="only_form_textarea_inputs <?=$uname_field_err ?>" id="lun" value="<?php if(isset($uvalue))echo $uvalue; if(isset($_GET['username']))echo $_GET['username'];  ?>" id="username"  type="text" name="lname" /> <span class="red"><?php  if(isset($nullfields)) echo $nullfields ;    if(isset($null_u)) echo $null_u ;  if(isset($uincorrect))echo $uincorrect; ?></span></p>
				<p>PASSWORD:<input  maxlength="50" id="lpw" class="lpw only_form_textarea_inputs <?=$pwd_field_err ?>"  value="<?php if(isset($pvalue))echo $pvalue;   if(isset($_GET['password']))echo $_GET['password']; ?>" type="password"   name="lpass" /> <span class="red"><?php if(isset($nullfields)) echo $nullfields ; if(isset($pincorrect))echo $pincorrect; if(isset($null_p)) echo $null_p ;  ?></span></p>
				<button class="formButtons" name="login">login</button>
			</form><br/>
			<label  class='show_pwd_chkbox_txt_wrapper_login'>show password<input onclick="showpassword()"  id="login_checkbox" type="checkbox" name="showpassword" /></label>
			<br/><a  href="forgotpassword" id="red" class="links">Forgot your password?</a><br/>
			<span class="black">Not registered yet? </span><a class="links" href="register"> sign up now</a>

		</div>
		<p style="display:none;" id="getcuser"><?php if(isset($dbuname)) echo $dbuname; ?></p>

	</div>

	<?php require_once('eurofooter.php')?> 
</div>	
</body>
</html>
