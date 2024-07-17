<?php 

setPageTimeZone();


//////////////FUNCTIONS TO HANDLE PDO CONNECTIONS////////////////////////////////////////////////////////////////////////////////////////////////////////

function pdoConn($type){
	
	require_once('.db.config.php');

	static $pdo_conn;
	static $pdo_conn_login;
				
	////////////////PDO CONNECTION///////

	if(!$pdo_conn instanceof PDO){
					
		try{
			
			$pdo_conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USERNAME, DB_PWD);
			
			$pdo_conn_login = $pdo_conn;
			
			$pdo_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		}

		catch(PDOException $e){
			
			echo $e->getMessage();
			
			
			
		}
		
	}
	
	if($type == "eurotech")
		return $pdo_conn;
	
	if($type == "loginform")
		return $pdo_conn_login;

	
}






////////////////////////FUNCTION TO CHECK IF UPLOADED FILE EXCEEDS THE MAX POST LENGTH SET IN PHP INI///////////////////////////////////////////////////////////////////////////////////////

function checkUploadLength(){
	
	
	if(isset($_SERVER["REQUEST_METHOD"]) && strtolower($_SERVER["REQUEST_METHOD"]) === "post" &&
			empty($_POST) && empty($_FILES) && isset($_SERVER["CONTENT_LENGTH"]) ){
				
				
				$max_post_size = ini_get("post_max_size");
				$content_length = ceil($_SERVER["CONTENT_LENGTH"]/(1024 * 1024));
				
				 if($content_length > $max_post_size){
					 
					 return "ERROR";
			 
				 }
				
				else
					return "SUCCESS";
				
			}	
	
	
}









////////////////////////FUNCTION TO DETECT DEVICE TYPE///////////////////////////////////////////////////////////////


function detectDevice(){
			
			include_once('DETECT-DEVICES/Mobile_Detect.php');
			
			$detected = "";			
			
			$detect_device = new Mobile_Detect;
			$detected = ($detect_device->isMobile()?($detect_device->isTablet()? "Tablet" : "Phone") : "computer");
		    $detected = strtolower($detected);
			
			return $detected;
			
	
	
	
}






//////////////FUNCTION TO RETURN REQUESTING PAGE URL//////////////////////////////////////////////////////////////////////


function getReferringPage($type){
	
	$current_page="";
	
	
	if(trim(strtolower($type)) == "qstr url"){
		
		$current_page = $_SERVER["REQUEST_URI"];
		
		$refr_page_arr = explode("/", $current_page);
		
		array_shift($refr_page_arr);
		
		if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "localhost")
			array_shift($refr_page_arr);
		
		$current_page = $refr_page_arr[0];
		
	
	}
	
	else if(trim(strtolower($type)) == "page url"){
		
		$current_page = preg_replace("#\?.*#", "", $_SERVER["REQUEST_URI"]);
		
		$refr_page_arr = explode("/", $current_page);
		
		array_shift($refr_page_arr);
		
		if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "localhost")
			array_shift($refr_page_arr);
		
		$current_page = $refr_page_arr[0];
		
	
	}
	else if(trim(strtolower($type)) == "http url"){
		
		if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "localhost")
			$current_page = urlencode((isset($_SERVER["HTTPS"])? 'https':'http').'://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
			
		else
			$current_page = urlencode((isset($_SERVER["HTTPS"])? 'https':'http').'://www.'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
		
	
	}
	
	else
		$current_page = preg_replace("#\?.*#", "", $_SERVER["REQUEST_URI"]);
	
	   return $current_page;
	
}












//////////////FUNCTION TO GET MIME TYPES////////////////////////////////////////////////////////////////////

if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
		
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    
	
	}
	

}










/////////// FUNCTION TO PROTECT AGAINST USER INPUT/INJECTIONS///////////////////////////////////////////////////////////////////////


function protect($p){

	 $p=trim($p);
	 $p=stripslashes($p);
	// $p=htmlspecialchars($p);
	 
	 ////////THESE TWO SEEMS TO BE INTERFERING WITH USERS REAL INPUT//////////////////////////////
	 //$p=mysql_real_escape_string($p);
	 //$p=htmlentities($p,ENT_QUOTES);
	 return $p;
 
 
 }



 
 
 

/////////FORMAT TOPIC LINKS ON HOW THEY SHOULD BE FORMED/////////////////////////////////////////////////////////////////////////////


function sanitize_topic_links($e){
	
	
/////////////////DEALING WITH SPACES IN TOPIC NAMES////////////////

$topic_link="";

$topic_link=trim($e);
$topic_link=str_replace(" & ","-",$topic_link);
$topic_link=str_replace(" ","-",$topic_link);
$topic_link=str_replace("'","-",$topic_link);
$topic_link=str_replace('"','-',$topic_link);
$topic_link=str_replace("/","-",$topic_link);
$topic_link=str_replace("(","-",$topic_link);
$topic_link=str_replace(")","-",$topic_link);
$topic_link=str_replace("?","-",$topic_link);
$topic_link=str_replace("&","-",$topic_link);
$topic_link=str_replace(",","-",$topic_link);

return strtolower($topic_link);

	
/*$topic_link=stripslashes($topic_link);
 $topic_link=htmlspecialchars($topic_link);
 $topic_link=mysql_real_escape_string($topic_link);
 $topic_link=htmlentities($topic_link,ENT_QUOTES);
 */

	
	
}











//////////////////FUNCTION TO GENERATE BIRTHDAY INPUT FIELDS////////////////////////////////////////////////////////////////////////////////////////////////

function generateDateInput($sday, $smonth, $syear){
	
	$sday_start = substr($sday, 0, 1);
	
	if($sday_start == "0")
		$sday = substr($sday, 1);
	
	$days = ""; $months = ""; $years = "";  $months_arr = ""; 
	
	$months_arr = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	
	
	for($idx=1; $idx <= 31; $idx++){
		
		if($sday == $idx )
			$days .= "<option selected>".$idx."</option>";
		
		else
			$days .= "<option>".$idx."</option>";
		
	}
	
	$days = " <select name='dob_day' ><option>--select day--</option>".$days."</select> ";
	
	
	for($idx=0; $idx < count($months_arr); $idx++){
		
		if($smonth == $months_arr[$idx])
			$months .= "<option selected>".$months_arr[$idx]."</option>";
		
		else
			$months .= "<option>".$months_arr[$idx]."</option>";
		
	}
	
	$months = " <select name='dob_month' ><option>--select month--</option>".$months."</select> ";
	
	
	for($idx=date('Y'); $idx >= 1900 ; $idx--){
		
		if($syear == $idx)
			$years .= "<option selected>".$idx."</option>";
		
		else
			$years .= "<option>".$idx."</option>";
		
	}
	
	$years = " <select name='dob_year' ><option>--select year--</option>".$years."</select> ";
	
	
	return "<div class='date_field'>".$days.$months.$years."</div>";
	
	
}










/////////////////////FUNCTION TO RETURN DOMAIN NAME ADDRESS/////////////////////////////

function getDomain(){
	
	if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "localhost")
		return "http://localhost/WEALTH";
	else
		return "http://www.provident-funds.com";
	
}





/////////////////////FUNCTION TO RETURN DOMAIN NAME/////////////////////////////

function getDomainName(){
	
	return "Provident Funds";
}











/////////////////FUNCTION TO FORMAT DOB DISPLAY TO USERS/////////////////////////////////////////////////////////////////////////////////////////

function formatDob($dob, $currentuser){
	
	$day =""; $month="";$year="";
	
	$username = $_SESSION["username"];
	
	
	$dob_arr = explode("-", $dob);
	
	if(is_array($dob_arr)){
		
		for($idx=0; $idx < count($dob_arr); $idx++){
			
			if($idx == 0)
				$day = $dob_arr[$idx];
			
			if($idx == 1)
				$month = $dob_arr[$idx];
			
			if($idx == 2)
				$year = $dob_arr[$idx];
			
		}
			
	}		

///////DETERMINE THE DAY SUFFIX//////////////////////////////////////////
	
	$day_end = substr($day, 1);
	
	if($day_end == "1")
		$suffix = "st";
	
	else if($day_end == "2")
		$suffix = "nd";
	
	else if($day_end == "3")
		$suffix = "rd";
		
	else
		$suffix = "th";

///////REMOVE PRECEEDING ZEROS FROM THE DAY IF ANY AND APPEND THE SUFFIX/////////////
	
	if(substr($day, 0, 1) == "0")
		$day = substr($day, 1).$suffix;
	
	else
		$day .= $suffix;
		
	
	if($username == $currentuser){
		
		return ($day." of ".$month." ".$year);
	}
	else
		return ($day." of ".$month);
	
}










////////////////GENERATE RANDOM CODES FOR USERS///////////////////////////////////////////////////////////////////////////////////////////////////////////////


 
 function generateConfirmationCode(){
	 
	 $randompwd="";$mix="";

$randompwd=rand();

$mix=substr($randompwd,0,1);

switch ($mix){

case 0: {$mix="Wy42vfdsewHHAYU8EQ75GAcc46SIA563vv58DRP529";
break;
 }	
case 1:{
	$mix="13tE6543iuhcGHhObCVKL713viNNkggwTCcfYWrrVSs6";
	break;
}	
	
case 2:{
	$mix="Z52Z7Hd73v5MMERgawBb529ffHAR5229ZFgbe83bBskj2";
	break;
}	


case 3:{
	$mix="u66G53jbcy74bNKlg9b4dh4QDF77K18jjfte99HJR";
	break;
}
	
case 4:{
	$mix="L3r4Kla5dwUYKD341KK3bnGdk8vWbdjFd83LL662HGTE";
	break;
}



	
case 5:{
	$mix="QQ49skSEgry22kiWtRf7DWR8yygwrBDB6N65vI9ewd66";
	break;
}


	
case 6:{
	$mix="vZL3NmrFFW4Lwo7PPHEUfft374twr6637j355GAD6hfVCQy";
	break;
}


	
case 7:{
	$mix="rwA52EEPPJ229T2Eg7GDH19O0XX6V93SIU4jMN423pkj";
	break;
}


	
case 8:{
	$mix="iu3Dr7fQ23nhSE6628GTWU55saU100vxtkl904AOXDre";
	break;
}

	
case 9:{
	$mix="L43r4RndgurT33sqt28YygG8Ee94HIcVzEE30GD9K";
	break;
}



case 10:{
	$mix="DATBBe441gteW17bdaI7O0GRwNGe551MZcv820UaN4xArrE220I10E9K";
	break;
}


default:{
	$mix="yXgtdeEA2AUhwoPfeYac4gtrJWIEhca77393TAVEID71BVSccRk84JhFR52";
	break;
}
	
}


$confirmcode = rand(1,1000).rand(1,1000).$mix.rand(1,5000).$randompwd.rand(1,5000).rand(1,5000);
	 
	 
	 
	 
return $confirmcode;	 
	 
	 
	 
	 
 }
 

 
 
 
 
 
 
 
 
 /////FUNCTION TO ADD MIDPAGE SCROLL/////////////////////////////////////////////////////
 
 function getMidPageScroll(){
	 
	 
	return	'<div class="midpage_scroll">
				<a class="topagedown" title="scroll to bottom of page" href="#"><img alt="icon" src="wealth-island-images/icons/scrolldown.png" /></a>
				<a class="topageup" title="scroll to top of page" href="#"><img alt="icon" src="wealth-island-images/icons/scrollup.png" /></a>
			</div>';
 }
 

 
 
 
 
 
 
 


 
 
 
 
 
 
 
 
/////////////////////GET USER PRIVILEGE FUNCTION//////////////////////////////////////////////////////////
 
 function getUserPrivilege($username){

//////////PDO CONNECTION//////////////////

$pdo_conn_login = pdoConn("loginform");	 
	 
	 
	 $privilege="";
/////////////////GET THE USER PRIVILEGE///////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	
	
///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT USER_PRIVILEGE FROM members WHERE USERNAME=? LIMIT 1";

	$stmt1 = $pdo_conn_login->prepare($sql);
	$stmt1->execute(array($username));
		
	$priv_row = $stmt1->fetch(PDO::FETCH_ASSOC);
	
	if(is_array($priv_row))
		$privilege = $priv_row["USER_PRIVILEGE"];	

//////////////CLOSE PDO CONNECTION/////////////////////////////			
	
	$pdo_conn_login = null;
	
	return $privilege;
	 
	 
	 
	 
 }
 
 
 
 
 
 
 
 
 
 
 

///////////////////////////FUNCTION TO GET USER IP///////////////////////////////////////////////////////////////////////////////////////////////
 
 function getIP(){
	 
	 $ip = "";
	 
	 
if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	
else if(isset($_SERVER["HTTP_CLIENT_IP"]))
		$ip = $_SERVER["HTTP_CLIENT_IP"];

		
else if(isset($_SERVER["REMOTE_ADDR"]))
		$ip = $_SERVER["REMOTE_ADDR"];
	 
	 
	 return $ip;
	 
	 
 }
 
 
 
 
 
 
 



 
 
 
 
 
 
 
 
 
 

///////////////////////////FUNCTION TO CHECK FOR SECURED CONNECTION///////////////////////////////////////////////////////////////////////////////////////////////
 
function connectionSecured(){
	 
	 $is_secured = "";
	 
	 
if((isset($_SERVER["HTTPS"]) && !empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) != "off")
	|| (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] == 443))
		$is_secured = "https";
	
elseif((isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && !empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) && strtolower($_SERVER["HTTP_X_FORWARDED_PROTO"]) == "https")
		|| (isset($_SERVER["HTTP_X_FORWARDED_SSL"]) && !empty($_SERVER["HTTP_X_FORWARDED_SSL"]) && strtolower($_SERVER["HTTP_X_FORWARDED_SSL"]) == "on"))
			$is_secured = "https";
		
else
	$is_secured = "http";
	 
	 return $is_secured;
	 
	 
 }
 
 



 
/********FUNCTION TO RETURN RULES*******************************************/
function getRules(){
	
	return '
			<ul style="font-weight:bold;" class="red">
				<li>Participants must Recycle within 24 hours or face the system\'s wrath.</li>
				<li>To ensure that all participants meet up with Banking hours, the system will only assign on weekdays(excluding weekdays that are public holidays) between the hours of 6:00AM and 2:00PM.</li>
				<li>Participants should disburse directly into the assigned member\'s  bank account using only the account details  on the order form dispatched to their dashboard.</li>
				<li>All Transactions and donations are strictly between members only, meaning that there is no form of central account where funds are first disbursed to before they are dispatched to members. All donations and disbursement are made directly to members\' registered bank account.</li>
				<li>After your disbursement is confirmed, your capital return will be within 3 hours while your return on Investment will be on or before 10 Working Days (14 full days). Please we beseech participants to be Patient as our system is very impartial and works strictly on a first come first serve basis.</li> 
				<li>Participants upon successful registration, are assigned a unique account verification number (AVN), Please copy it and keep it safe as you will need it for all your transactions.</li>
				<li>Participants are advised to pledge amounts they can redeem instantly by joining only the package that corresponds to the cash they have at hand.</li>
				<li>Participants are highly advised to confirm disbursement within 24 hours to avoid the system\'s wrath </li>
				<li>No refund of payments made in fulfilment of a pledge.</li>
				<li>Communications about transactions and donations(including purging, declinations and disbursement  confirmations) are strictly between participants only.</li>
				<li>For your generosity in helping other participants, you’ll be rewarded with 200% as your return on investment.</li>
				<li>Disbursement methods are through bank payment, mobile transfers and cash at hand.</li>
				<li>In cases where disbursement method is by cash at hand, participants are requested to supervise the transactions effectively.</li>
				<li>Participants should only use the purge button to flag users that uploaded fake proofs of payment when they have not actually made any disbursement.</li>
				<li>When a participant purges another for possibly uploading fake proof of payments, he/she will have to wait at least 24 hours before the system re-assigns him/her. This is  to enable administrators look into the purge case judiciously.</li>
				<li>When a participant declines to make disbursement twice after he/she has been assigned, his/her account will be automatically suspended without notice.</li>
				<li>When a participant is genuinely purged twice for uploading fake proofs of payment, his/her account will be automatically suspended without notice.</li>
				<li>All complex problems and disagreements will be manually handled by system administrators. Please submit a <a class="links" href="contact-support">support</a> ticket to report any issues and it will be resolved within 24 hours.</li>
			</ul>			
			';
	
}
 
 



 
 
 
 
 
 ///////////////FUNCTION TO SET DEFAULT TIME ZONE FOR PAGE////////////////////////////////////////////////////////
 
 function setPageTimeZone(){
	 
	 
	 $time_zone = date_default_timezone_set("Africa/Lagos");
	 
	 return $time_zone;
	 
	 
 }
 
 
 
 
 
 /////////////FUNCTION TO DECODE SITE BBCCODES////////////////////////////////////////////
 
 function decodeBBC($content){
	 
	 
	 $content = preg_replace("#\[(.*)\]#isU", "<$1>", $content);
	 $content = preg_replace("#\[/(.*)\]#isU", "<$1>", $content);
	 
	 return $content;
	 
	 
 }
 
 
 
 
 
 
 
 ///////FUNCTION TO RETURN FORMATED THOUSAND COMMA SEPERATED NUMBERS///////////////////////////////////////////////////////////
 
 function formatNumber($num){
	 
	 return number_format($num,2);
 }
 
 
 
 
 
 
 
 
 
 
 
 
//////////////FUNCTION FOR FORMARTING DATE STYLE ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
 function dateFormatStyle($date){
	 
	
//////////////FORMAT THE WAY POST DATES ARE SHOWN////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	$today = date('l, d M, Y');
	
	$today_arr = explode(",", $today);
	
	$post_date = date('l, d M, Y',$date);
	
	$post_date_arr = explode(",", $post_date);
	
	$T_yesterday = time() - 86400;
	$T_2days_ago = $T_yesterday - 86400;
	$T_3days_ago = $T_2days_ago - 86400;
	$T_4days_ago = $T_3days_ago - 86400;
	$T_5days_ago = $T_4days_ago - 86400;
	$T_6days_ago = $T_5days_ago - 86400;
	$T_7days_ago = $T_6days_ago - 86400;
	$T_1week_ago = $T_7days_ago - 86400;
	
	$yesterday = date('l, d M, Y', $T_yesterday );
	$two_days = date('l, d M, Y', $T_2days_ago );
	$three_days = date('l, d M, Y', $T_3days_ago );
	$four_days = date('l, d M, Y', $T_4days_ago );
	$five_days = date('l, d M, Y', $T_5days_ago );
	$six_days = date('l, d M, Y', $T_6days_ago );
	$seven_days = date('l, d M, Y', $T_7days_ago );
	
	$yesterday_arr = explode(",", $yesterday);
	$two_days_arr = explode(",", $two_days);
	$three_days_arr = explode(",", $three_days);
	$four_days_arr = explode(",", $four_days);
	$five_days_arr = explode(",", $five_days);
	$six_days_arr = explode(",", $six_days);
	$seven_days_arr = explode(",", $seven_days);
	
	
	if(trim($today_arr[1]) == trim($post_date_arr[1]))
		$post_date = "today at ".date('h:iA', $date);
	
	else if(trim($post_date_arr[1]) == trim($yesterday_arr[1]))
		$post_date = "yesterday at ".date('h:iA', $date);
	
	else if(trim($post_date_arr[1]) == trim($two_days_arr[1]))
		$post_date = "2days ago at ".date('h:iA', $date);
	
	else if(trim($post_date_arr[1]) == trim($three_days_arr[1]))
		$post_date = "3days ago at ".date('h:iA', $date);
	
	else if(trim($post_date_arr[1]) == trim($four_days_arr[1]))
		$post_date = "4days ago at ".date('h:iA', $date);
	
	else if(trim($post_date_arr[1]) == trim($five_days_arr[1]))
		$post_date = "5days ago at ".date('h:iA', $date);
	
	else if(trim($post_date_arr[1]) == trim($six_days_arr[1]))
		$post_date = "6days ago at ".date('h:iA', $date);
	
	else if(trim($post_date_arr[1]) == trim($seven_days_arr[1]))
		$post_date = "1week ago at ".date('h:iA', $date);
	
	else 
		$post_date =  date(' \o\n l, d M, Y  \a\t h:iA',$date);
		
	
	 return $post_date;
	 	 
 }
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
////////////////////////GET USER DP////////////////////////////////////////////////////////////////////////////////////////////////
 
 function getDP($user,$type){


//////////PDO CONNECTION//////////////////

$pdo_conn_login = pdoConn("loginform"); 
	 
	
	$dp="";$loc="";$gender="";$dashboard_name="";$sex="";
	
	$username = $_SESSION["username"];
	
	if($user != $username && $user)
		$username = $user;
	
	if($username){		
		
	/////////PDO QUERY////////////////////////////////////	
		
					$sql = "SELECT AVATAR,GENDER,FULL_NAME FROM members WHERE USERNAME LIKE  ?  LIMIT 1";

					$stmt1 = $pdo_conn_login->prepare($sql);
					$stmt1->execute(array($username ));
					
		if($stmt1->rowCount()){
			
			$row = $stmt1->fetch(PDO::FETCH_ASSOC);
			
			$dp = $row["AVATAR"];
			$gender = strtolower($row["GENDER"]);
			$dashboard_name = '<span id="db_name">'.$row["FULL_NAME"].'</span>';
			
			if($gender == "male")
				$sex = '<span id="avatar_gender" title="MALE" class="blue">M</span>';
			if($gender == "female")
				$sex = '<span id="avatar_gender" title="FEMALE" class="cyan">F</span>';
			
			
		}
		if($type == "NOLINK"){
			if($dp){
				
				
					$dp = "<a class='avatar' ><img title=\"".$username."'s Avatar\" alt='Avatar' alt='Avatar' src='wealth-island-uploads/avatars/".$dp."' />".$sex."</a>";
					
			
			}
			else if(!$dp || !file_exists("wealth-island-uploads/avatars/".$dp) ){
					
					if($gender == "male")	
						$dp = "<a class='avatar' ><img title=\"Default Male Avatar\"  alt='Avatar' src='wealth-island-uploads/avatars/default_avatar_m.png' />".$sex."</a>";
					elseif($gender == "female")	
						$dp = "<a class='avatar' ><img title=\"Default Female Avatar\"  alt='Avatar' src='wealth-island-uploads/avatars/default_avatar_f.png' />".$sex."</a>";
					else
						$dp = "<a class='avatar' ><img title=\"Default Avatar\"  alt='Avatar' src='wealth-island-uploads/avatars/default-avatar.png' />".$sex."</a>";
					
				
						
			}
		}
		elseif($type == "LINK"){
			if($dp){
				
				
					$dp = "<a class='avatar' href='edit-profile'><img title=\"".$username."'s Avatar\" alt='Avatar' alt='Avatar' src='wealth-island-uploads/avatars/".$dp."' />".$sex."</a>";
					
			
			}
			else if(!$dp || !file_exists("wealth-island-uploads/avatars/".$dp) ){
					
					if($gender == "male")	
						$dp = "<a class='avatar' href='edit-profile' ><img title=\"Default Male Avatar\"  alt='Avatar' src='wealth-island-uploads/avatars/default_avatar_m.png' />".$sex."</a>";
					elseif($gender == "female")	
						$dp = "<a class='avatar' href='edit-profile' ><img title=\"Default Female Avatar\"  alt='Avatar' src='wealth-island-uploads/avatars/default_avatar_f.png' />".$sex."</a>";
					else
						$dp = "<a class='avatar' href='edit-profile' ><img title=\"Default Avatar\"  alt='Avatar' src='wealth-island-uploads/avatars/default-avatar.png' />".$sex."</a>";
					
				
						
			}
		}
		
	}
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

		
	 return $dp;
	 
	 
	 
	 
 }
 
 
 
 
 
 
 //////////////FUNCTION TO GET PACKAGE FEATURES/////////////////////////////////////////////////
 function getPackFeats(){
	 
	 
	 return '
			<span class="pack_feat"><i class="chk_fa"></i>2:1 Matrix</span>
			<span class="pack_feat"><i class="chk_fa"></i>Auto Assign</span>
			<span class="pack_feat"><i class="chk_fa"></i>Fake POP Purge</span>
			<span class="pack_feat"><i class="chk_fa"></i>Capital Return: 3 hours</span>
			<span class="pack_feat"><i class="chk_fa"></i>Investment Return:  3 to 14 days</span><hr/>
			';
	 
 }
 
 
 

 
 
 
 
 ///////////FUNCTION TO CHECK PACKAGE VISIBILITY////////////////////////////////////////////////////////////
 
 function packageVisibility($package){
	 
	 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");	
	
	$vis_status = "";
	
	///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT VISIBILITY FROM packages WHERE PACKAGE = ?  LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);
	$stmt->execute(array($package));
	
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$vis_status = $row["VISIBILITY"];
	
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	if(strtolower($vis_status) == "visible")
		return true;
	else
		return false;
 }
 
 
 
 

 
 
 
 
 ///////////FUNCTION TO GET PACKAGE OPEN TIME////////////////////////////////////////////////////////////
 
 function packageOpenTime($package){
	 
	 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");	
	
	$open_time = "";
	
	///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT OPEN_TIME FROM packages WHERE PACKAGE = ?  LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);
	$stmt->execute(array($package));
	
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$open_time = $row["OPEN_TIME"];
	
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

		return $open_time;
 }
 
 
 
 
 
 
 
 
 
 
 
 
 
 ///////////FUNCTION TO RETURN MEMBER EMAIL////////////////////////////////////////////////////////////
 
 function getMemberEmail($username){
	 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");			
	
	
	///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT EMAIL FROM members WHERE USERNAME = ?   LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);
	$stmt->execute(array($username));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$email = $row["EMAIL"];
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	return $email;
	
 }
 
 
 
 
 
 
 
 
 
 ///////////FUNCTION TO VERIFY IF A USER EXIST////////////////////////////////////////////////////////////
 
 function verifyUser($username){
	 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");			
	
	
	///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT ID FROM members WHERE USERNAME = ?   LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);
	$stmt->execute(array($username));
	
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	if($stmt->rowCount())
		return true;
	else
		return false;
 }
 
 
 
 
 
 
 
 
 
 
 
 
 ///////////FUNCTION TO VERIFY AVN////////////////////////////////////////////////////////////
 
 function verifyLOH(){
	 
	 $username = $_SESSION["username"];
	 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");			
	///////VERIFY AVN//////////////////////////////////
	
	///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT ID FROM members WHERE USERNAME = ? AND LOH_STATUS = 'CLEARED'  LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);
	$stmt->execute(array($username));
	
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	if($stmt->rowCount())
		return true;
	else
		return false;
 }
 
 
 
 
 
 
 
 
 
 ///////////FUNCTION TO VERIFY AVN////////////////////////////////////////////////////////////
 
 function verifyAVN($username,$avn){
	 
	 $avn = sha1($avn);
	 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");			
	///////VERIFY AVN//////////////////////////////////
	
	///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT ID FROM members WHERE USERNAME = ? AND AVN = ?  LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);
	$stmt->execute(array($username, $avn));
	
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	if($stmt->rowCount())
		return true;
	else
		return false;
 }
 
 
 
 
 
 
 
 
 
 
 
 
 
 ///////////FUNCTION TO SEND USER AVN////////////////////////////////////////////////////////////
 
 function sendAVN($username){
	 
	 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");	

///////GET DOMAIN NAME ///////////////////////////////////	
	
	$getdomain = getDomain();
	$domain_name = getDomainName();
	
	///////////PDO QUERY////////////////////////////////////	
	
	$sql = "SELECT OT_AVN, EMAIL FROM members WHERE USERNAME = ?   LIMIT 1";

	$stmt = $pdo_conn_login->prepare($sql);
	$stmt->execute(array($username));
	if($stmt->rowCount()){
			
		$avn_row = $stmt->fetch(PDO::FETCH_ASSOC);
		$avn = $avn_row["OT_AVN"];
		$email = $avn_row["EMAIL"];

	///SEND CONFIRMATION EMAIL TO USER////////////

		$to=$email;
		 $subject="YOUR ACCOUNT VERIFICATION NUMBER(AVN) at ".$getdomain;
		 $message="Hello ".$username."\n Thank you for registering an account with us\n\n<div style='font-size:20px;color:#0000ff;'>Your ACCOUNT VERIFICATION NUMBER(AVN) is:\n".$avn."</div>\n Please copy it and keep it save because you will need it for all your transactions.\nThank you\n\n\n\n";
					
		 $footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ."  All Rights Reserved.
						NOTE: This email was sent to you because you registered an account at ".$getdomain." . If you did not make such registration, please kindly ignore this message.\n\n\n please do not reply to this email.";
		 $headers="from: DoNotReply@".$domain_name."\r\n";
		 sendHTMLMail($to,$subject,$message,$footer,$headers);
	}
	
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	
 }
 
 
 
 
 
 
 
 
 
 
 
 /**********FUNCTION TO GENERATE RANDOM NUMBER OF FIXED LENGTH********************************************************/
 
 function generateFLRand($len,$salt){
	 
	 ///////SALT IS UNIQUE MEMBER ID PASSED WHICH CAN NEVER BE ZERO///////////////
	 $rand="";
	
		while(strlen($rand) < $len ){
			
			//////////CALCULATE THE SALT SPACING DEPENDING ON THE REQUESTED LENGTH/////////////////////////////////
			////////IF THE SALT IS = 1 THEN TREAT SPECIALLY BY NOT MULTIPLYING WITH THE LEN ELSE THE RANDOM/////////
			////////// RANGE WILL BE UNDER-RANGE E.G IF SALT = 1 AND LEN = 4, CONDITION TWO WILL BE RAND(17,16)////////////////////////////////////
			if($salt == 1)
				$rand .= rand($salt,($len*$len));
			else
				$rand .= rand(($salt + ($len*$len)),($len*$len*$salt));

			if(strlen($rand) > $len)
				$rand = substr($rand,0,$len);
			 
		}
		
		return $rand;
	 
 }
 
 
 
 
 
 
 
 
 
 
 ////////////////FUNCTION TO GENERATE TRANSACTION NUMBER////////////////////////////////////////////////////
 
 function generateTransactionNumber($user){
	 
	 		 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");
	
	$trans_num="";
	
	////////////FIRST GET THE USER ID FROM MEMBERS TABLE TO MAKE TRANS_NUMBER UNIQUE/////////////////////////////////////////////////////
		
		/////////PDO QUERY////////////////////////////////////

	$sql = "SELECT ID FROM members WHERE USERNAME = ? LIMIT 1";

	$stmt1 = $pdo_conn_login->prepare($sql);
	$stmt1->execute(array($user));
	
	$user_row = $stmt1->fetch(PDO::FETCH_ASSOC);
	
	$user_id = $user_row["ID"];
	
	$trans_num = generateFLRand("12",$user_id);
	

/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	return $user.'-'.$trans_num;	 
	 
 }
 
 
 
 
 
 
 
 ////////////////FUNCTION TO GENERATE TRANSACTION NUMBER////////////////////////////////////////////////////
 
 function generateTransactionNumberOld($user){
	 
	 		 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");
	
	$trans_num="";
	
	////////////FIRST GET THE USER ID FROM MEMBERS TABLE TO MAKE TRANS_NUMBER UNIQUE/////////////////////////////////////////////////////
		
		/////////PDO QUERY////////////////////////////////////

	$sql = "SELECT ID FROM members WHERE USERNAME = ? LIMIT 1";

	$stmt1 = $pdo_conn_login->prepare($sql);
	$stmt1->execute(array($user));
	
	$user_row = $stmt1->fetch(PDO::FETCH_ASSOC);
	
	$user_id = $user_row["ID"];
	
	////////////CHECK IF A TRANS_NUMBER HAS ALREADY BEEN GENERATED FOR THE USER B4 THEN USE IT AS SALT TO GENERATE NEXT TRANS_NUMBER////////////////////////////////////////////////////
	
	/////////PDO QUERY////////////////////////////////////

	$sql = "SELECT TRANS_NUMBER FROM transactions  WHERE USERNAME = ? LIMIT 1";

	$stmt1 = $pdo_conn_login->prepare($sql);
	$stmt1->execute(array($user));
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	if($stmt1->rowCount()){
		
		$row = $stmt1->fetch(PDO::FETCH_ASSOC);
		$salt_trans_num = $row["TRANS_NUMBER"];
		$trans_num = rand(($salt_trans_num + 1), ($salt_trans_num + 1000000) );
		
		/////////MAKE UNIQUE BY PREPENDING USER ID////////////////////////////////
		
		$trans_num = $user_id.$trans_num;
		
		return $trans_num;
		
	}////IF NO TRANS_NUMBER HAS BEEN GENERATED B4 FOR THE USER/////////////////////////
	else{
		
		$trans_num = rand(0, 1000000 );
		
		/////////MAKE UNIQUE BY PREPENDING USER ID////////////////////////////////
		
		$trans_num = $user_id.$trans_num;
		
		return $trans_num;
		
		
	}
	

	 
	 
 }
 
 
 
 
 

 
 
 
 
 
 
 
 /****************FUNCTION TO FETCH CURRENT ORDER NUMBER************************************/
 function getCurrentOrderNumber(){
	 
	 $order_num="";
	 $username = $_SESSION["username"];
//////////PDO CONNECTION//////////////////

$pdo_conn_login = pdoConn("loginform");	 
	 
		///////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT TRANS_NUMBER FROM transactions WHERE USERNAME = ? AND STATUS IN ('PENDING', 'PAID', 'RECEIVED', 'SEMI-SUCCESSFUL', 'SUCCESSFUL') ORDER BY TRANS_TIME DESC LIMIT 1 ";

				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute(array($username));
				if($stmt1->rowCount()){
					
					$row = $stmt1->fetch(PDO::FETCH_ASSOC);
					$order_num = $row["TRANS_NUMBER"];
				
				}
				
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

				
	 return $order_num;
	 
 }
 
 
 
 
 
 
 
 
 
 
 ///////////FUNCTION TO GET ARRAY OF PACKAGE NAMES (IN LOWER CASE)/////////////////////////////////////////
 
 function getPackagesArray(){
	 

//////////PDO CONNECTION//////////////////

$pdo_conn_login = pdoConn("loginform");	 
	 		
			$packages_arr = array();
/******************GET PACKAGES********************/	

////////////////////PDO QUERY////////////////////	
			
			$sql = "SELECT PACKAGE FROM packages WHERE VISIBILITY = 'VISIBLE' ORDER  BY PACKAGE";

			$stmt1 = $pdo_conn_login->prepare($sql);
			$stmt1->execute();
			
			while($pack_row = $stmt1->fetch(PDO::FETCH_ASSOC)){
				
				$packages_arr[] = $pack_row["PACKAGE"];
			}
			
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;
	 
	 return $packages_arr;
 }
 
 
 
 
 
 
 
 
 
 
 /****************FUNCTION TO FETCH LATEST NEWS************************************/
 function getLatestNews(){
	 
	 $news="";
//////////PDO CONNECTION//////////////////

$pdo_conn_login = pdoConn("loginform");	 
	 
		///////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT * FROM news ORDER BY TIME DESC LIMIT 1 ";

				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute();
				if($stmt1->rowCount()){
					
					$row = $stmt1->fetch(PDO::FETCH_ASSOC);
					$news = '
							<div class="news_wrapper">
								<div class="news">
									<h1 class="hbkg">LATEST NEWS</h1>
									<div class="news_header accordion_1_trig">'.$row["HEADER"].'</div>
									<div class="accordion_1">
										<div class="news_content ">'.decodeBBC($row["CONTENT"]).'<div class="clear"><span class="news_tstamp">'.dateFormatStyle($row["TIME"]).'</span></div><div clear><a href="news"  class="links abtn">More News</a></div></div>
										<div class="news_footer">'.$row["FOOTER"].'</div>
									</div>
								</div>
							</div>';
				
				}
				
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

				
	 return $news;
	 
 }
 
 
 
 
 
 
 
 
 

 
 
 
 /****************FUNCTION TO FETCH PACKAGE FOLLOWERS************************************/
 function getPackageFollowers($package){
	 
//////////PDO CONNECTION//////////////////
$pdo_conn_login = pdoConn("loginform");	 

		$total_followers="";$inc_amt=0;

		///////DETERMINE THE DEFAULT PACKAGE FOLLOWERS/////////

		switch($package){
			
			case "STANDARD":{$inc_amt = 0;  break;}
			case "CLASSIC":{$inc_amt = 0;  break;}
			case "PREMIUM":{$inc_amt = 0;  break;}
			case "ELITE":{$inc_amt = 0;  break;}
			case "LORD":{$inc_amt = 0;  break;}
			case "MASTER":{$inc_amt = 0;  break;}
			case "ROYAL":{$inc_amt = 0;  break;}
			case "ULTIMATE":{$inc_amt = 0;  break;}
			
		}
		
	 
		///////////PDO QUERY////////////////////////////////////
	
				$sql = "SELECT ID FROM package_followers WHERE PACKAGE = ? ";

				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute(array($package));				
				$total_followers = $stmt1->rowCount();				
				
				///SET FOUNDATION NUMBER////
				$fnum = 2;
				
				if($total_followers <= $fnum )//////////HIDE FOUNDATION PH////////////////////////
					$total_followers = 0;
				
				if($total_followers == 1)
					$total_followers = '<div class="followers">('.($total_followers + $inc_amt).' Follower)</div>';
				else
					$total_followers = '<div class="followers">('.($total_followers + $inc_amt).' Followers)</div>';
				
				
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

				
	 return $total_followers;
	 
 }
 
 
 
 
 
 
 
 
 /****************FUNCTION TO UPDATE PACKAGE FOLLOWERS************************************/
 function updatePackageFollowers($package){
	 
//////////PDO CONNECTION//////////////////
$pdo_conn_login = pdoConn("loginform");	 

$username = $_SESSION["username"];
	 
		///////////PDO QUERY////////////////////////////////////
	
				$sql = "SELECT ID FROM package_followers WHERE PACKAGE = ? AND USERNAME = ? LIMIT 1";

				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute(array($package, $username));
				if(!$stmt1->rowCount()){
						
					$time = time();
							
			///////////PDO QUERY////////////////////////////////////
		
					$sql = "INSERT INTO package_followers (USERNAME,PACKAGE,TIME_OF_FOLLOW) VALUES(?,?,?)";

					$stmt1 = $pdo_conn_login->prepare($sql);
					$stmt1->execute(array($username,$package,$time));
					
				}
					
				
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	 
 }
 
 
 
 
 
 
 
 
 
 /****************FUNCTION TO CASHOUT REFERRAL REWARDS************************************/
 function referralCashout(){
	 
//////////PDO CONNECTION//////////////////
$pdo_conn_login = pdoConn("loginform");	 

$username = $_SESSION["username"];

	///////////////SET LOOP_STATUS TO SEMI-COMPLETE AND CONFIRMED TO YES////////////////////
			$package = 'CLASSIC';	/***TO CASH 20K GO THROUGH 10K PACKAGE(CLASSIC)***********/		
			$time_of_pledge = time();
			$confirm_time = $time_of_pledge + (60*30);//////SET CONFIRMATION TIME TO 30MINUTES AFTER PLEDGE///////////////////
			$match_status = "AWAITING";
			$loop_status = "SEMI-COMPLETE";
			$confirm_status = "YES";
			$paid_or_decl = "PAID";
			$amount_pledged = 10000;
		
			///////////GENERATE TRANSACTION NUMBER/////////////////////////////////
						
			$trans_num = generateTransactionNumber($username);
					
			
			$table = 'euro_'.strtolower($package).'_donations';
			
			$return_amt = ($amount_pledged * 2);
			
			//////////////START A PACKAGE AND TAKE A SLOT ON THE PACKAGE DONATION TABLE////////////////////////////////////////////////////////////////////
			
			///////////PDO QUERY////////////////////////////////////	
		
			$sql = "INSERT INTO ".$table." (USERNAME,PACKAGE,AMOUNT_PLEDGED,RETURN_AMOUNT,TIME_OF_PLEDGE,MATCH_STATUS,CONFIRMED,CONFIRM_TIME,LOOP_STATUS,PAID_OR_DECLINED, TRANS_NUMBER) VALUES(?,?,?,?,?,?,?,?,?,?,?)";

			$stmt1 = $pdo_conn_login->prepare($sql);

			if($stmt1->execute(array($username, $package, $amount_pledged, $return_amt, $time_of_pledge, $match_status, $confirm_status, $confirm_time, $loop_status, $paid_or_decl, $trans_num))){
						
			/*****CATCH THE DONATION ID**********************/
			$did = $pdo_conn_login->lastInsertId();	
			
			//////////////////////////SINCE THE USER IS ELIGIBLE TO RECEIVE THE REWARD, SET HIS FLOW_DIRECTION TO IN//////////////////////////////////////////////////
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "UPDATE members SET CURRENT_PACKAGE = ?, RECYCLING_DEADLINE = '0', FLOW_DIRECTION = 'IN', LOOP_STATUS = 'SEMI-COMPLETE' WHERE USERNAME = ? AND CURRENT_PACKAGE ='NONE'  LIMIT 1";

				$stmt2 = $pdo_conn_login->prepare($sql);

				if($stmt2->execute(array($package, $username))){																									
					
					//////////INSERT THE INFOS INTO TRANSACTION TABLE///////////////////////////////////////////////////////////
					
					$desc = 'REFERRAL REWARD CASHOUT';
					$trans_time = time();
					
					
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "INSERT INTO transactions (TRANS_NUMBER,USERNAME,DESCRIPTION,AMOUNT,TRANS_TIME,PACKAGE,DONATION_ID) VALUES(?,?,?,?,?,?,?)";

					$stmt3 = $pdo_conn_login->prepare($sql);
					$stmt3->execute(array($trans_num,$username,$desc,$amount_pledged,$trans_time,$package,$did));
					
					
					/*******MAKE SURE CASHOUT IS REMITTED*******************************/
					
					$cashout_lim = 40;
					
					///////////PDO QUERY////////////////////////////////////	
											
					$sql = "UPDATE referrals SET REMIT_STATUS = 'CASHED' WHERE REFERRAL = ? AND CONFIRMATION = 'CONFIRMED' AND REMIT_STATUS = 'PENDING' LIMIT ".$cashout_lim;

					$stmt4 = $pdo_conn_login->prepare($sql);
					$stmt4->execute(array($username));
					
				}
			}
			
			return $trans_num;
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	 
 }
 
 
 
 
 
 
 
 
 
 
 /**********FUNCTION TO RETURN RECYCLING DEADLINE******************************************/
 
 function getRecyclingDeadline(){
	 
	/*********SET RECYCLING DEADLINE TO 1DAY******************/
	$recyl_deadline = (time() + (86400*1));
	
	return $recyl_deadline;
 }
 
 
 
 
 
 
 


/*************FUNCTION TO HANDLE DEFAULTERS****************************************************************/

function handleDefaulters(){
	
//////////PDO CONNECTION//////////////////

$pdo_conn_login = pdoConn("loginform");	 
	 
	
	/************HANDLE RECYCLING DEADLINE DEFAULTERS***********************************************/
				/////////FETCH USERS THAT THEIR RECYCLING DEADLINE HAS ELAPSED//////////
				
				$time = time();
	
		///////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT USERNAME FROM members WHERE (USERNAME != '' AND CURRENT_PACKAGE = 'NONE' AND SUSPENSION_STATUS !='YES' AND  RECYCLING_DEADLINE < ? ) ";

				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute(array($time));
				
				if($stmt1->rowCount()){
					////////SUSPEND THE DEFAULTERS/////////////////////////////////////////
					while($row = $stmt1->fetch(PDO::FETCH_ASSOC) ){
						
						$suspension="";
						
						$user = $row["USERNAME"];
						
						////////////DO NOT SUSPEND ADMINS AND MODERATORS//////////////////////////
						if(getUserPrivilege($user) == "ADMIN" || getUserPrivilege($user) == "MODERATOR")
							$suspension = "NO";
						else
							$suspension = "YES";
						
						$comment_recy = 'YOUR ACCOUNT WAS SUSPENDED FOR FAILING TO MEET UP WITH YOUR RECYCLING DEADLINE';
								
		///////////PDO QUERY////////////////////////////////////	
			
						$sql = "UPDATE members SET SUSPENSION_STATUS = ?, RECYCLING_DEADLINE = '0', COMMENT1 = ? WHERE USERNAME = ? LIMIT 1";

						$stmt2 = $pdo_conn_login->prepare($sql);
						$stmt2->execute(array($suspension,$comment_recy,$user));
						
						
					}
					
				}
				
	/************HANDLE PAYMENT DEADLINE DEFAULTERS***********************************************/


			//////DEFINE ARRAY OF PACKAGES SO YOU CAN LOOP THROUGH ALL PACKAGES AND DO MATCHING///////////////////////////////////
			
			$package_arr = getPackagesArray();

			//////////////LOOP THROUGH EACH PACKAGES AND DO MATCHING////////////////////////////////////
			
			foreach($package_arr as $pack_name){
				
				$donation_table = 'euro_'.strtolower($pack_name).'_donations';
				$matching_table = 'euro_'.strtolower($pack_name).'_matching';
				
	/////////FETCH USERS THAT THEIR PAYMENT DEADLINE HAS ELAPSED//////////
				
				$time = time();
	
		///////////PDO QUERY////////////////////////////////////	
				
				$sql = "SELECT PAYER_USERNAME, PAYER_DID FROM ".$matching_table." WHERE (DEFAULTER_STATUS = 'PENDING' AND PAID_OR_DECLINED != 'DECLINED' AND UPLOADED_PROOF='' AND  PAYER_DEADLINE < ? )";

				$stmt3 = $pdo_conn_login->prepare($sql);
				$stmt3->execute(array($time));
				
				if($stmt3->rowCount()){
					////////SUSPEND THE DEFAULTERS///////////
					while($row = $stmt3->fetch(PDO::FETCH_ASSOC) ){
						
						$suspension="";
						
						$user = $row["PAYER_USERNAME"];
						$payer_did = $row["PAYER_DID"];
						$comment_payd = 'YOUR ACCOUNT WAS SUSPENDED FOR FAILING TO MEET UP WITH YOUR PAYMENT DEADLINE';																											
						
						////////////DO NOT SUSPEND ADMINS AND MODERATORS//////////////////////////
						if(getUserPrivilege($user) == "ADMIN" || getUserPrivilege($user) == "MODERATOR")
							$suspension = "NO";
						else
							$suspension = "YES";
								
		///////////PDO QUERY////////////////////////////////////	
			
						$sql = "UPDATE members SET SUSPENSION_STATUS = ?, RECYCLING_DEADLINE = '0', COMMENT1 = ? WHERE USERNAME = ? LIMIT 1";

						$stmt4 = $pdo_conn_login->prepare($sql);
						$stmt4->execute(array($suspension,$comment_payd,$user));
							
		///////////////MARK USER AS TREATED AND HANDLE THE DECLINATION TO REMATCH THE RECEIVER/////////////////////////////////////
		///////////PDO QUERY////////////////////////////////////	
			
						$sql = "UPDATE ".$matching_table." SET DEFAULTER_STATUS = 'TREATED' WHERE PAYER_DID = ? AND  PAYER_USERNAME = ? LIMIT 1";

						$stmt4 = $pdo_conn_login->prepare($sql);
						if($stmt4->execute(array($payer_did,$user)))
							handleDeclination($payer_did, $donation_table, $matching_table, "DECLINATION");
						
						
					}
					
				}
			}
				
				
	/************HANDLE PURGING DEFAULTERS***********************************************/
	/////////FETCH USERS THAT HAS BEEN PURGED 2 TIMES//////////
				
	
		///////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT USERNAME FROM purges WHERE TOTAL >= '2' AND DEFAULTER_STATUS = 'PENDING'  ";

				$stmt5 = $pdo_conn_login->prepare($sql);
				$stmt5->execute();
				if($stmt5->rowCount()){
					////////SUSPEND THE DEFAULTERS/////////////////////////////////////////
					while($row = $stmt5->fetch(PDO::FETCH_ASSOC) ){
						
						$suspension="";
						
						$user = $row["USERNAME"];
						$comment_purges = 'YOUR ACCOUNT WAS SUSPENDED FOR BEING PURGED TWICE';
																		
						////////////DO NOT SUSPEND ADMINS AND MODERATORS//////////////////////////
						if(getUserPrivilege($user) == "ADMIN" || getUserPrivilege($user) == "MODERATOR")
							$suspension = "NO";
						else
							$suspension = "YES";
								
						///////////PDO QUERY////////////////////////////////////	
			
						$sql = "UPDATE members SET SUSPENSION_STATUS = ?, RECYCLING_DEADLINE = '0', COMMENT1 = ? WHERE USERNAME = ? LIMIT 1";

						$stmt6 = $pdo_conn_login->prepare($sql);
						$stmt6->execute(array($suspension,$comment_purges,$user));
		
		///////////////////MARK THE USER AS TREATED//////////////////////////////////////////////
		
		///////////PDO QUERY////////////////////////////////////	
			
						$sql = "UPDATE purges SET DEFAULTER_STATUS = 'TREATED' WHERE USERNAME = ? LIMIT 1";

						$stmt6 = $pdo_conn_login->prepare($sql);
						$stmt6->execute(array($user));
						
						
					}
					
				}
	
	/************HANDLE DECLINATION DEFAULTERS***********************************************/
	/////////FETCH USERS THAT HAS DECLINED TO DISBURSE  2 TIMES//////////
				
	
		///////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT USERNAME FROM declinations WHERE TOTAL >= '2' AND DEFAULTER_STATUS = 'PENDING'  ";

				$stmt7 = $pdo_conn_login->prepare($sql);
				$stmt7->execute();
				if($stmt7->rowCount()){
					////////SUSPEND THE DEFAULTERS/////////////////////////////////////////
					while($row = $stmt7->fetch(PDO::FETCH_ASSOC) ){
						
						$suspension="";
						
						$user = $row["USERNAME"];
						$comment_purges = 'YOUR ACCOUNT WAS SUSPENDED FOR DECLINING TO MAKE PAYMENT TWICE';
																								
						////////////DO NOT SUSPEND ADMINS AND MODERATORS//////////////////////////
						if(getUserPrivilege($user) == "ADMIN" || getUserPrivilege($user) == "MODERATOR")
							$suspension = "NO";
						else
							$suspension = "YES";
		///////////PDO QUERY////////////////////////////////////	
			
						$sql = "UPDATE members SET SUSPENSION_STATUS = ?, RECYCLING_DEADLINE = '0', COMMENT1 = ? WHERE USERNAME = ? LIMIT 1";

						$stmt8 = $pdo_conn_login->prepare($sql);
						$stmt8->execute(array($suspension,$comment_purges,$user));
								
		////////////////MARK THE USER AS TREATED////////////////
		///////////PDO QUERY////////////////////////////////////	
			
						$sql = "UPDATE declinations SET DEFAULTER_STATUS = 'TREATED' WHERE USERNAME = ? LIMIT 1";

						$stmt8 = $pdo_conn_login->prepare($sql);
						$stmt8->execute(array($user));
						
						
					}
					
				}
	
	
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;


}
 
 

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 //////////FUNTION TO HANDLE DECLINATIONS/PURGE/////////////
 
 function handleDeclination($payer_did, $donation_table, $matching_table, $type){	 
	  		 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");				
		
	
	//////////////FIRST FETCH THE RECEIVER DID  AND OTHER DETAIL FROM THE MATCHING TABLE WHERE PAYER DECLINED////////////////////////////////////////////////////////
	
	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "SELECT REC_DID,PAYER_USERNAME, REC_USERNAME FROM ".$matching_table." WHERE PAYER_DID = ?   LIMIT 1";

		$stmt = $pdo_conn_login->prepare($sql);
		$stmt->execute(array($payer_did));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);					
		$rec_did = $row["REC_DID"];
		$receiver = $row["REC_USERNAME"];
		$payer = $row["PAYER_USERNAME"];

///////////GET ALL THE CURRENT PAYER'S DETAILS //////////////
		/////////PDO QUERY////////////////////////////////////	

		$sql = "SELECT * FROM members  WHERE USERNAME = ? LIMIT 1";

		$stmt4 = $pdo_conn_login->prepare($sql);
		$stmt4->execute(array($payer));
		if($stmt4->rowCount()){
			$payer_detail = $stmt4->fetch(PDO::FETCH_ASSOC);
			$payer_email = $payer_detail["EMAIL"];
			$payer_phone = $payer_detail["MOBILE_PHONE"];
			$payer_alt_phone = $payer_detail["ALT_MOBILE_PHONE"];
			$payer_acc_name = $payer_detail["ACCOUNT_NAME"];
			$payer_bnk_name = $payer_detail["BANK_NAME"];
			$payer_acc_num = $payer_detail["ACCOUNT_NUMBER"];
			$payer_fn = $payer_detail["FULL_NAME"];
			$payer_avatar = getDP($payer,"NOLINK");
			$payer_gender = $payer_detail["GENDER"];
			
			////////////////COMPOSE DECLINATION MESSAGE FOR THE RECEIVER//////////////////////////////////
			
			$comment = '<p>ATTENTION!!!<br/> ONE OF THE MEMBERS (Name: <span class="blue">'.$payer_fn.'</span>,
			Phone: <span class="blue">'.$payer_phone.'</span>, E-mail: <span class="blue">'.$payer_email.'</span>,) 
			MERGED TO PAY YOU HAS DECLINED TO PAY</p>
			<p>SUBSEQUENTLY, IF YOU HAVE NOT ALREADY BEEN RE-MATCHED, PLEASE DO EXERCISE SOME PATIENCE AS THE SYSTEM
			WILL AUTOMATICALLY PRIORITIZE AND RE-MATCH YOU SHORTLY.<br/>THANK YOU</p>';
		}
		
		///////////GET ALL THE CURRENT RECEIVER'S DETAILS //////////////
		/////////PDO QUERY////////////////////////////////////	

		$sql = "SELECT * FROM members  WHERE USERNAME = ? LIMIT 1";

		$stmt4 = $pdo_conn_login->prepare($sql);
		$stmt4->execute(array($receiver));
		if($stmt4->rowCount()){
			$rec_detail = $stmt4->fetch(PDO::FETCH_ASSOC);
			$rec_email = $rec_detail["EMAIL"];
			$rec_phone = $rec_detail["MOBILE_PHONE"];
			$rec_alt_phone = $rec_detail["ALT_MOBILE_PHONE"];
			$rec_acc_name = $rec_detail["ACCOUNT_NAME"];
			$rec_bnk_name = $rec_detail["BANK_NAME"];
			$rec_acc_num = $rec_detail["ACCOUNT_NUMBER"];
			$rec_fn = $rec_detail["FULL_NAME"];
			$rec_avatar = getDP($receiver,"NOLINK");
			$rec_gender = $rec_detail["GENDER"];
			
			////////////////COMPOSE PURGE MESSAGE FOR THE PAYER//////////////////////////////////
			
			$comment_4_payer = '<p>ATTENTION!!!<br/> ONE OF THE MEMBERS (Name: <span class="blue">'.$rec_fn.'</span>,
			Phone: <span class="blue">'.$rec_phone.'</span>, E-mail: <span class="blue">'.$rec_email.'</span>,) 
			YOU WERE MERGED TO PAY HAS PURGED YOU, POSSIBLY FOR UPLOADING FAKE PROOF OF PAYMENT WHEN YOU HAVE NOT ACTUALLY PAID</p>
			<p>HOWEVER, IF YOU ARE SURE YOU WERE PURGED IN AN ERROR OR UNJUSTLY, PLEASE CONTACT THE <a class="links" href="contact-support">SUPPORT</a> TEAM
			WITHIN 24 HOURS FROM THE TIME YOU WERE PURGED ELSE THE SYSTEM WILL AUTOMATICALLY CLOSE THE CASE.</p>
			<p>NOTE: IF YOU KNOW YOU WERE PURGED IN AN ERROR OR UNJUSTLY, <b>DO NOT JOIN ANY OTHER PACKAGE UNTIL YOUR PURGE CASE HAS BEEN RESOLVED.</b><br/>THANK YOU</p>';
		}
		
		
		
///////////////IF THE PAYER DECLINES TO PAY THEN UPDATE HIS DONATION WITH DECLINED AND COMPLETE HIS LOOP CYCLE/////////////////////////////////////////////////////////////////
	
	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "UPDATE ".$donation_table." SET PAID_OR_DECLINED = 'DECLINED', MATCH_STATUS = 'DECLINED', LOOP_STATUS = 'COMPLETE', CONFIRMED = 'DECLINED' WHERE ID = ?   LIMIT 1";

		$stmt = $pdo_conn_login->prepare($sql);
		$stmt->execute(array($payer_did));
		
		
	///////////////IF THE PAYER DECLINES TO PAY THEN ALSO UPDATE HIS MATCHING WITH DECLINED AND STOP HIS TIMER BY SETTING PAYER_DEADLINE TO ZERO/////////////////////////////////////////////////////////////////
	
	///////////PDO QUERY////////////////////////////////////	
		
		$sql = "UPDATE ".$matching_table." SET PAID_OR_DECLINED = 'DECLINED', CONFIRMED = 'DECLINED', PAYER_DEADLINE = '0' WHERE PAYER_DID = ?   LIMIT 1";

		$stmt = $pdo_conn_login->prepare($sql);
		$stmt->execute(array($payer_did));
		
		if($type == "DECLINATION"){/*************DECLINATION*********************************/
			
		///////////////IF THE PAYER DECLINES TO PAY THEN ALSO UPDATE HIM IN DECLINATION TABLE/////////////////////////////////////////////////////////////////
		
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT ID FROM declinations WHERE USERNAME = ?   LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);
			$stmt->execute(array($payer));
			$time = time();
			
			if($stmt->rowCount()){
								
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "UPDATE declinations SET DECLINED_MEMBER2 = ?, DECLINE2_TIME = ?, TOTAL = (TOTAL + 1) WHERE USERNAME = ?   LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($receiver,$time,$payer));
				
			}
			else{
				
				$total = 1;
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "INSERT INTO declinations (USERNAME,DECLINED_MEMBER1,DECLINE1_TIME,TOTAL) VALUES(?,?,?,?)";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($payer,$receiver,$time,$total ));
			}
			
			///////////////UPDATE DECLINATION COUNTER IN MEMBERS TABLE FOR THE  PAYER/////////////////////////////////////////////////////////
			///////////PDO QUERY////////////////////////////////////	
					
					$sql = "UPDATE members SET TOTAL_DECL = (TOTAL_DECL + 1)  WHERE USERNAME = ?    LIMIT 1";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array($payer));
			
			
			///////////DROP A COMMENT FOR THE RECEIVER ABOUT THE DECLINATION/////////////////////////////////////////////////////////////
				
			//////////CHECK IF COMMENT1 COL IS FREE ELSE PUT IT IN COMMENT2 COL////////////////////////////////////////////////
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "SELECT ID FROM members WHERE USERNAME = ? AND COMMENT1 != ''   LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($receiver));
				if($stmt->rowCount()){
					
					////////////////PUT IN COMMENT2///////////////////////////////////////////
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "UPDATE members SET COMMENT2 = ?  WHERE USERNAME = ?    LIMIT 1";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array($comment,$receiver));
				}
				else{
					
					////////////////PUT IN COMMENT1///////////////////////////////////////////
					///////////PDO QUERY////////////////////////////////////	
					
					$sql = "UPDATE members SET COMMENT1 = ?  WHERE USERNAME = ?    LIMIT 1";
					$stmt = $pdo_conn_login->prepare($sql);
					$stmt->execute(array($comment,$receiver));
				}
			
			/*********UPDATE TRANSACTION STATUS IN TRANSACTION TABLE TO DECLINED ************************************/
				
				$trx_time = time();
				
				///////////PDO QUERY////////////////////////////////////	

				$sql = "UPDATE transactions SET STATUS='DECLINED', DONATION1='DECLINED', DONATION2='DECLINED', DONATION1_TIME=?, DONATION2_TIME=? WHERE DONATION_ID=? LIMIT 1 ";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($trx_time,$trx_time,$payer_did));
		
		}
		elseif($type == "PURGING"){/*********PURGING*************************/	
			
		///////////////IF THE PAYER WAS PURGED THEN ALSO UPDATE HIM IN PURGE TABLE/////////////////////////////////////////////////////////////////
		
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT ID FROM purges WHERE USERNAME = ?   LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);
			$stmt->execute(array($payer));
			$time = time();
			
			if($stmt->rowCount()){
								
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "UPDATE purges SET PURGER2 = ?, PURGE2_TIME = ?, TOTAL = (TOTAL + 1) WHERE USERNAME = ?   LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($receiver,$time,$payer));
				
			}
			else{
				
				$total = 1;
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "INSERT INTO purges (USERNAME,PURGER1,PURGE1_TIME,TOTAL) VALUES(?,?,?,?)";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($payer,$receiver,$time,$total ));
			}
			
			///////////////UPDATE PURGE COUNTER IN MEMBERS TABLE FOR THE  PAYER/////////////////////////////////////////////////////////
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "UPDATE members SET TOTAL_PURGE = (TOTAL_PURGE + 1)  WHERE USERNAME = ?    LIMIT 1";
				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($payer));
				
				
				
			/*********UPDATE TRANSACTION STATUS IN TRANSACTION TABLE TO DECLINED ************************************/
			
				$trx_time = time();
				
				///////////PDO QUERY////////////////////////////////////	

				$sql = "UPDATE transactions SET STATUS='DECLINED', DONATION1='DECLINED', DONATION2='DECLINED', DONATION1_TIME=?, DONATION2_TIME=?  WHERE DONATION_ID=? LIMIT 1 ";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($trx_time,$trx_time,$payer_did));
				
			////DROP A COMMENT FOR THE PURGE PAYER///////////////////////////////////////
			
			///////////PDO QUERY////////////////////////////////////	
			
				$sql = "UPDATE members SET COMMENT1 = ? WHERE USERNAME = ?   LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($comment_4_payer,$payer));
			
		}
	
	
	//////VERY IMPORTANT REMEMBER TO SET CURRENT_PACKAGE,FLOW_DIRECTION, LOOP_STATUS ACCORDINGLY WHEN //////////////
	/////////////////////FORCE CONFIRMING A PURGED USER FROM DONATIONS PAGE/////////////////////////////////////////////
	////////////////UPDATE THE PAYER CURRENT_PACKAGE AND FLOW_DIRECTION IN MEMBERS TABLE/////////////////////////////////////////////////////////////
	///////////PDO QUERY////////////////////////////////////	

		/*********SET RECYCLING DEADLINE******************/
		$recyl_deadline = getRecyclingDeadline();
		
		$sql = "UPDATE members SET CURRENT_PACKAGE = 'NONE', FLOW_DIRECTION = 'NONE', LOOP_STATUS = 'COMPLETE', RECYCLING_DEADLINE = ? WHERE USERNAME = ?   LIMIT 1";

		$stmt = $pdo_conn_login->prepare($sql);
		$stmt->execute(array($recyl_deadline,$payer));
		
		
//////////////PREPARE THE RECEIVER THAT WAS DECLINED PAYMENT FOR RE-MATCHING/////////////////////////////////////////////////////////////////		
		
		if($type == "DECLINATION"){/*************DECLINATION*********************************/
				
		////////////NOW PREPARE THE RECEIVER FOR RE-MATCCH////////////
		
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT MATCH_STATUS FROM ".$donation_table." WHERE ID = ?   LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);
			$stmt->execute(array($rec_did));
			$rec_row = $stmt->fetch(PDO::FETCH_ASSOC);					
			$match_stat = $rec_row["MATCH_STATUS"];
			
			///////IF THE RECEIVER WAS FULLY MATCHED B4 THEN UNMATCH HIM BY 50% AND SET HIS MATCH_STATUS TO SEMI-MATCHED///////////////////////////////////////////////////////
			
			if($match_stat == "MATCHED"){
											
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "UPDATE ".$donation_table." SET AMOUNT_MATCHED = (AMOUNT_MATCHED / 2), AMOUNT_REM = (RETURN_AMOUNT / 2), MATCH_STATUS = 'SEMI-MATCHED' WHERE ID = ?   LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($rec_did));
				
			}///////IF THE RECEIVER WAS NOT FULLY MATCHED B4 THEN UNMATCH HIM BY 100% AND SET HIS MATCH_STATUS TO AWAITING ///////////////
			elseif($match_stat == "SEMI-MATCHED"){
				
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "UPDATE ".$donation_table." SET AMOUNT_MATCHED = 0, AMOUNT_REM = 0, MATCH_STATUS = 'AWAITING' WHERE ID = ?   LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($rec_did));
			}
		}
		elseif($type == "PURGING"){/*************PURGING*********************************/
				
		////////////NOW PREPARE THE RECEIVER FOR PURGE TRIGGERED(PT) RE-MATCCH////////////
		/////////////NOTE THE MATCH_STATUS SET HERE (SEMI-MATCHED-PURGED AND AWAITING-AND-PURGED)////////////
		////////////HOLDS SPECIAL(IN DASHBOARD PAGE WHERE IT ALLOWS USERS WITH FDIRECTION=IN TO SEE THEIR////////////////
		/////////////////PENDING REMATCH//////////////////////////////////
		//// AND ALSO IN DONATIONS AND DONATION HISTORIES PAGES WHERE IT WAS USED FOR SORTING)///
		//////////// MEANING AS IT IS JUST A PLACE HOLDER TO COUNTER MATCH INITIATOR ////////////
		//////////////STATUS(AWAITING AND SEMI-MATCHED)/////////////////////////////////////
		
		///////////PDO QUERY////////////////////////////////////	
			
			$sql = "SELECT MATCH_STATUS FROM ".$donation_table." WHERE ID = ?   LIMIT 1";

			$stmt = $pdo_conn_login->prepare($sql);
			$stmt->execute(array($rec_did));
			$rec_row = $stmt->fetch(PDO::FETCH_ASSOC);					
			$match_stat = $rec_row["MATCH_STATUS"];
			
			///////IF THE RECEIVER WAS FULLY MATCHED B4 THEN UNMATCH HIM BY 50% AND SET HIS MATCH_STATUS///////
			////////////// TO SEMI-MATCHED-PURGED///////////////////////////////////////////////////////
			
			if($match_stat == "MATCHED"){
											
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "UPDATE ".$donation_table." SET AMOUNT_MATCHED = (AMOUNT_MATCHED / 2), AMOUNT_REM = (RETURN_AMOUNT / 2), MATCH_STATUS = 'SEMI-MATCHED-PURGED' WHERE ID = ?   LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($rec_did));
				
			}///////IF THE RECEIVER WAS NOT FULLY MATCHED B4 THEN UNMATCH HIM BY 100%////////////
			////////////////// AND SET HIS MATCH_STATUS TO AWAITING-AND-PURGED ///////////////
			elseif($match_stat == "SEMI-MATCHED"){
				
			///////////PDO QUERY////////////////////////////////////	
				
				$sql = "UPDATE ".$donation_table." SET AMOUNT_MATCHED = 0, AMOUNT_REM = 0, MATCH_STATUS = 'AWAITING-AND-PURGED' WHERE ID = ?   LIMIT 1";

				$stmt = $pdo_conn_login->prepare($sql);
				$stmt->execute(array($rec_did));
			}
		}

/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

	 
 }
 
 
 
 
 

 
 
 
 
 
 
 
 //////////FUNTION TO DO FIRST MATCHING/////////////
 
 function doFirstMatching(){
		 
			 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");


	///////////GET DOMAIN OR HOMEPAGE///////////////////////
		$getdomain = getDomain();
		$domain_name = getDomainName();

	/***************************NOTE: THE FIRST MATCHING CRITERIAS ARE:***********************************************
				MATCH_STATUS="AWAITING", 
				CONFIRMED="YES",
				LOOP_STATUS="SEMI-COMPLETE" 				
						
	*****************************************************************************************************************/

	$order_used=$admin_door_qry=$admin_door_limit="";
	
	$username = $_SESSION["username"];
	
////////////////SELECT MEMBERS THAT HAVE MADE DONATION AND RECEIVED CONFIRMATION AND MATCH THEM FOR PAYMENT//////////////////////////////////////////////////////
					
					/////SET ORDER USED TO MERGE////////////
					
					//$order_used = ' ORDER BY TIME_OF_PLEDGE ASC';
					$order_used = ' ORDER BY CONFIRM_TIME ASC';
					
					
					//////DEFINE ARRAY OF PACKAGES SO YOU CAN LOOP THROUGH ALL PACKAGES AND DO MATCHING///////////////////////////////////
					
					$package_arr = getPackagesArray();


				//////////////LOOP THROUGH EACH PACKAGES AND DO MATCHING////////////////////////////////////
				
				foreach($package_arr as $pack_name){
					
					$donation_table = 'euro_'.strtolower($pack_name).'_donations';
					$matching_table = 'euro_'.strtolower($pack_name).'_matching';
					
					///////////////GET THE FIRST 50 PEOPLE DUE TO RECEIVE DONATIONS//////////////////////////////////////////////////////////////////////////
					/****PPLE WHO HAS BEEN CONFIRMED AND HAVE NOT BEEN MATCHED THE FIRST TIME ONLY*********************************/
					/////////PDO QUERY////////////////////////////////////

					$sql = "SELECT * FROM ".$donation_table."  WHERE LOOP_STATUS = 'SEMI-COMPLETE' AND CONFIRMED = 'YES' AND MATCH_STATUS = 'AWAITING'  ".$order_used."  LIMIT 50";

					$stmt1 = $pdo_conn_login->prepare($sql);
					$stmt1->execute();
					if($stmt1->rowCount()){
						
						while($receiver_rows = $stmt1->fetch(PDO::FETCH_ASSOC)){/////FOR EACH CONFIRMED DONATORS //////////////////////////
							
							$match_limit="";
							
							$amount_pledged = $receiver_rows["AMOUNT_PLEDGED"];
							$receiver = $receiver_rows["USERNAME"];	
							$rec_did = $receiver_rows["ID"];
							
							/***********CREATE ADMIN BACK DOOR IN FIRST MATCHING SO ALL ADMINS WILL BE MATCHED****************
							*****************FULLY WITHOUT WAITING FOR SECOND MATCHING TO RUN*****************/
								if(getUserPrivilege($receiver) == "ADMIN"){
																		
									$match_limit = " LIMIT 2";
									
								}////IF NOT ADMIN THEN MATCH BY ONE ONLY////////////////////////////////////////
								else{
									
									$match_limit = " LIMIT 1";
								}
																											
							///////////GET ONE MEMBER (OR TWO MEMBERS IF PRIVILEGE="ADMIN") THAT HAS MADE A CORRESPONDING PLEDGE AND MERGE THEM TOGETHER//////////////////////
							/////////PDO QUERY////////////////////////////////////	
				
							$sql = "SELECT * FROM ".$donation_table."  WHERE MATCH_STATUS = 'AWAITING' AND CONFIRMED = 'PENDING' AND AMOUNT_PLEDGED = ? ".$match_limit;

							$stmt2 = $pdo_conn_login->prepare($sql);
							$stmt2->execute(array($amount_pledged));
							$match_found = $stmt2->rowCount();
							$counter = 1;
							
							if($match_found){
								while($payer_rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
									
									$payer = $payer_rows["USERNAME"];
									$amt_to_pay = $payer_rows["AMOUNT_PLEDGED"];
									$payer_did = $payer_rows["ID"];								
									$payer_deadline = (time() + (3600*6));///////////////SET PAYMENT DEADLINE TO 6 HRS/////
									//$payer_deadline = (time() + (300));///////////////SET PAYMENT DEADLINE TO 6 HRS/////
									
									/////////ADD THE MATCHES TO DATABASE//////////////////////////////////////////////////////////
									
									/////////PDO QUERY////////////////////////////////////	
						
									$sql = "INSERT INTO ".$matching_table."  (PAYER_DID,PAYER_USERNAME,AMOUNT_TO_PAY,PAYER_DEADLINE,REC_DID,REC_USERNAME) VALUES(?,?,?,?,?,?)";
									$stmt3 = $pdo_conn_login->prepare($sql);
									$stmt3->execute(array($payer_did,$payer,$amt_to_pay,$payer_deadline,$rec_did,$receiver));
									
									/////////////UPDATE AMOUNT MATCHED AND AMOUNT REMAINING FOR THE RECEIVER IN DONATION TABLE////////////////////////////////////////////////////
									
									/////////PDO QUERY////////////////////////////////////	
						
									$sql = "UPDATE ".$donation_table." SET AMOUNT_MATCHED = (AMOUNT_MATCHED + ?), AMOUNT_REM = (RETURN_AMOUNT - AMOUNT_MATCHED) WHERE ID = ? LIMIT 1";
									$stmt4 = $pdo_conn_login->prepare($sql);
									$stmt4->execute(array($amt_to_pay,$rec_did));
									
									/////////////SET MATCH_STATUS TO MATCHED FOR THE PAYER IN DONATION TABLE////////////////////////////////////////////////////
									
									/////////PDO QUERY////////////////////////////////////	
						
									$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = 'MATCHED' WHERE ID = ? LIMIT 1";
									$stmt5 = $pdo_conn_login->prepare($sql);
									$stmt5->execute(array($payer_did));
									
									/////////////CHECK IF THE RECEIVER HAS BEEN FULLY MATCHED/////////////////////////////////////////////////////////////////
									
										/////////PDO QUERY////////////////////////////////////	
						
									$sql = "SELECT MATCH_STATUS FROM ".$donation_table."  WHERE ID = ? LIMIT 1";
									$stmt6 = $pdo_conn_login->prepare($sql);
									$stmt6->execute(array($rec_did));
									$chk_row = $stmt6->fetch(PDO::FETCH_ASSOC);
									$chk_full_match = $chk_row["MATCH_STATUS"];
									
									if($chk_full_match == "AWAITING"){
										
										/////////////SET MATCH_STATUS TO SEMI-MATCHED FOR THE RECEIVER IN DONATION TABLE////////////////////////////////////////////////////
										
										/////////PDO QUERY////////////////////////////////////	
							
										$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = 'SEMI-MATCHED' WHERE ID = ? LIMIT 1";
										$stmt7 = $pdo_conn_login->prepare($sql);
										$stmt7->execute(array($rec_did));
										
									}
									elseif($chk_full_match == "SEMI-MATCHED"){
										
										/////////////SET MATCH_STATUS TO MATCHED FOR THE RECEIVER IN DONATION TABLE////////////////////////////////////////////////////
										
										/////////PDO QUERY////////////////////////////////////	
							
										$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = 'MATCHED' WHERE ID = ? LIMIT 1";
										$stmt7 = $pdo_conn_login->prepare($sql);
										$stmt7->execute(array($rec_did));
										
									}
																											
									////////////////SEND NOTIFICATION EMAIL TO A PAYER THAT HAS BEEN MATCHED////////////////////////////////////////////////////////////
									
											///////////PDO QUERY////////////////////////////////////	
										
										$sql = "SELECT EMAIL FROM members WHERE USERNAME = ? LIMIT 1";

										$stmt8 = $pdo_conn_login->prepare($sql);

										$stmt8->execute(array($payer));
										
										$p_row = $stmt8->fetch(PDO::FETCH_ASSOC);
											
										$to = $p_row["EMAIL"];									
										 
										 $message = 'Hello '.$payer.',<br/> Earlier you made a request to provide help and here it is.<br/> Please login into your dashboard now and redeem your pledge<br/>
													Please endeavor to make disbursement before your payment deadline below elapses else your  account will be suspended by the system.
													<h2>YOUR PAYMENT DEADLINE IS:'.dateFormatStyle($payer_deadline).'</h2>
													';
										 
										 $subject = 'YOUR ORDER TO PROVIDE HELP HAS BEEN MATCHED - '.$domain_name;
										
										$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
													NOTE: This email was sent to you because you pledged a donation at ".$getdomain.". 
											  please kindly ignore this message if otherwise.\n\n\n Please do not reply to this email.\n\n\nThank you";
														  
										 $headers="from: DoNotReply@".$domain_name."\r\n";
										 sendHTMLMail($to,$subject,$message,$footer,$headers);
										 
										 
										 ////////////SEND NOTIFICATION EMAIL TO THE RECEIVER/////////////////////////////////////////////////////////////////
																		
											///////////PDO QUERY////////////////////////////////////	
										
										$sql = "SELECT EMAIL FROM members WHERE USERNAME = ? LIMIT 1";

										$stmt9 = $pdo_conn_login->prepare($sql);

										$stmt9->execute(array($receiver));
										
										$r_row = $stmt9->fetch(PDO::FETCH_ASSOC);
											
										$to = $r_row["EMAIL"];									
										 
										 $message = 'Hello '.$receiver.',<br/> You have been matched to get help( your capital returns), Please login into your dashboard and contact your
													 payer to make disbursement and please endeavor to confirm him/her as soon as you get the disbursement. 
													<h2>YOUR PAYER\'S DEADLINE IS:'.dateFormatStyle($payer_deadline).'</h2>
													';
										 
										 $subject = 'YOU HAVE BEEN MATCHED TO GET HELP( YOUR CAPITAL RETURNS) - '.$domain_name;
										
										$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
													NOTE: This email was sent to you because you redeemed a donation at ".$getdomain.". 
											  please kindly ignore this message if otherwise.\n\n\n Please do not reply to this email.\n\n\nThank you";
														  
										 $headers="from: DoNotReply@".$domain_name."\r\n";
										 sendHTMLMail($to,$subject,$message,$footer,$headers);
										 
										//////VERY IMPORTANT DURING CASE OF A PREVIOUSLY SEMI-MATCHED RECEIVER///////
										////////// TO AVOID MATCHING HIM 3X ENSURE TO BREAK OUT OF THE LOOP//////////////////////////////////////////
										 if($chk_full_match == "SEMI-MATCHED"){	
												
												break;
												
										 }
									
								}
							}
							/////IF NO MATCH IS FOUND OR THE MATCH IS INCOMPLETE THEN //////////////////////////////////////////////////////
							if(!$match_found || ($match_found < 2)){
								
							}
							
						}
					}
				}
				
				
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

		 
 }
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 

//////////FUNTION TO DO SECOND MATCHING/////////////
 
 function doSecondMatching(){
	 
			 
	//////////PDO CONNECTION//////////////////

	$pdo_conn_login = pdoConn("loginform");


	///////////GET DOMAIN OR HOMEPAGE///////////////////////
		$getdomain = getDomain();
		$domain_name = getDomainName();


	/***************************NOTE: THE SECOND MATCHING CRITERIAS ARE:***********************************************
				MATCH_STATUS="SEMI-MATCHED", 
				CONFIRMED="YES",
				LOOP_STATUS="SEMI-COMPLETE" 
	*****************************************************************************************************************/


	$order_used="";
		
		$username = $_SESSION["username"];
	
////////////////SELECT MEMBERS THAT HAVE MADE DONATION AND RECEIVED CONFIRMATION AND MATCH THEM FOR PAYMENT//////////////////////////////////////////////////////
					
					/////SET ORDER USED TO MERGE////////////
					
					//$order_used = ' ORDER BY TIME_OF_PLEDGE ASC';
					$order_used = ' ORDER BY CONFIRM_TIME ASC';
					
					//////DEFINE ARRAY OF PACKAGES SO YOU CAN LOOP THROUGH ALL PACKAGES AND DO MATCHING///////////////////////////////////
					
					$package_arr = getPackagesArray();


				//////////////LOOP THROUGH EACH PACKAGES AND DO MATCHING////////////////////////////////////
				
				foreach($package_arr as $pack_name){
					
					$donation_table = 'euro_'.strtolower($pack_name).'_donations';
					$matching_table = 'euro_'.strtolower($pack_name).'_matching';
					
					///////////////GET THE FIRST 50 PEOPLE DUE TO RECEIVE THEIR SECOND  DONATION (RETURNS ON INVESTMENT)//////////////////////////////////////////////////////////////////////////
					/****PPLE WHO HAS BEEN CONFIRMED AND HAVE BEEN MATCHED FIRST TIME ONLY*********************************/
					/////////PDO QUERY////////////////////////////////////

					$sql = "SELECT * FROM ".$donation_table."  WHERE LOOP_STATUS = 'SEMI-COMPLETE' AND CONFIRMED = 'YES' AND MATCH_STATUS = 'SEMI-MATCHED'  ".$order_used."  LIMIT 50";

					$stmt1 = $pdo_conn_login->prepare($sql);
					$stmt1->execute();
					if($stmt1->rowCount()){
						
						while($receiver_rows = $stmt1->fetch(PDO::FETCH_ASSOC)){/////FOR EACH CONFIRMED DONATORS //////////////////////////
							
							$amount_pledged = $receiver_rows["AMOUNT_PLEDGED"];
							$receiver = $receiver_rows["USERNAME"];	
							$rec_did = $receiver_rows["ID"];											
							
													
							///////////GET ONE MEMBER THAT HAS MADE A CORRESPONDING PLEDGE AND MERGE THEM TOGETHER//////////////////////
							/////////PDO QUERY////////////////////////////////////	
				
							$sql = "SELECT * FROM ".$donation_table."  WHERE MATCH_STATUS = 'AWAITING' AND CONFIRMED = 'PENDING' AND AMOUNT_PLEDGED = ? LIMIT 1";

							$stmt2 = $pdo_conn_login->prepare($sql);
							$stmt2->execute(array($amount_pledged));
							$match_found = $stmt2->rowCount();
							$counter = 1;
							
							if($match_found){
								while($payer_rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
									
									$payer = $payer_rows["USERNAME"];
									$amt_to_pay = $payer_rows["AMOUNT_PLEDGED"];
									$payer_did = $payer_rows["ID"];								
									$payer_deadline = (time() + (3600*6));///////////////SET PAYMENT DEADLINE TO 6 HRS/////
									//$payer_deadline = (time() + (300));///////////////SET PAYMENT DEADLINE TO 6 HRS/////
									
									/////////ADD THE MATCHES TO DATABASE//////////////////////////////////////////////////////////
									
									/////////PDO QUERY////////////////////////////////////	
						
									$sql = "INSERT INTO ".$matching_table."  (PAYER_DID,PAYER_USERNAME,AMOUNT_TO_PAY,PAYER_DEADLINE,REC_DID,REC_USERNAME) VALUES(?,?,?,?,?,?)";
									$stmt3 = $pdo_conn_login->prepare($sql);
									$stmt3->execute(array($payer_did,$payer,$amt_to_pay,$payer_deadline,$rec_did,$receiver));
									
									/////////////UPDATE AMOUNT MATCHED AND AMOUNT REMAINING FOR THE RECEIVER IN DONATION TABLE////////////////////////////////////////////////////
									
									/////////PDO QUERY////////////////////////////////////	
						
									$sql = "UPDATE ".$donation_table." SET AMOUNT_MATCHED = (AMOUNT_MATCHED + ?), AMOUNT_REM = (RETURN_AMOUNT - AMOUNT_MATCHED) WHERE ID = ? LIMIT 1";
									$stmt4 = $pdo_conn_login->prepare($sql);
									$stmt4->execute(array($amt_to_pay,$rec_did));
									
									/////////////SET MATCH_STATUS TO MATCHED FOR THE PAYER IN DONATION TABLE////////////////////////////////////////////////////
									
									/////////PDO QUERY////////////////////////////////////	
						
									$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = 'MATCHED' WHERE ID = ? LIMIT 1";
									$stmt5 = $pdo_conn_login->prepare($sql);
									$stmt5->execute(array($payer_did));
									
									/////////////CHECK IF THE RECEIVER HAS BEEN FULLY MATCHED/////////////////////////////////////////////////////////////////
									
										/////////PDO QUERY////////////////////////////////////	
						
									$sql = "SELECT MATCH_STATUS FROM ".$donation_table."  WHERE ID = ? LIMIT 1";
									$stmt6 = $pdo_conn_login->prepare($sql);
									$stmt6->execute(array($rec_did));
									$chk_row = $stmt6->fetch(PDO::FETCH_ASSOC);
									$chk_full_match = $chk_row["MATCH_STATUS"];
									
									if($chk_full_match == "AWAITING"){
										
										/////////////SET MATCH_STATUS TO SEMI-MATCHED FOR THE RECEIVER IN DONATION TABLE////////////////////////////////////////////////////
										
										/////////PDO QUERY////////////////////////////////////	
							
										$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = 'SEMI-MATCHED' WHERE ID = ? LIMIT 1";
										$stmt7 = $pdo_conn_login->prepare($sql);
										$stmt7->execute(array($rec_did));
										
									}
									elseif($chk_full_match == "SEMI-MATCHED"){
										
										/////////////SET MATCH_STATUS TO MATCHED FOR THE RECEIVER IN DONATION TABLE////////////////////////////////////////////////////
										
										/////////PDO QUERY////////////////////////////////////	
							
										$sql = "UPDATE ".$donation_table." SET MATCH_STATUS = 'MATCHED' WHERE ID = ? LIMIT 1";
										$stmt7 = $pdo_conn_login->prepare($sql);
										$stmt7->execute(array($rec_did));
										
										
									}
																											
									////////////////SEND NOTIFICATION EMAIL TO A PAYER THAT HAS BEEN MATCHED////////////////////////////////////////////////////////////
									
											///////////PDO QUERY////////////////////////////////////	
										
										$sql = "SELECT EMAIL FROM members WHERE USERNAME = ? LIMIT 1";

										$stmt8 = $pdo_conn_login->prepare($sql);

										$stmt8->execute(array($payer));
										
										$p_row = $stmt8->fetch(PDO::FETCH_ASSOC);
											
										$to = $p_row["EMAIL"];									
										 
										 $message = 'Hello '.$payer.',<br/> Earlier you made a request to provide help and here it is.<br/> Please login into your dashboard now and redeem your pledge<br/>
													Please endeavor to make disbursement before your payment deadline below elapses else your  account will be suspended by the system.
													<h2>YOUR PAYMENT DEADLINE IS:'.dateFormatStyle($payer_deadline).'</h2>
													';
										 
										 $subject = 'YOUR ORDER TO PROVIDE HELP HAS BEEN MATCHED - '.$domain_name;
										
										$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
													NOTE: This email was sent to you because you pledged a donation at ".$getdomain.". 
											  please kindly ignore this message if otherwise.\n\n\n Please do not reply to this email.\n\n\nThank you";
														  
										 $headers="from: DoNotReply@".$domain_name."\r\n";
										 sendHTMLMail($to,$subject,$message,$footer,$headers);
										 
										 
										 ////////////SEND NOTIFICATION EMAIL TO THE RECEIVER/////////////////////////////////////////////////////////////////
																		
											///////////PDO QUERY////////////////////////////////////	
										
										$sql = "SELECT EMAIL FROM members WHERE USERNAME = ? LIMIT 1";

										$stmt9 = $pdo_conn_login->prepare($sql);

										$stmt9->execute(array($receiver));
										
										$r_row = $stmt9->fetch(PDO::FETCH_ASSOC);
											
										$to = $r_row["EMAIL"];									
										 
										 $message = 'Hello '.$receiver.',<br/> You have been matched to get help( your investment returns), Please login into your dashboard and contact your
													 payer to make disbursement and please endeavor to confirm him/her as soon as you get the disbursement. 
													<h2>YOUR PAYER\'S DEADLINE IS:'.dateFormatStyle($payer_deadline).'</h2>
													';
										 
										 $subject = 'YOU HAVE BEEN MATCHED TO GET HELP (YOUR INVESTMENT RETURNS) - '.$domain_name;
										
										$footer = "<a href='".$getdomain."'  class='links'>".$domain_name."</a>-Copyright &copy; ". Date('Y')  ." All Rights Reserved.<br/>
													NOTE: This email was sent to you because you redeemed a donation at ".$getdomain.". 
											  please kindly ignore this message if otherwise.\n\n\n Please do not reply to this email.\n\n\nThank you";
														  
										 $headers="from: DoNotReply@".$domain_name."\r\n";
										 sendHTMLMail($to,$subject,$message,$footer,$headers);
										 
										 //////VERY IMPORTANT DURING CASE OF A PREVIOUSLY SEMI-MATCHED RECEIVER///////
										////////// TO AVOID MATCHING HIM 3X ENSURE TO BREAK OUT OF THE LOOP//////////////////////////////////////////
										 if($chk_full_match == "SEMI-MATCHED"){	
												
												break;
												
										 }
									
									
								}
							}
							/////IF NO MATCH IS FOUND OR THE MATCH IS INCOMPLETE THEN //////////////////////////////////////////////////////
							if(!$match_found || ($match_found < 2)){
								
							}
							
						}
					}
				}
				
				
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

		 
 }
 
 
 
 
 
 
 
 
 
 
 
 
 
////////////////////////GET USER LOCATION////////////////////////////////////////////////////////////////////////////////////////////////
 
 function getLocation(){
	 

//////////PDO CONNECTION//////////////////

$pdo_conn_login = pdoConn("loginform");
	
	$loc="";
	
	$username = $_SESSION["username"];
	
/////////PDO QUERY////////////////////////////////////	
	
				$sql = "SELECT COUNTRY,STATE FROM members WHERE USERNAME LIKE ? LIMIT 1";

				$stmt1 = $pdo_conn_login->prepare($sql);
				$stmt1->execute(array($username ));
				
	if($stmt1->rowCount()){
		
		$row = $stmt1->fetch(PDO::FETCH_ASSOC);
		
		$loc = $row["STATE"];
		
		if($loc && $row["COUNTRY"])
			$loc .= ', '.$row["COUNTRY"];
		
		
	}
	
/////////CLOSE PDO CONNECTION///////////////////////////////////		
	$pdo_conn = null;
	$pdo_conn_login = null;

		
	 return $loc;
	 
	 
	 
	 
 }
 
 
 









//////////////////////////////////FUNCTION TO HANDLE URL IN PM SYSTEM////////////////////////////////////////////

function pmHandler($row, $type){
			
		$username = $_SESSION["username"];
		$id_db = $row["ID"];
		$sender_db = $row["SENDER"];
		$subject_db = preg_replace("#(user-profile\?cuser\=".$username.")#isU","user-profile",$row["MESSAGE_SUBJECT"]);
		
		if(trim(strtolower($type)) == "inbox")
			$inbox_db = preg_replace("#(user-profile\?cuser\=".$username.")#isU","user-profile",$row["INBOX"]);
		
		else if(trim(strtolower($type)) == "old_inbox")
			$inbox_db = preg_replace("#(user-profile\?cuser\=".$username.")#isU","user-profile",$row["OLD_INBOX"]);
		
		$time_db = $row["TIME"];

//////////////FORMAT THE WAY POST DATES ARE SHOWN////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
		$post_date =  dateFormatStyle($time_db);
		
		$subject="";$inbox="";
		
		//$data = '<div id="messageWrapper" class="messageWrapper clear"><header id="mssgsender" class="clear">sent by: <b><a href="user-profile?cuser='.$sender_db.'" class="senderinbox links" id="senderinbox"  >'.$sender_db.'</a></b> ('.$post_date.')  <a href="send-pm?pm='.$id_db.'" class="reply_pm">Reply</a></header><hr/><p id="messagesubject" class="messagesubject"><span id="subjectshow">Subject:</span> <span class="yellow">'.$subject_db.'</span></p><span><input   type="checkbox"   mid="'.$id_db.'"   name="delchk"  class="checkbox_inbox checkbox"></span><div  class="mssgcontents" id="mssgcontents">'.$inbox_db.'</div><a href="send-pm?pm='.$id_db.'" class="reply_pm">Reply</a></div>';
		$data = '<div id="messageWrapper" class="messageWrapper clear"><header id="mssgsender" class="clear">sent by: <b><a href="javascript:void(0)" class="senderinbox links" id="senderinbox"  >'.$sender_db.'</a></b> ('.$post_date.')  <a href="send-pm?pm='.$id_db.'" class="reply_pm">Reply</a></header><hr/><p id="messagesubject" class="messagesubject"><span id="subjectshow">Subject:</span> <span class="yellow">'.$subject_db.'</span></p><span><input   type="checkbox"   mid="'.$id_db.'"   name="delchk"  class="checkbox_inbox checkbox"></span><div  class="mssgcontents" id="mssgcontents">'.$inbox_db.'</div><a href="send-pm?pm='.$id_db.'" class="reply_pm">Reply</a></div>';
		
		
			return $data;
	
	
	
}










////////////HANDLE TO SEND EMAIL IN HTML/////////////////////////////////////////

function sendHTMLMail($to,$subject,$content,$footer,$headers){
	
	
///////////GET DOMAIN OR HOMEPAGE///////////////////////
	$getdomain = getDomain();
	$domain_name = getDomainName();
	$css = ""; 
	
	$subject = preg_replace("#(\\n)#isU", "<br/>", $subject);
	$subject = preg_replace("#(https?\://www\.)#isU", "", $subject);
	$content = preg_replace("#(\\n)#isU", "<br/>", $content);
	
	
	/*if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "localhost"){ 
		
		$css = '<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<link rel=stylesheet type="text/css" href="http://localhost/WEALTH/wealth-island_email_style_sheet.css"></link>';
		
	}
	else{
		
		$css = '<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<link rel="stylesheet" type="text/css" href="http://wealth-island.000webhostapp.com/styles/main/css/wealth-island_email_style_sheet.css"></link>';
				
	max-width:60%;border:none;max-height:100px;
	}
	<h1 style="text-align:center;background:#8B0000;color:#fff;padding:10px;
								margin-bottom:15px;margin-top:15px;">'.strtoupper($domain_name).'</h1>
*/

	$message = '
				<html>				
				<body>
					<div class="email_wrapper">
						<header class="email_header">							
							<h1 style="text-align:center;background:#8B0000;color:#fff;padding:10px;
								margin-bottom:15px;margin-top:15px;"><a href="<?=$getdomain;  ?>"><img style="max-width:80%;border:none;max-height:70px;"
								src="'.$getdomain.'/wealth-island-images/icons/pf-logo-0.png" alt="logo" /> </a></h1>
						</header>
						<div style="padding-bottom:20px;">
							'.wordwrap($content).'
							<span style="color:#808000;">Best regards,<br/>
							'.$domain_name.' Team</span>
						</div>
						<footer style="text-align:center;background:#E8ECE0;padding:10px 10px;font-size:0.8em;margin:0 auto;margin-bottom:10px;">
							'.$footer.'
						</footer>									
					</div>
				</body>
				</html>';
				
	$inmail_domain = preg_replace("#(https?\://www\.)#isU", "", $getdomain);
	
	// Always set content-type when sending HTML email
	$from = 'ProvidentFunds <no-reply@'.($inmail_domain).'>';
	$from_2 = '<no-reply@'.($inmail_domain).'>';
	$mailer = ($inmail_domain); 
	

	$headers = "From: ".($from)."\r\n";
	$headers .= "Reply-To: ".($from_2)."\r\n";
	$headers .= "Return-Path: ".($from_2)."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html;charset=UTF-8\r\n";
	$headers .= "X-Priority: 1\r\n";
	$headers .= "X-MSMail-Priority: High\r\n";
	$headers .= "Importance: High\r\n";
	$headers .= "X-Mailer: PHP".phpversion()."\r\n";
	

	$stat = mail($to,$subject,$message,$headers);
	
	
}



?>