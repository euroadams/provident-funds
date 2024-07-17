<?php


session_start();
require_once("phpfunctions.php");


//////////GET DATABASE CONNECTION///////////////////////
$pdo_conn = pdoConn("eurotech");
$pdo_conn_login = $pdo_conn;

///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();

setPageTimeZone();

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$message_field_err="";$message="";$asterix_all="";$header="";$footer="";$header_field_err="";$footer_field_err="";

$username = $_SESSION['username'];


/********PRE-DEFINE FOOTER***************/

$footer = 'Together we\'ll cross the bridge';

if(getUserPrivilege($username) == 'ADMIN' || getUserPrivilege($username) == 'MODERATOR'){
		
					
		$page_self = getReferringPage("qstr url");
		//////////GET FORM-GATE RESPONSE//////////////////////////////////////////////

		if(isset($_COOKIE["form_gate_response"])){
			
			$data = $_COOKIE["form_gate_response"];
			
				
		/////UNSET (EXPIRE IT BY 30MIN) THE FORM-GATE RESPONSE AFTER EXTRACTING IT//////////////////////////// 

				setcookie("form_gate_response", "", (time() -  1800));

		}

		
		
		
		/**********CAPTURE EDIT ID eid PASSED************************/

		if(isset($_POST["eid"])){
			
			$eid = protect($_POST["eid"]);


		/*********FETCH DETAILS FROM EID PASSED****************************************/
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT HEADER,CONTENT,FOOTER FROM news WHERE ID = ? LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);

			$stmt->execute(array($eid));
			
			$e_rows = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$header = $e_rows["HEADER"];
			$footer = $e_rows["FOOTER"];
			$message = $e_rows["CONTENT"];

			/******************ON EDIT***************************************/		
			
			if(isset($_POST["edit_news"])){
				
				$message = protect($_POST['composedmessage']);
				$header = protect($_POST['header']);
				$footer = protect($_POST['footer']);
				
				if($message && $header && $footer ){
					
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "UPDATE news SET HEADER = ?, CONTENT = ?, FOOTER = ? WHERE ID = ? LIMIT 1";

					$stmt = $pdo_conn_login->prepare($sql);

					if($stmt->execute(array($header,$message,$footer,$eid)))
						$data = "<span class='green'>YOUR MODIFICATIONS HAS BEEN SAVED<br/> Back to <a href='news' class='links'>News</a> </span>";
											
						////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
						echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($data)."')</script>";
									

					
				}				
				else{
					
					$data= "<span class='red'>Fields marked(*) are required</span>";	
					$asterix_all = "<span class=asterix>*</span>";
						
					if(!$header)
						$header_field_err = "field_err";
					if(!$message)
								$message_field_err = "field_err";
					if(!$footer)
								$footer_field_err = "field_err";
						
							
				}
				
			}
			
		}

	////////////////////////ON SEND/////////////////////////////////////////////////////////////////////

	if(isset($_POST['write_news'])){

			if($username){
			
			$header=protect($_POST['header']);
			$message=protect($_POST['composedmessage']);
			$footer=protect($_POST['footer']);
			$timesent=time();
			

			if($header && $message && $footer){
			
				//////////PREVENT REFRESH PAGE EFFECT////////////////////////////////////////////////////////
				///////////PDO QUERY////////////////////////////////////	
				
				$sql = "SELECT ID FROM news WHERE HEADER = ? AND CONTENT = ? AND FOOTER = ? AND AUTHOR = ?  LIMIT 1";

				$stmt1 = $pdo_conn_login->prepare($sql);

				$stmt1->execute(array($header,$message,$footer,$username));

				if(!$stmt1->rowCount()){
						
				///////////PDO QUERY////////////////////////////////////	
					
					$sql = "INSERT INTO news (AUTHOR,HEADER,CONTENT,FOOTER,TIME) VALUES(?,?,?,?,?)";

					$stmt2 = $pdo_conn_login->prepare($sql);

					$stmt2->execute(array($username,$header,$message,$footer,$timesent));
				}	


				$data= "<span class='green'>your news has been submitted successfully.<br/>Thank You  </span>";
				
						
				////// REDIRECT TO AVOID PAGE REFRESH DUPLICATE ACTION//////////////////////////////////
				echo "<script>location.assign('form-gate?rdr=".urlencode($page_self)."&response=".urlencode($data)."')</script>";
														

			}

			else{
				
				$data= "<h2 class='red'>Fields marked(*) are required</h2>";	
				$asterix_all = "<span class=asterix>*</span>";

				
				if(!$header)
					$header_field_err = "field_err";
				if(!$message)
							$message_field_err = "field_err";
				if(!$footer)
							$footer_field_err = "field_err";
				
				
			}

		}
		else{
		$not_logged="<span class=cyan>Sorry you are not logged in, please</span> <a href='login?rdr=".getReferringPage("http url")."#lun' class=links>click here to Login first</a>";

		}

	}
}


?>


<!DOCTYPE HTML>
<html>
<head>
<title>WRITE NEWS</title>
<?php require_once("include-html-headers.php")   ?>
<script></script>

<style>
</style>
</head>

<body>
<div class="wrapper">

	<?php if(isset($_SESSION["username"])) require_once('euromenunav.php') ?>

	<span id="go_up"></span>
			
	<header class="mainnav">
		<a href='<?=$getdomain ?>' title='Helping you cross the wealth bridge '><?=$domain_name; ?></a> <span class="pos_point" id="pos_point"> > </span>

		<?php 

		$page_self = getReferringPage("qstr url");

		echo "<a href='".$page_self."'>Write News</a> "  ;
		
				
		?>
	</header>

	<!--<div class="postul">(<a class="links topagedown" href="#go_down">Go Down</a>)</div>-->

	<div class="view_user_wrapper">
		
		<?php echo getMidPageScroll(); ?>	

		<?php if(isset($not_logged))   echo $not_logged ;

		if(getUserPrivilege($username) == 'ADMIN' || getUserPrivilege($username) == 'MODERATOR'){
			
		?>

		<ul>
			<li><?php if(isset($data))   echo '<div class="errors blink">'.$data.'</div>';   ?></li>
			<form action="write-news" method="post">
				<div class="modal_header">WRITE NEWS</div>
				<fieldset>
					<label>News Header:</label>
					<li><input type="text" maxlength="100"  class="only_form_textarea_inputs <?= $header_field_err ?>"  placeholder="Type your news heading here" name="header" value="<?php if(isset($header))  echo $header ?>"/><?php if(!$header) echo $asterix_all  ?></li>
					<label>News Content:</label>
					<li><textarea  class="only_form_textarea_inputs <?= $message_field_err ?>"  placeholder="Type your news here" name="composedmessage"><?php if(isset($message))  echo $message ?></textarea><?php if(!$message) echo $asterix_all  ?></li>
					<label>News Footer:</label>
					<li><input type="text" maxlength="100" class="only_form_textarea_inputs <?= $footer_field_err ?>"  placeholder="Type your news footer here" name="footer" value="<?php if(isset($footer))  echo $footer ?>"/><?php if(!$footer) echo $asterix_all  ?></li>
					
				</fieldset>
				<?php if(isset($_POST["eid"])){ ?>
						<li><input  type="hidden" value="<?php  if(isset($eid)) echo $eid;  ?> ?>" name="eid"></li>
						<li><input  class="formButtons"  type="submit" value="UPDATE" name="edit_news"></li>
				<?php }else{ ?>				
						<li><input  class="formButtons"  type="submit" value="SUBMIT NEWS" name="write_news"></li>
				<?php } ?>
			</form>
		</ul>
		
		<?php }else{ echo '<div class="errors blinks">SORRY YOU DO NOT HAVE ENOUGH PRIVILEGE TO VIEW THIS PAGE</div>';}  ?>
	</div>

	<!--<div class="postul">(<a class="links topageup" href="#go_up">Go Up</a>)</div>
	<span id="go_down"></span>-->

	<?php   require_once('eurofooter.php');   ?>
</div>
</body>
</html>
