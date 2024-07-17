<?php

require_once("phpfunctions.php");

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;


///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();

/*********************VARIABLE DECLARATION**************************************************/
$show_full_form= "";$demail="";$rqdemail="";$page_error="";$dob="";$sex="";$dob_day="";$dob_month="";$dob_year="";
$unamelen_err="";$pwdlen_err="";
$smale="";$sfemale="";$space_pwd="";$space_username="";$username_chars="";$mrt_arr="";$marital_stat_opt="";
$show_email_fields="";$alert_email_code_sent="";$email_registered="";$email_label="";$rqdmobphone="";

$blanks="";$rqduname="";$rqdpwd="";$rqdpwd2="";$rqdfname="";$rqdlname="";$rqdemail="";$unmatchedpwd="";
$email_blanks="";$dusername="";$dpassword="";$dpassword2="";$dfname="";$dlname="";$mrt_stat="";$mobphone="";
$username="";$password="";$password2="";$fname="";$lname="";$email="";$hide_form_on_succ="";$email_field_err="";
$uname_field_err="";$pwd_field_err="";$fname_field_err="";$lname_field_err="";$mobphone_field_err="";
$country="";$state="";$country_field_err="";$state_field_err="";$rqdcountry="";$rqdstate="";$referred_by="";
$referral_email_link="";$ref_readonly="";
$email_confirmed="";$get_code="";$get_email="";$get_code_null="";$check_code="";$confirm_link_used="";
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	
	
$page_self = getReferringPage("qstr url");
//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////

if(isset($_COOKIE["form_gate_response"])){
	
	$regerror = $_COOKIE["form_gate_response"];
	$hide_form_on_succ = true;
		
/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

		setcookie("form_gate_response", "", (time() -  1800));

}


if(isset($_GET["email"]))
	$email = protect($_GET["email"]);

$email_label = "E-mail";


/*********GRAB REFERRAL LINKS****************/
if(isset($_GET["rise"]))
	$referred_by = protect($_GET["rise"]);

if(isset($_POST["rise"]))
	$referred_by = protect($_POST['rise']);

if($referred_by){
	$referral_email_link =  'rise='.$referred_by.'&';
	$ref_readonly = 'readonly';

}

$mrt_arr = array("Single","Married","Seperated","Divorced","Widowed");

	foreach($mrt_arr as $mstat){

		if(isset($_POST["marital_stat"]) && $_POST["marital_stat"] == $mstat)
			$marital_stat_opt .= '<option selected>'.$mstat.'</option>';
		else
			$marital_stat_opt .= '<option>'.$mstat.'</option>';

	}

	$marital_stat_opt = '<select class="only_form_textarea_inputs" name="marital_stat">'.$marital_stat_opt.'</select>';


/*////////////CLEAR ALL UNCOMPLETED REGISTRATIONS OLDER THAN 24HRS THAT HAS'NT CONFIRMED THEIR EMAIL/////////
			AND THEIR LINKS HAS EXPIRED SO THAT THEY ARE ABLE TO REUSE SAME EMAIL
						SHOULD THEY OPT TO START A NEW REGISTRATION
///////////////////////////////////////////////////////////////////////////////////////////////////////////*/


////////////////////PDO QUERY////////////////////////////////////

				$sql =   "DELETE FROM members WHERE USERNAME='' AND (TIME + 86400) < ".time();
				$stmt_1 = $pdo_conn_login->prepare($sql);
				$stmt_1->execute();

//////////////////////ON SUBMIT OF THE EMAIL FORM FIELD OR THE FULL FORM/////////////////////////////////////////////////////////////////////////////////////////

if(isset($_POST['submit'])){

	//////////////////////////////HANDLE FOR PROCESSING THE EMAIL FORM FIELD ONLY////////////////////////////////////////////////////////////////////////////////

		if(isset($_POST["email_form_fields"])){

			$confirm_email="";$confirm_email_code="";

			$confirm_email = protect($_POST["email"]);

			if($confirm_email !=""){

				/////////////////////////CHECK IF THE EMAIL IS ALREADY REGISTERED///////////////////////////////////////////////////////////////////////////////////////

				////////////////////PDO QUERY////////////////////////////////////

						$sql =   "SELECT ID FROM members WHERE EMAIL=? LIMIT 1";
						$stmt1 = $pdo_conn_login->prepare($sql);
						$stmt1->execute(array($confirm_email));

						if(!$stmt1->rowCount()){

								$confirm_email_code = generateConfirmationCode();
								$time = time();

				////////////////////PDO QUERY////////////////////////////////////

								$sql =  "INSERT INTO members (EMAIL, EMAIL_CONFIRMATION_CODE, TIME) VALUES(?,?,?)";
								$stmt2 = $pdo_conn_login->prepare($sql);
								$stmt2->execute(array($confirm_email, $confirm_email_code, $time));

				////////////////SEND EMAIL ASKING USERS TO CONFIRM THEIR EMAIL ///////////////////////////

								$to = $confirm_email;

								 $subject = "Confirm your Email for registration at ".$getdomain;

								 $message = "Hello \n Thank you for choosing to register an account with us\nYour confirmation link is shown below and it's valid for 24hours\nplease click on the following link to confirm your email and continue with your registration\n\n <a class='links' href='".$getdomain."/register?".$referral_email_link."email=".$confirm_email."&code=".$confirm_email_code."'>Confirm Your Email Now</a> \n\nThank you\n\n\n\n";
									
								 $footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ."  All Rights Reserved.
											NOTE: This email was sent to you because you are about to register an account with ".$getdomain." . if you did not make such registration request, please kindly ignore this message.\n\n\n please do not reply to this email.";


								 $headers = "from: DoNotReply@".$domain_name."\r\n";
								 sendHTMLMail($to,$subject,$message,$footer,$headers);


								$regerror="<span class='green'>Thank you for choosing to register an account with us.
								<br/>A confirmation link has been dispatched to your E-mail: <a href='mailto:".$confirm_email."' class='blue'>".$confirm_email.
								"</a>,<br/> please click on the link inside to confirm your email and continue with your registration <br/>Thank you.</span><br/>
								<span class='cyan'>If you do not get your confirmation E-mail after a few minutes,
								<br/><a class='resendemailcode links' onclick='return false;' href='resend-email-confirmation-code?".$referral_email_link."email=".$confirm_email."&rdr=register' user_email='".$confirm_email."' rise='".$referred_by."'  id='resendemailcode'>please click on this link to resend your email confirmation link</a></span>";
								
															 
								////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
								echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($regerror)."')</script>";
									
								 

					}

					else{$email_registered="<span class='red'>Sorry the Email: <a href='mailto:".$confirm_email."'>".$confirm_email."</a> is already registered</span>";}

			}

			else{
				$rqdemail="<span class='red'>*</span>";
				$email_blanks="Please enter your Email !";
				$email_field_err = "field_err";
			}

		}
	/////////////////END OF HANDLE FOR EMAIL FORM FIELDS ONLY///////////////////////////////////////////////////////////////////////////////////////////



	//////////////HANDLE FOR PROCESSING THE FULL FORM FIELDS////////////////////////////////////////////////////////////////////////////////////////////////

	if(isset($_POST["username"])){


			$username=$_POST['username'];
			$password=$_POST['password'];
			$password2=$_POST['password2'];
			$fname=protect($_POST['fname']);
			$lname=protect($_POST['lname']);
			$email=protect($_POST['email']);
			$mobphone=protect($_POST['mobphone']);
			$country=protect($_POST['country']);
			$state=protect($_POST['state']);
			$sex=protect($_POST['sex']);


			if($sex == "Male")
				$smale = "selected";

			if($sex == "Female")
				$sfemale = "selected";

			$fullname=$fname." ".$lname;

			$copy_username = preg_replace("#[\|\[\]\,\.\#\$\!\~\`\*\@\%\^\(\)\+\;\:\'\"\=\?\/]#isU", "", $username);

			$spacechkpwd = false;
			/////////SPACE CHECKING IN PASSWORD///////////////////////////////////////////////////
			 $spacechkpwd1 = strpos($password," ");
			 $spacechkpwd2 = strpos($password2," ");
			 $chkindexpwd1 = substr($password,0,1);
			 $chkindexpwd2 = substr($password2,0,1);
			 if($chkindexpwd1 == " " || $chkindexpwd2 == " " || $spacechkpwd || $spacechkpwd2 ){
				 $spacechkpwd = true;
			 }


			//////SPACE CHECKING IN USERNAME/////////////////////////////////////////////////////////////////////////////////////////

			 $spacechk_username = strpos($username," ");

			 $chkindex_username = substr($username,0,1);

			 if($chkindex_username == " "){
				 $spacechk_username = true;
			 }



	//////////////////////////CHECK IF A USER HAS ALREADY USED OR ALTERED THE CONFIRMATION LINK////////////////////////////////////////////


	////////////////////PDO QUERY////////////////////////////////////

			$sql = "SELECT EMAIL_CONFIRMATION_CODE FROM members WHERE EMAIL=? LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);
			$stmt->execute(array($email));

			$check_code = $stmt->fetch(PDO::FETCH_ASSOC);

			$check_code = $check_code["EMAIL_CONFIRMATION_CODE"];

			if($check_code){

				if($username && $password  && $password2 && $fname && $lname && $email && $mobphone && $country && $state ){

						if($spacechk_username == false){

							if(strlen($username)  == strlen($copy_username)){

								if($spacechkpwd == false){

									if($password == $password2){

										if(strlen($password) >= 6){

											if(strlen($username) >= 4){

												////////////////////PDO QUERY////////////////////////////////////

												$sql =  "SELECT USERNAME FROM members WHERE USERNAME=? LIMIT 1";
												$stmt4 = $pdo_conn_login->prepare($sql);
												$stmt4->execute(array($username));

												$nrow =  $stmt4->rowCount();

												if(!$nrow){


													$time=time();
													$status="REGISTERED";

													$date=Date('Y-m-d h:i:s');

													////GENERATE CONFIRMATION CODE////////////////////////////////////////////////////////////////////////////

													$confirmcode=generateConfirmationCode();

													//////////CREATE AN UNENCRYPTED TMP PWD STORAGE FOR LATER RETRIEVAL ON CONFIRMATION OF FULL REG PRIOR TO LOGIN/////////////////////////////////////////////////////////////////////////////////////////////

													/*$path = "LOCAL COOKIES/TMP/".$username."_tmp_pwd.txt";

													$opened_file = fopen($path, "w");

													fwrite($opened_file, $password);

													fclose($opened_file);
													*/


													////////////////////PDO QUERY////////////////////////////////////

													$sql =  "SELECT ID FROM members WHERE EMAIL=? LIMIT 1";
													$stmt = $pdo_conn_login->prepare($sql);
													$stmt->execute(array($email));
													$id_row = $stmt->fetch(PDO::FETCH_OBJ);

													$id = $id_row->ID;

													$avn = generateFLRand("6",$id);
													$avn_enc = sha1($avn);

													//PUBLIC TABLE/////////////

													$password=sha1($password);

													$confirmstatus="NOT ACTIVATED";


													/*********SET RECYCLING DEADLINE ******************/
													$recyl_deadline = getRecyclingDeadline();

													////////////////////PDO QUERY////////////////////////////////////

													$sql = "UPDATE members SET USERNAME=?,PASSWORD=?,FULL_NAME=?,EMAIL=?,MOBILE_PHONE=?,FIRST_NAME=?,LAST_NAME=?,TIME=?,ACC_CONFIRMATION_CODE=?,ACCOUNT_STATUS=?
																,GENDER=?,AVN=?,OT_AVN=?,RECYCLING_DEADLINE=?,COUNTRY=?,STATE=? WHERE EMAIL=? LIMIT 1";
													$stmt5 = $pdo_conn_login->prepare($sql);

													if($stmt5->execute(array($username,$password,$fullname,$email,$mobphone,$fname,$lname,$time,$confirmcode,$confirmstatus,$sex,$avn_enc,$avn,$recyl_deadline,$country,$state,$email))){

															 $regerror= "<span class='black'><img class='reg_suc_fav' src='wealth-island-images/icons/ok.png' /><br/> <span class='blue'>REGISTRATION SUCCESSFUL!!! </span><br/>Your account activation code has been dispatched to your email address: <a class='links' href='mailto:".$email."'>".$email."</a>, it will arrive shortly <br/>
														Please click on the link inside to activate your account and then proceed to <a class='links' href='login'>login</a> <br/>Once your account is confirmed, Your ACCOUNT VERIFICATION NUMBER(AVN) WILL BE SENT TO YOUR EMAIL. Please copy it and keep it save because you will need it for all your transactions<br/>Thank you<br/>if you do not get the activation code after a few minutes,
														<a  href='activate-account?user=".$username."&rdr=register' onclick='return false;' class='resendcode links' name='".$username."'  value='".$email."' >please click on this link to resend your activation email</a></span><hr/>";


														///SEND CONFIRMATION EMAIL TO USER////////////

																$to=$email;
																 $subject="Confirm your registration at ".$getdomain;
																
																$message="Hello ".$username."\n Thank you for registering an account with us\nPlease click on the following link to activate your account and finish the registration\n <a class='links' href='".$getdomain."/activate-account?username=".$username."&code=".$confirmcode."'>Activate Your Account</a> \n\n Once your account is confirmed, Your ACCOUNT VERIFICATION NUMBER(AVN) WILL BE SENT TO YOUR EMAIL. Please copy it and keep it save because you will need it for all your transactions.\nThank you\n\n\n\n";
																			
																 $footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ."  All Rights Reserved.
																			NOTE: This email was sent to you because you registered an account at ".$getdomain." . if you did not make such registration, please kindly ignore this message.\n\n\n please do not reply to this email.";
																 $headers="from: DoNotReply@".$getdomain."\r\n";
																 sendHTMLMail($to,$subject,$message,$footer,$headers);


														///////AFTER SUCCESSFUL REGISTRATION SET THE USER EMAIL_CONFIRMATION_CODE TO ZERO TO PREVENT MULTIPLE REGS FROM ONE EMAIL ////////////////////////////////////////////////////////////////////////

																		$null = "0";

														////////////////////PDO QUERY////////////////////////////////////

																$sql = "UPDATE members SET EMAIL_CONFIRMATION_CODE=? WHERE USERNAME=? AND  EMAIL=? LIMIT 1  ";

																$stmt_8 = $pdo_conn_login->prepare($sql);
																$stmt_8->execute(array($null, $username, $email));


															$hide_form_on_succ = true;

														///////////INSERT THE USER INTO REFERRALS IF REFERRED BY A VALID USER//////////////////////////////////////////////////

															if($referred_by){

																////////////////////PDO QUERY////////////////////////////////////

																$sql = "SELECT ID FROM members WHERE USERNAME = ? LIMIT 1";

																$stmt = $pdo_conn_login->prepare($sql);
																$stmt->execute(array($referred_by));

																if($stmt->rowCount()){

																	$ref_time = time();
																	$incentive = 500;
																	////////////////////PDO QUERY////////////////////////////////////

																	$sql = "INSERT INTO referrals (REFERRAL,REFERRED,TIME,INCENTIVE) VALUES(?,?,?,?)";

																	$stmt = $pdo_conn_login->prepare($sql);
																	$stmt->execute(array($referred_by,$username,$ref_time,$incentive));

																	//////////INSERT THE INFOS INTO TRANSACTION TABLE FOR THE REFERRAL///////////////////////////////////////////////////////////

																	///////////GENERATE TRANSACTION NUMBER/////////////////////////////////
																	$trans_num = generateTransactionNumber($referred_by);

																	$desc = 'REFERRAL INCENTIVE FOR '.$username;
																	$trans_time = $ref_time;
																	$package = 'NONE (REFERRAL REWARD)';

																	///////////PDO QUERY////////////////////////////////////

																	$sql = "INSERT INTO transactions (TRANS_NUMBER,USERNAME,DESCRIPTION,AMOUNT,TRANS_TIME,PACKAGE) VALUES(?,?,?,?,?,?)";

																	$stmt = $pdo_conn_login->prepare($sql);
																	$stmt->execute(array($trans_num,$referred_by,$desc,$incentive,$trans_time,$package));


																}


															}
															
																													 
															////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
															echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($regerror)."')</script>";
																
															 

													}
													else{

														$regerror = "<span class='red'>REGISTRATION FAILED</span>";

													}


												}


												else{

													$regerror= "<span class='red'>sorry the username:<span class='blue'> ".$username." </span> is already taken. Please choose another username</span>";

													$uname_field_err = "field_err";

													echo "<script>location.assign('#u') </script>";

												}

											}

											else{

												$uname_field_err = "field_err";
												$unamelen_err = "<span class=asterix>*</span>";
												echo "<script>location.assign('#u') </script>";

											  }

										}
										else{

											$pwd_field_err = "field_err";
											$pwdlen_err = "<span class=asterix>*</span>";

											echo "<script>location.assign('#p') </script>";

										  }


									}
									else{

										$pwd_field_err = "field_err";
										$unmatchedpwd="<span class=asterix>*</span>";

										echo "<script>location.assign('#p') </script>";

									}

								}
								else{

									$pwd_field_err = "field_err";
									$space_pwd="<span class=asterix>*</span>";

									echo "<script>location.assign('#p') </script>";

								}

							}
							else{

								$uname_field_err = "field_err";
								$username_chars = "<span class=asterix>*</span>";

								echo "<script>location.assign('#u') </script>";

							}

						}
						else{

							$uname_field_err = "field_err";
							$space_username="<span class=asterix>*</span>";


							if($username)
								$dusername=$username;

							if($fname)
								$dfname=$fname;

							if($lname)
								$dlname=$lname;

							if($email)
								$demail=$email;


							if($password)
								$dpassword=$password;


							if($password2)
								$dpassword2=$password2;

							echo "<script>location.assign('#u') </script>";

						}


				}
				else{

					if(!$username && !$password && !$fname && !$lname && !$email && !$password2 && !$mobphone && !$country && !$state){

						$rqduname="<span class=asterix>* </span>";
						$rqdpwd="<span class=asterix>* </span>";
						$rqdpwd2="<span class=asterix>* </span>";
						$rqdfname="<span class=asterix>* </span>";
						$rqdlname="<span class=asterix>*</span>";
						$rqdmobphone="<span class=asterix>*</span>";
						$rqdcountry="<span class=asterix>*</span>";
						$rqdstate="<span class=asterix>*</span>";

						$uname_field_err = "field_err";
						$pwd_field_err = "field_err";
						$fname_field_err = "field_err";
						$lname_field_err = "field_err";
						$mobphone_field_err = "field_err";
						$country_field_err = "field_err";
						$state_field_err = "field_err";
						$blanks = true;

					}


					if(($username || $password || $password2 || $fname || $lname || $email || $mobphone  || $country || $state)
						&& (!$username || !$password || !$password2 || !$fname || !$lname || !$email || !$mobphone  || !$country || !$state)){

								if(!$username){

									$uname_field_err = "field_err";
									$rqduname="<span class=asterix>*</span>";
								}

								if(!$password){

									$pwd_field_err = "field_err";
									$rqdpwd="<span class=asterix>* </span>";
								}

								if(!$password2){

									$pwd_field_err = "field_err";
									$rqdpwd2="<span class=asterix>* </span>";
								}


								if(!$fname)	{

									$fname_field_err = "field_err";
									$rqdfname="<span class=asterix>* </span>";
								}

								if(!$lname)	{

									$lname_field_err = "field_err";
									$rqdlname="<span class=asterix>* </span>";
								}

								if(!$email){

									$email_field_err = "field_err";
									$rqdemail="<span class=asterix>* </span>";
								}
								if(!$mobphone){

									$mobphone_field_err = "field_err";
									$rqdmobphone="<span class=asterix>* </span>";
								}
								if(!$country){

									$country_field_err = "field_err";
									$rqdcountry="<span class=asterix>* </span>";
								}
								if(!$state){

									$state_field_err = "field_err";
									$rqdstate="<span class=asterix>* </span>";
								}

								$blanks=$rqduname.$rqdpwd.$rqdpwd2.$rqdfname.$rqdlname.$rqdemail.$rqdmobphone.$rqdcountry.$rqdstate;



					}

				}

			}
			else{

				$confirm_link_used = "<span class='red'>Sorry this link has either been already used, altered or expired</span>";

				$begin_new_reg = true;

				$email_label = "Enter your E-mail to start a new registration: ";


			}

	}

}



///////////////////////////CONFIRM A USER EMAIL WHEN THEY CLICK ON LINKS SENT TO THEIR EMAIL/////////////////////////////////////////////////////////////////

if( isset($_GET["email"]) && isset($_GET["code"])  ){

		if($_GET["email"] && $_GET["code"] ){

			$get_code=protect($_GET["code"]);
			$get_email=protect($_GET["email"]);

			$get_code_null="0";

	//////////////////////////CHECK IF A USER HAS ALREADY USED OR ALTERED THE CONFIRMATION LINK////////////////////////////////////////////


	////////////////////PDO QUERY////////////////////////////////////

			$sql = "SELECT EMAIL_CONFIRMATION_CODE FROM members WHERE EMAIL=? LIMIT 1";

			$stmt7 = $pdo_conn_login->prepare($sql);
			$stmt7->execute(array($get_email));

			$check_code = $stmt7->fetch(PDO::FETCH_ASSOC);

			$check_code = $check_code["EMAIL_CONFIRMATION_CODE"];


			if($check_code== "0"  || $check_code != $get_code ){

				$confirm_link_used="<span class='red'>Sorry this link has either been already used, altered or expired</span>";

				$get_email="";

				$begin_new_reg = true;

				$email_label="Enter your E-mail to start a new registration: ";

			}

	////////////////////CONFIRM THE USER IF CODES MATCH///////////////////////////////////////////////////////////////////////////////////////////////

			elseif($check_code != "0" &&  $check_code == $get_code){

				$email_confirmed="<span class='blue'>Thank you for confirming your Email please complete the form below to finish your registration<br>Thank you</span>";

			///////////////////////////////////UPDATE MEMBERS AND SET EMAIL CONFIRMATION CODE TO 0 IF TIME > 24HRS///////////////////////////////////////////////////////////////

				$time = time();

			////////////////////PDO QUERY////////////////////////////////////

				$sql = "UPDATE members SET EMAIL_CONFIRMATION_CODE=? WHERE (TIME + 86400) < ? AND  EMAIL=? AND EMAIL_CONFIRMATION_CODE=? LIMIT 1"	;

				$stmt8 = $pdo_conn_login->prepare($sql);
				$stmt8->execute(array($get_code_null,$time,$get_email,$get_code));


		///////////////////////STORE THE FULL FORM IN A VARIABLE TO HIDE OR DISPLAY IT ACCORDINGLY/////////////////////////////////////////////////////////////////////////////
		///////////////////////////////POPS ACTIVE ONLY ONCE AND IMMEDIATELY A USER CONFIRMS HIS EMAIL ////////////////////////////////////////////////////////////////////////////////

			$show_full_form='
					<ul>
						<form name="reg" method="post" action="register">
							<span class="red">
								please note that the following fields cannot be changed once submitted:<br/>
								Username, First Name, Last Name & Phone Number

							</span>
							<fieldset>
								<label>Username<span class="red"> *</span></label>
								<li><input maxlength="50"  placeholder="example: euroadams (minimum length is 4)" class="only_form_textarea_inputs '.$uname_field_err.'" type="text" value="'. $username.'" id="u"    name="username" /><span>'. $rqduname.$unamelen_err.$space_username.''.$username_chars.'</span> </li>
								<label>Password<span class="red"> *</span></label>
								<li><input  maxlength="50" placeholder="****** (minimum length is 6)"  class="only_form_textarea_inputs '.$pwd_field_err.'" type="password" id="p" value="'. $password.'"  name="password" /><span>'. $rqdpwd.''.$pwdlen_err. $unmatchedpwd.''.$space_pwd.'</span> </li>
								<label>Confirm password<span class="red"> *</span></label>
								<li><input maxlength="50" placeholder="Re-type your password here"  class="only_form_textarea_inputs '.$pwd_field_err.'" type="password"  value="'. $password2 .'"  name="password2" /><span>'. $rqdpwd2.''.$pwdlen_err.  $unmatchedpwd.''.$space_pwd.'</span> </li>

								<label>First name<span class="red"> *</span></label>
								<li><input  maxlength="80" placeholder="example: Steve" class="only_form_textarea_inputs '.$fname_field_err.'" type="text"  value="'. $fname.'"  name="fname" /><span>'. $rqdfname.'</span> </li>
								<label>Last name<span class="red"> *</span></label>
								<li><input maxlength="80" placeholder="example: Mason"  class="only_form_textarea_inputs '.$lname_field_err.'" type="text"   value="'. $lname.'"  name="lname" /><span>'. $rqdlname.'</span> </li>
								<label>E-mail<span class="red"> *</span></label><li>
								<input  class="only_form_textarea_inputs '.$email_field_err.'" placeholder="example: stevemason@gmail.com" type="email" readonly  value="'. $email.'"  name="email" /><span>'. $rqdemail.'</span> </li>
								<label>Phone Number<span class="red"> *</span></label>
								<li><input maxlength="80" placeholder="example: 08062xxxxxxx"  class="only_form_textarea_inputs '.$mobphone_field_err.'" type="text"   value="'. $mobphone.'"  name="mobphone" /><span>'. $rqdmobphone.'</span> </li>
								<label>Country<span class="red"> *</span></label>
								<li><input maxlength="80" placeholder="example: Nigeria"  class="only_form_textarea_inputs '.$country_field_err.'" type="text"   value="'. $country.'"  name="country" /><span>'. $rqdcountry.'</span> </li>
								<label>State<span class="red"> *</span></label>
								<li><input maxlength="80" placeholder="example: Delta"  class="only_form_textarea_inputs '.$state_field_err.'" type="text"   value="'. $state.'"  name="state" /><span>'. $rqdstate.'</span> </li>
								<label>Gender</label>
								<li><select  class="only_form_textarea_inputs" name="sex" >
								<option '.$smale.'>Male</option>
								<option '.$sfemale.'>Female</option>
								</select></li>
								<label>Referral</label>
								<li><input type="text" '.$ref_readonly.' class="only_form_textarea_inputs" value="'.$referred_by.'"  placeholder="Please leave blank if none"  name="rise" /> </li>
								<li><input type="hidden" value=""  name="full_form_fields" /> </li>
							</fieldset>
							<li><input  class="formButtons" type=submit name="submit" value="submit" /></li>
						</form>
					</ul>';





		}

	}
	else{

		$page_error = "<h1 class=page_errors> An unexpected error has occured <br/>We are sorry about this</h1>";


	}

}



///////////////////END OF HANDLE FOR CONFIRMING EMAIL//////////////////////////////////////////////////////////////////////////


/////////////////////////STORE EMAIL FORM FIELDS IN A VARIABLE TO HIDE OR DISPLAY IT ACCORDINGLY//////////////////////////////////////////////////////////////////////////////////

$show_email_fields='
				<ul>
					<form name="reg" method="post" action="register">
						<fieldset>
							<label id="blue">(We have to confirm your Email before you can proceed with the registration)</label>
							<label>'.$email_label.'<span class="red"> *</span></label>
							<li><input class="only_form_textarea_inputs '.$email_field_err.'" placeholder="Please Enter Your Email Here" type="email"  value="'. $demail .'"  name="email" />'.$rqdemail.' </li>
							<li><input type="hidden" value="'.$referred_by.'"  name="rise" /> </li>
							<li><input type="hidden" value=""  name="email_form_fields" /> </li>
						</fieldset>
						<li><input  class="formButtons" type="submit" name="submit" value="submit"></li>
					</form>
				</ul>';


//////////////////////////////////HIDE FORM AFTER SUCCESSFUL REGISTRATION///////////////////////////////////////////////////////////////////////////////


if(isset($_GET["code"]) && !isset($begin_new_reg)){


	$show_email_fields="";
	

}


//////////////MAJOR AND POPS ACTIVE DURING FORM PROCESSING AFTER A USER HAS CONFIRMED HIS EMAIL///////////////////////////////////////////////////////////////////////////////////////////////

if(isset($_POST["username"]) && !isset($begin_new_reg)){

	$show_email_fields="";


	$show_full_form='
			<ul>
				<form name="reg" method="post" action="register">
					<fieldset>
						<label>Username<span class="red"> *</span></label>
						<li><input maxlength="50" placeholder="example: euroadams (minimum length is 4)" class="only_form_textarea_inputs '.$uname_field_err.'" type="text" value="'. $username.'" id="u"    name="username" /><span>'. $rqduname.$unamelen_err.$space_username.''.$username_chars.'</span> </li>
						<label>Password<span class="red"> *</span></label>
						<li><input maxlength="50" placeholder="****** (minimum length is 6)" class="only_form_textarea_inputs '.$pwd_field_err.'" type="password" id="p" value="'. $password.'"  name="password" /><span>'. $rqdpwd.''.$pwdlen_err. $unmatchedpwd.''.$space_pwd.'</span> </li>
						<label>Confirm password<span class="red"> *</span></label>
						<li><input maxlength="50" placeholder="Re-type your password here" class="only_form_textarea_inputs '.$pwd_field_err.'" type="password"  value="'. $password2 .'"  name="password2" /><span>'. $rqdpwd2.''. $pwdlen_err. $unmatchedpwd.''.$space_pwd.'</span> </li>

						<label>First name<span class="red"> *</span></label>
						<li><input maxlength="80" placeholder="example: Isabel"   class="only_form_textarea_inputs '.$fname_field_err.'" type="text"  value="'. $fname.'"  name="fname" /><span>'. $rqdfname.'</span> </li>
						<label>Last name<span class="red"> *</span></label>
						<li><input  maxlength="80" placeholder="example: Jason"  class="only_form_textarea_inputs '.$lname_field_err.'" type="text"   value="'. $lname.'"  name="lname" /><span>'. $rqdlname.'</span> </li>
						<label>E-mail<span class="red"> *</span></label><li>
						<input  class="only_form_textarea_inputs '.$email_field_err.'" placeholder="example: isabeljason@yahoo.com" type="email" readonly  value="'. $email.'"  name="email" /><span>'. $rqdemail.'</span> </li>
						<label>Phone Number<span class="red"> *</span></label>
						<li><input maxlength="80" placeholder="example: 08028xxxxxxx"  class="only_form_textarea_inputs '.$mobphone_field_err.'" type="text"   value="'. $mobphone.'"  name="mobphone" /><span>'. $rqdmobphone.'</span> </li>
						<label>Country<span class="red"> *</span></label>
						<li><input maxlength="80" placeholder="example: USA"  class="only_form_textarea_inputs '.$country_field_err.'" type="text"   value="'. $country.'"  name="country" /><span>'. $rqdcountry.'</span> </li>
						<label>State<span class="red"> *</span></label>
						<li><input maxlength="80" placeholder="example: California"  class="only_form_textarea_inputs '.$state_field_err.'" type="text"   value="'. $state.'"  name="state" /><span>'. $rqdstate.'</span> </li>
						<label>Gender</label>
						<li><select  class="only_form_textarea_inputs" name="sex" >
						<option  '.$smale.'>Male</option>
						<option  '.$sfemale.'>Female</option>
						</select></li>
						<label>Referral</label>
						<li><input type="text" '.$ref_readonly.' class="only_form_textarea_inputs" value="'.$referred_by.'" placeholder="Please leave blank if none"  name="rise" /> </li> 
						<li><input type="hidden" value=""  name="full_form_fields" /> </li>
					</fieldset>
					<li><input  class="formButtons" type="submit" name="submit" value="submit" /></li>
				</form>
			</ul>';



}

if($hide_form_on_succ ){

	$show_full_form="";

	$show_email_fields="";
	
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////


?>

<!DOCTYPE HTML>
<html>
<head>

<title> REGISTER</title>
<?php require_once('include-html-headers.php')   ?>



<style>


</style>


</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php'); ?>


	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php


		echo "<a href='register' title=>Register</a> "  ;

		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper" id="hide_vuwbb">

		<?php echo getMidPageScroll(); ?>


		<h1 class="h_bkg">REGISTRATION:</h1>
		<div class="errors">ATTENTION: <br/>PLEASE WE ADVISE YOU USE A GMAIL ACCOUNT FOR FASTER E-MAIL DISPATCH<br/> ALSO, ALWAYS CHECK YOUR SPAM FOLDER FOR ANY E-MAIL YOU HAVE'NT RECEIVED.</div>
		
		<div id="signupformcontainer">
			<p><?php if(isset($regerror)) echo $regerror; ?></p>

			<p><?php if($email_confirmed ) echo $email_confirmed;

			if($alert_email_code_sent) echo $alert_email_code_sent;

			if($confirm_link_used ) echo $confirm_link_used;

			if($email_registered ) echo $email_registered;


			if(isset($_GET["code-resend"]))
				echo "<span class='black'>Your confirmation code has been resent !</span>";


			?></p>

			<p style="color:#ff0000"><?php

			if($email_blanks) echo $email_blanks;

			if($space_username) echo " Spaces are not allowed in the username field ! ";

			if($space_pwd) echo " Spaces are not allowed in the password fields ! ";

			 if($blanks) echo "Fields marked * are required !";

			 if($unmatchedpwd) echo "The password fields did not match !";

			 if($pwdlen_err) echo "The password you entered is too short (minimum length allowed is 6) !";

			 if($unamelen_err) echo "The username you entered  is too short (minimum length allowed is 4) !";

			  if($username_chars) echo " Characters in the bracket ( []|,.#'/$!~`*@%^()+;:\"=?\ ) are not allowed in the username field ";

			 if(isset($date_err) && $dob) echo "<span class='red'>The birthday format is incorrect. please follow this format (yyyy/mm/dd) example 1988/05/23</span>";

			 ?></p>


			<p id="showcoderesentres"></p>
			<p id="showemailcoderesentres"></p>



			<?php 
				if($page_error != "") echo $page_error; 
				if($show_email_fields != "")  echo $show_email_fields; 
				if($show_full_form != "") echo $show_full_form; 
			?>
			<span>Already Registered? <a class="links all_abtn" href="login">Login Now</a></span><br/><br/>
			<span>By signing up you agree to all our <a class="links" href="terms-and-condition">Terms and Conditions</a></span>

		</div>

		<span style="display:none;" id="getcuser"><?php if(isset($username)) echo $username; ?></span>
		<span style="display:none;" id="getcuseremail"><?php if(isset($email)) echo $email; ?></span>

	</div>

	<!--<span id="go_down"></span>
	<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>-->

	<?php require_once('eurofooter.php')     ?>
</div>
</body>
</html>
