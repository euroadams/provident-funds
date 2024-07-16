<?php

session_start();
require_once ("forumdb_conn.php");
require_once ("phpfunctions.php");

//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();

$not_logged="";$file="";$dob_day="";$dob_month="";$dob_year="";$mrt_arr="";$marital_stat_opt="";
$readonly_bnk_acc_holder="";$readonly_bnk_acc_num="";$readonly_bnk_name="";$readonly_bvn="";$cnt="";
$data="";$bvn_rqd="";$bvn="";$mfound="";$rise="";
$row1="";$err="";$Errxt="";$Errfl="";$old_userdp="";$smale=""; $sfemale="";$autofocus="";$dob_comp="";


if($_SESSION["username"]){

		date_default_timezone_set("Africa/Lagos");

		$granted="";$filetoolarge="";
		////////MONITOR UPLOAD MAX POST LENGTH//////////////////////////////////////////
		$granted = checkUploadLength();


		if($granted == "ERROR")
			$filetoolarge = "<span class='red'><b>Sorry the file you are trying to upload is too large (maximum allowed file size is 5MB).</b></span>" ;

		/////////////////////////////////////////////////////////////////////////////////////////////

		$currentuser = $_SESSION['username'];
		
		
				///////////PDO QUERY////////////////////////////////////	
					
					$sql = "SELECT REFERRAL FROM referrals WHERE REFERRED = ? LIMIT 1";

					$stmtz = $pdo_conn_login->prepare($sql);
					$stmtz->execute(array($currentuser));
					if($stmtz->rowCount()){
						
						$rise_row = $stmtz->fetch(PDO::FETCH_ASSOC);
						$rise = $rise_row["REFERRAL"];
						$rise = '<label>Your Rise</label>
								<li>
									<input  class="only_form_textarea_inputs" title="This Field is not editable" readonly  name="rise" value="'.$rise.'" />
								</li>';
						
					}

		///////////////////////ON SUBMITTING YOUR EDITED PROFILE//////////////////////////////////////////////////////////////////////////////////////////

		if(isset($_POST['submit'])){
			
					
				$username=protect($_POST['username']);
				$fname=protect($_POST['fname']);
				$lname=protect($_POST['lname']);
				$fullname=$fname." ".$lname;
				$email=protect($_POST['email']);
				$currentuser=$_SESSION['username'];
				$dob_day = $_POST['dob_day'];
				$dob_month = $_POST['dob_month'];
				$dob_year = $_POST['dob_year'];
				$sex=$_POST['sex'];
				$marital_stat=$_POST['marital_stat'];
				$alt_mobile=protect($_POST['alt_mobile']);
				$country = protect($_POST["country"]);
				$state = protect($_POST["state"]);
				$address = protect($_POST["address"]);
				$bnk_name = protect($_POST["bnk_name"]);
				$bnk_acc_num=protect($_POST['bnk_acc_num']);
				$bnk_acc_holder=protect($_POST['bnk_acc_holder']);
				//$bvn=protect($_POST['bvn']);
				$userdp="";
				
				if($bnk_name || $bnk_acc_num || $bnk_acc_holder){
				
				////////////CHECK FOR MULTIPLE BANK DETAILS/////////////////////////////////////////////
					$bnk_holder_sqry = "";$bnk_name_sqry = "";
					
					$bnk_acc_holder_arr = explode(" ", $bnk_acc_holder);
					$bnk_name_arr = explode(" ", $bnk_name);
					
					for($idx=0; $idx < count($bnk_acc_holder_arr); $idx++){/////CHECK EVERY PART OF THE ACCOUNT NAME/////////////
						
						if(($idx + 1) == count($bnk_acc_holder_arr))						
							$bnk_holder_sqry .= 'ACCOUNT_NAME LIKE "%'.$bnk_acc_holder_arr[$idx].'%"';
						else
							$bnk_holder_sqry .= 'ACCOUNT_NAME LIKE "%'.$bnk_acc_holder_arr[$idx].'%" OR ';
						
					}
					for($idx=0; $idx < count($bnk_name_arr); $idx++){/////CHECK EVERY PART OF THE BANK NAME/////////////
						
						if(($idx + 1) == count($bnk_name_arr))						
							$bnk_name_sqry .= 'BANK_NAME LIKE "%'.$bnk_name_arr[$idx].'%"';
						else
							$bnk_name_sqry .= 'BANK_NAME LIKE "%'.$bnk_name_arr[$idx].'%" OR ';
						
					}
					
					
				///////////PDO QUERY////////////////////////////////////	
					
					$sql = "SELECT ID FROM members WHERE USERNAME !=? AND (".$bnk_holder_sqry.")
						AND (".$bnk_name_sqry.") AND ACCOUNT_NUMBER = ?  LIMIT 1";

					$stmt = $pdo_conn_login->prepare($sql);

					$stmt->execute(array($currentuser,$bnk_acc_num));
					$mfound = $stmt->rowCount();
					
				}else{
					$pass = true;
				}
						
					if(!$mfound || isset($pass) || getUserPrivilege($currentuser) == "ADMIN"){
				
						$row1="";
						$data="";


						/////////////HANDLE FOR DATE CONTROL//////////////////////////

						if($dob_day == "--select day--")
							$dob_day = "01";

						if(strlen($dob_day) == 1)
							$dob_day = "0".$dob_day;

						if($dob_month == "--select month--")
							$dob_month = "January";

						if($dob_year == "--select year--")
							$dob_year = date('Y');


						$dob = $dob_day."-".$dob_month."-".$dob_year;



						//////////FETCH AN EXISTING DP SO THAT IF NO NEW UPLOAD WAS MADE DURING EDIT IT RETAINS OLD DP///////////////////////////////////////////////////////////////////////////////////////////////////////

								
						///////////PDO QUERY////////////////////////////////////	
							
							$sql = "SELECT * FROM members WHERE USERNAME=? LIMIT 1";

							$stmt1 = $pdo_conn_login->prepare($sql);

							$stmt1->execute(array($currentuser));
							
							$row1=$stmt1->fetch(PDO::FETCH_ASSOC);

							
						/////////////////UPLOADING AVATAR///////////////////////////////////////////////////////////////////////////////////////////////

						if($_FILES['profilepic']['name']){

						$filepath = "wealth-island-uploads/avatars/".basename($_FILES['profilepic']['name']);


						//////MAKE THE UPLOAD FOLDER INVISIBLE TO USERS

						$useruploadeddp=basename($_FILES['profilepic']['name']);

						$uok=1;

						$file = $_FILES['profilepic']['name'];

						$filetypeext=pathinfo($filepath,PATHINFO_EXTENSION);

							

						if((strtolower($filetypeext) !="jpg" ) &&(strtolower($filetypeext) !="jpeg" ) && ($filepath!="wealth-island-uploads/avatars/")){

						//$err="<span class=black>ERRORS:</span><br/>";

						//$err .= "<span class=black> ->> </span><span class=blue>sorry only picture formats ( .jpg, .jpeg ...) file extensions are allowed </span><br/>";
						$uok=0;

						$Errxt="Errxt";


						}

						/////////////RENAME THE FILE IF THE FILE NAME ALREADY EXISTS//////////////////////////////////////////////

						if(file_exists($filepath) && $file != ""){

						$i=0;
						while(file_exists($filepath)){
							
							
							$newfn = explode(".", $file);
							
							
							$newfn = $newfn[0]."(".$i.")".".".$newfn[1];
							
							$i++;
							
							$filepath = "wealth-island-uploads/avatars/".$newfn;
							
							
						}

						$filepath = "wealth-island-uploads/avatars/".$newfn;

						$useruploadeddp = $newfn;

						}

						else
							$useruploadeddp = $_FILES["profilepic"]["name"];


						if($_FILES['profilepic']['size'] > 5242880){ 

						$uok=0;

						$Errfl ="Errfl";

							
						}


						if($uok == 0){
							
							
							$autofocus = "autofocus";
							
							
							
						}


						else if ($uok){
							if(move_uploaded_file($_FILES['profilepic']['tmp_name'],$filepath)){

								$userdp=$useruploadeddp;

								$old_userdp=$row1['AVATAR'];

								$path2del = "wealth-island-uploads/avatars/".$old_userdp;

								if(realpath($path2del)  && $old_userdp)
									unlink($path2del);


							}

						}


						else if($_FILES["profilepic"]["name"] != ""){
						$err .= " <span class='red'>sorry there was an error uploading your file: ".$_FILES['profilepic']['name']." please try uploading the file again </span> ";

						}

						}

						if($userdp==""){
							$userdp=$row1['AVATAR'];
							
						$old_userdp=$row1['AVATAR'];
							
						}


						//PUBLIC COPY UPDATE

						$status="";$upload_time="";
						$timeedited="";
						$dateedited="";
						$status="EDITED PROFILE";

						$timeedited=time();
						$dateedited=Date('Y-m-d h:i:s');

						if($userdp !="" && $userdp != $old_userdp ){
							$upload_time=time();
							
						}

						else{
							
							$upload_time = $row1["TIME_UPLOADED"];
							
						}


						if(($file != "" && $uok ) || ($file == "")){




						///////////PDO QUERY////////////////////////////////////	
							
							$sql = "UPDATE members SET FULL_NAME=?,FIRST_NAME=?,LAST_NAME=?,EMAIL=?, 
									 COUNTRY =?, AVATAR=?, TIME_UPLOADED=? , GENDER=?, MARITAL_STATUS=?, ALT_MOBILE_PHONE=?  , DOB=? , STATE=?,
									 ADDRESS=?,  BANK_NAME=?,ACCOUNT_NUMBER=?, ACCOUNT_NAME=?, BVN=?  WHERE USERNAME=? ";


							$stmt2 = $pdo_conn_login->prepare($sql);

							$stmt2->execute(array($fullname,$fname,$lname,$email,$country,$userdp,
											$upload_time,$sex,$marital_stat,$alt_mobile,$dob,$state,$address,$bnk_name,$bnk_acc_num,$bnk_acc_holder,$bvn,$currentuser));
							

						 
						$data .= "<p class='green'><span class='cyan'>".strtoupper($currentuser)." </span>your profile has been updated successfully<br/> <a href='dash-board' class='links'>Back to dashboard</a> </p>";


						  }


					}else{
						
						$data .= "<p class='red'>Sorry <span class='cyan'>".strtoupper($currentuser)."</span>, The bank details you entered(<span class='blue'>".$bnk_name."/".$bnk_acc_num."/".$bnk_acc_holder."</span>) is already registered to a different account.<br/><b>Please note that multiple accounts and circumventing are highly prohibited!!!</b> </p>";
					}
					
		}



		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT * FROM members WHERE USERNAME=? LIMIT 1";

			$stmt4 = $pdo_conn_login->prepare($sql);

			$stmt4->execute(array($currentuser));
			

		if(isset($_POST["E1"]) && $Errxt ){
			
			
			
			if($_POST["E1"] != ""){
				
				$err = "<span class=black>ERROR:</span><br/>";
				
			$err .= "<span class=black> ->> </span><span class=blue>sorry only picture formats ( .jpg, .jpeg ...) file extensions are allowed </span><br/>";
			
			
			}
			
		}




		if(isset($_POST["E2"])  && $Errfl){
			
			
			if($Errxt == "")
				$err = "<span class=black>ERROR:</span><br/>";
			
			if($_POST["E2"] != "")
			
			 $err .= "<span class=black> ->> </span><span class=red><b>The image file you are trying to upload is too large -> ". ceil($_FILES["profilepic"]["size"]/(1024*1024)) ." Mb. (maximum image size allowed is 5Mb) </b></span>";
			
			
		}



		
		while($rows= $stmt4->fetch(PDO::FETCH_ASSOC)){
			
			$name=$rows['FULL_NAME'];
			
			if($rows["GENDER"] == "Male")
				$smale = "selected";
			
			if($rows["GENDER"] == "Female")
				$sfemale = "selected";
			
			$mrt_arr = array("Single","Married","Seperated","Divorced","Widowed");
			
			foreach($mrt_arr as $mstat){
				
				if($rows["MARITAL_STATUS"] == $mstat)
					$marital_stat_opt .= '<option selected>'.$mstat.'</option>';
				else
					$marital_stat_opt .= '<option>'.$mstat.'</option>';
						
			}
			
			$marital_stat_opt = '<select class="only_form_textarea_inputs" name="marital_stat">'.$marital_stat_opt.'</select>';
			
			
		///////DOB///////////////////////////
			
			$dob = $rows["DOB"];
			
			$dob_arr = explode("-", $dob);
			
			if(is_array($dob_arr)){
				
				for($idx=0; $idx < count($dob_arr); $idx++){
					
					if($idx == 0)
						$dob_day = $dob_arr[$idx];
					
					else if($idx == 1)
						$dob_month = $dob_arr[$idx];
					
					else if($idx == 2)
						$dob_year = $dob_arr[$idx];
					
				}
					
			}
			
			
			$avatar="";
			if($rows["AVATAR"])
				$avatar = "<div>Current Avatar File: <span><a class='links'  href='downloadfiles?f=".$rows["AVATAR"]."&rdr=profile'>".$rows["AVATAR"]."</a><a onclick='return false;' href='remove-avatar?file=".$rows["AVATAR"]."' class='remove_avatar' file='".$rows["AVATAR"]."'  > <img  class='delete'  src='wealth-island-images/icons/delete.png'  alt='delete'  title='Remove avatar' /></a> </span></div>";
			
			
			$user_privilege = $rows["USER_PRIVILEGE"];
			
			if($rows["BANK_NAME"] && ($user_privilege != "ADMIN" && $user_privilege != "MODERATOR" && $user_privilege != "EDIT" ))
				$readonly_bnk_name = 'readonly title="This Field is not editable"';
			if($rows["ACCOUNT_NAME"] && ($user_privilege != "ADMIN" && $user_privilege != "MODERATOR" && $user_privilege != "EDIT" ))
				$readonly_bnk_acc_holder =  'readonly title="This Field is not editable"';
			if($rows["ACCOUNT_NUMBER"] && ($user_privilege != "ADMIN" && $user_privilege != "MODERATOR" && $user_privilege != "EDIT" ))
				$readonly_bnk_acc_num =  'readonly title="This Field is not editable"';
			//if($rows["BVN"] && ($user_privilege != "ADMIN" && $user_privilege != "MODERATOR" && $user_privilege != "EDIT" ))
				//$readonly_bvn =  'readonly title="This Field is not editable"';
			
			if($readonly_bnk_name || $readonly_bnk_acc_holder || $readonly_bnk_acc_num || $readonly_bvn)
				$cnt = '<span class="red"> (If you wish to change your Bank details please contact <a class="links" href="contact-support">support</a>)</span>';
				
			/*if(($readonly_bnk_name || $readonly_bnk_acc_holder || $readonly_bnk_acc_num) && !$readonly_bvn )
					$bvn_rqd = 'required';
			
			<label>BVN<span class="red">*</span></label>
					<li>
						<input type="text" '.$bvn_rqd.' '.$readonly_bvn.'  placeholder=""  class="only_form_textarea_inputs" id="" name="bvn" value="'.$rows['BVN'].'"/>
					</li>*/
			
		$editprofile= '


		<form name="edit_prof" action="edit-profile" method="post" enctype="multipart/form-data">
			<ul>
				<h2 class="h_bkg">Personal Details:</h2>
				<fieldset>
					<label>Username</label>
					<li>
						<input title="This Field is not editable" class="only_form_textarea_inputs" type="text" id="" readonly value="'.$rows['USERNAME'].'"    name="username" /><span><?php if(isset($rqduname)) echo $rqduname; ?></span> 
					</li>
					<label>First name</label>
					<li>
						<input title="This Field is not editable"  class="only_form_textarea_inputs upperfirst"  placeholder="example: John" readonly type="text" id=""  value="'.$rows['FIRST_NAME'].'"  name="fname" /><span><?php if(isset($rqdfname)) echo $rqdfname; ?></span> 
					</li>
					<label>Last name</label>
					<li>
						<input title="This Field is not editable"  class="only_form_textarea_inputs upperfirst"  placeholder="example: Smith" readonly type="text" id=""   value="'.$rows['LAST_NAME'].'"  name="lname" /><span><?php if(isset($rqdlname)) echo $rqdlname; ?></span> 
					</li>
					<label>E-mail</label>
					<li>
						<input title="This Field is not editable" class="only_form_textarea_inputs" readonly type="email" id="" value="'.$rows['EMAIL'].'"  name="email" /><span><?php if(isset($rqdemail)) echo $rqdemail; ?></span> 
					</li>

					<label>Gender</label>
					<li>
						<select  class="only_form_textarea_inputs" name="sex">
						<option '.$smale.' >Male</option>
						<option  '.$sfemale.'>Female</option>
						</select>
					</li>
					<label>Marital Status</label>
					<li>
					'.$marital_stat_opt.'
					</li>
					<label>Main Phone Number</label>
					<li>
						<input title="This Field is not editable"  readonly class="only_form_textarea_inputs"  placeholder="example:08052xxxxxxx" type="text" name="main_mobile" value="'.$rows["MOBILE_PHONE"].'" />
					</li>
					<label>Alternate Phone Number</label>
					<li>
						<input  class="only_form_textarea_inputs"  placeholder="example:07067xxxxxxx" type="text" name="alt_mobile" value="'.$rows["ALT_MOBILE_PHONE"].'" />
					</li>
					<label>Date of Birth</label>
					<li>
					'.generateDateInput($dob_day, $dob_month, $dob_year).'
					</li>
					<label>Country</label>
					<li>
						<input  class="only_form_textarea_inputs" placeholder="example:Nigeria" type="text" maxlength="100" name="country" value="'.$rows["COUNTRY"].'" />
					</li>
					<label>State</label>
					<li>
						<input   class="only_form_textarea_inputs" placeholder="example:Lagos" type="text" maxlength="60" name="state" value="'.$rows["STATE"].'" />
					</li>
					<label>Address</label>
					<li>
						<input  class="only_form_textarea_inputs"  placeholder="example: 10 Queens, Ikeja"  type="text" maxlength="60" name="address" value="'.$rows["ADDRESS"].'" />
					</li>
					
					<label >upload/change Avatar: (<span class="red">maximum allowed file size is 5MB</span>)</label>
					<label id="upload_err">'. $err.$filetoolarge .'</label>
					<li ><input  '.$autofocus.'  class="upload_input"  accept="image/*"  type="file" name="profilepic" value="'.$rows['AVATAR'].'"></li>
					'.$avatar.$rise.'
					<input type="hidden" name="E1"  value="$Errxt" />
					<input type="hidden" name="E2"  value="Errfl" />
				</fieldset>
				<br/>
				<h2 class="h_bkg">Bank Details:</h2>
				<span class="red">
					Please you are highly advised to fill out your bank details carefully<br/> because
					once you save your details, you cannot edit it again.
				
				</span>
				<fieldset>
					<label>Bank Name</label>
					<li>
						<input  class="only_form_textarea_inputs" '.$readonly_bnk_name.'   placeholder="example: ACCESS BANK" type="text" maxlength="50" name="bnk_name" value="'.$rows["BANK_NAME"].'" />
					</li>
					<label>Account Number</label>
					<li>
						<input  class="only_form_textarea_inputs"  '.$readonly_bnk_acc_num.' type="text"   placeholder="example: 0029******" id="" name="bnk_acc_num" value="'.$rows["ACCOUNT_NUMBER"].'" />
					</li>

					<label>Account Holder</label>
					<li>
						<input type="text" '.$readonly_bnk_acc_holder.'  placeholder="example: Claire Dunes"  class="only_form_textarea_inputs" id="" name="bnk_acc_holder" value="'.$rows['ACCOUNT_NAME'].'"/>
					</li>
					
				</fieldset>
				'.$cnt.'

				<li><input id=""  class="formButtons"  type="submit" name="submit" value="SUBMIT & SAVE" /></li>
			</ul>
		</form>
		';
		}


}

else{

//$not_logged="<span class='red'>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";
header('location:login');
exit();

}


?>

<!DOCTYPE HTML>
<html>
<head>
<title>EDIT PROFILE</title>

<?php require_once("include-html-headers.php")   ?>

<style>
</style>
</head>
<body>
<div class="wrapper">
	<?php require_once('euromenunav.php')     ?>


	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		echo "<a href='edit-profile' title=>Edit Profile</a> "  ;
				
		?>
	</header>


	<?php  if($not_logged)  echo "<div class=view_user_wrapper>".$not_logged."</div>"  ?>

	<?php  if($_SESSION["username"])  {  ?>

	<div class="view_user_wrapper" id="hide_vuwbb">

		<span id="pupdated"></span>

		<?php if($data) echo '<div class="errors blink">'.$data.'</div>'    ?>

		<?php if(isset($editprofile)) echo $editprofile    ?>
		
		<h4 class="h_bkg">Your Referral Link is: <span class=""><?php echo $getdomain.'/register?rise='.$currentuser; ?></span><br/><span class="lgreen">Please copy it and share on social medias and amongst friends</span><h4>

	</div>

	<?php }  ?>
	<?php require_once('eurofooter.php')     ?>
</div>
</body>
</html>