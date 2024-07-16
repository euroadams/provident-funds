
////////////////////FUNCTION TO RETURN REFERENCE TO document.getElementById//////////////////////////////////////////////

function dom(ele){
	
	return document.getElementById(ele);
	
}


/////FUNTION TO BLINK ELEMENTS//////////////////////////////////////

function blink(){
	
	$(".blink").fadeOut(10000);
	$(".blink").fadeIn(10000);
	
	
}

setInterval(blink, 1000);////BLINK EVERY SECONDS/////////

/*****GET RECYCLE DEADLINE COUNTER*********************************/

function startCountDown(event_time){
	
	function updateTimer(){
				
				/////CONVERT JAVASCRIPT TIME NOW TO PHP TIME NOW BOTH IN SECONDS//////////////////////
				var time_now = Math.floor((new Date().getTime())/1000);
				
				if(time_now >= event_time)
					var rem = (time_now - event_time);
				else
					if(time_now < event_time)
						var rem = (event_time - time_now);
					
								
				var s = Math.floor(rem);
				var m = Math.floor(s/60);
				var h = Math.floor(m/60);
				var d = Math.floor(h/24);
				
				h %= 24;
				m %= 60;
				s %= 60;
				
				//if(d > 1 || d == 0)
				//	var timer = d + "days " + h+"H : "+ m+"M : "+s + "S";
				//else
				//	var timer = d + "day " + h+"H : "+ m+"M : "+s + "S";
				
				
				var timer =	'<h1 class="lgreen">Recyling Deadline:</h1>\
							<div id="clockdiv">\
							  <div>\
								<span class="days">'+ d +'</span>\
								<div class="smalltext">Days</div>\
							  </div>\
							  <div>\
								<span class="hours">'+ h +'</span>\
								<div class="smalltext">Hours</div>\
							  </div>\
							  <div>\
								<span class="minutes">'+ m +'</span>\
								<div class="smalltext">Minutes</div>\
							  </div>\
							  <div>\
								<span class="seconds">'+ s +'</span>\
								<div class="smalltext">Seconds</div>\
							  </div>\
							</div>';
					
				if(rem <= 0 || (event_time < time_now)){
					clearInterval(active_interval);
					
					timer = '<span class="red">TIME OUT!!!</span>';
					$("div.timer_wrapper").html(timer);
					
				}
				else{
					
					$("div.timer_wrapper").html(timer);
				}
					
				
				
	}
	
	updateTimer();
	var active_interval = setInterval(updateTimer,1000);
	
}




/*****GET PAYER DEADLINE COUNTER*********************************/

function startPayerCountDown(event_time){
	
	function updatePayerTimer(){
				
				/////CONVERT JAVASCRIPT TIME NOW TO PHP TIME NOW BOTH IN SECONDS//////////////////////
				var time_now = Math.floor((new Date().getTime())/1000);
				
				if(time_now >= event_time)
					var rem = (time_now - event_time);
				else
					if(time_now < event_time)
						var rem = (event_time - time_now);
					
								
				var s = Math.floor(rem);
				var m = Math.floor(s/60);
				var h = Math.floor(m/60);
				var d = Math.floor(h/24);
				
				h %= 24;
				m %= 60;
				s %= 60;
				
				//if(d > 1 || d == 0)
				//	var timer = d + "days " + h+"H : "+ m+"M : "+s + "S";
				//else
				//	var timer = d + "day " + h+"H : "+ m+"M : "+s + "S";
				
				
				var timer =	'<span class="lgreen">TIME LEFT:</span><br/>\
							<div id="clockdiv">\
							  <div>\
								<span class="days">'+ d +'</span>\
								<div class="smalltext">Days</div>\
							  </div>\
							  <div>\
								<span class="hours">'+ h +'</span>\
								<div class="smalltext">Hours</div>\
							  </div>\
							  <div>\
								<span class="minutes">'+ m +'</span>\
								<div class="smalltext">Minutes</div>\
							  </div>\
							  <div>\
								<span class="seconds">'+ s +'</span>\
								<div class="smalltext">Seconds</div>\
							  </div>\
							</div>';
					
				if(rem <= 0 || (event_time < time_now)){
					clearInterval(active_interval);
					
					timer = '<span class="red">TIME OUT!!!</span>';
					$("h2.pay_timer_wrapper").html(timer);
					
				}
				else{
					
					$("h2.pay_timer_wrapper").html(timer);
				}
					
				
				
	}
	
	updatePayerTimer();
	var active_interval = setInterval(updatePayerTimer,1000);
	
}


/*****GET LORD LAUNCH COUNTER*********************************/

function startLordCountDown(event_time){
	
	function updateLordTimer(){
				
				/////CONVERT JAVASCRIPT TIME NOW TO PHP TIME NOW BOTH IN SECONDS//////////////////////
				var time_now = Math.floor((new Date().getTime())/1000);
				
				if(time_now >= event_time)
					var rem = (time_now - event_time);
				else
					if(time_now < event_time)
						var rem = (event_time - time_now);
					
								
				var s = Math.floor(rem);
				var m = Math.floor(s/60);
				var h = Math.floor(m/60);
				var d = Math.floor(h/24);
				
				h %= 24;
				m %= 60;
				s %= 60;
				
				//if(d > 1 || d == 0)
				//	var timer = d + "days " + h+"H : "+ m+"M : "+s + "S";
				//else
				//	var timer = d + "day " + h+"H : "+ m+"M : "+s + "S";
				
				
				var timer =	'<span class="lgreen">LAUNCH TIME:</span><br/>\
							<div id="clockdiv">\
							  <div>\
								<span class="days">'+ d +'</span>\
								<div class="smalltext">Days</div>\
							  </div>\
							  <div>\
								<span class="hours">'+ h +'</span>\
								<div class="smalltext">Hours</div>\
							  </div>\
							  <div>\
								<span class="minutes">'+ m +'</span>\
								<div class="smalltext">Minutes</div>\
							  </div>\
							  <div>\
								<span class="seconds">'+ s +'</span>\
								<div class="smalltext">Seconds</div>\
							  </div>\
							</div>';
					
				if(rem <= 0 || (event_time < time_now)){
					clearInterval(active_interval);
					
					timer = '<span class="red">TIME OUT!!!</span>';
					$("div.lrd_timer_wrapper").html(timer);
					
				}
				else{
					
					$("div.lrd_timer_wrapper").html(timer);
				}
					
				
				
	}
	
	updateLordTimer();
	var active_interval = setInterval(updateLordTimer,1000);
	
}


/*****GET MASTER LAUNCH COUNTER*********************************/

function startMasterCountDown(event_time){
	
	function updateMasterTimer(){
				
				/////CONVERT JAVASCRIPT TIME NOW TO PHP TIME NOW BOTH IN SECONDS//////////////////////
				var time_now = Math.floor((new Date().getTime())/1000);
				
				if(time_now >= event_time)
					var rem = (time_now - event_time);
				else
					if(time_now < event_time)
						var rem = (event_time - time_now);
					
								
				var s = Math.floor(rem);
				var m = Math.floor(s/60);
				var h = Math.floor(m/60);
				var d = Math.floor(h/24);
				
				h %= 24;
				m %= 60;
				s %= 60;
				
				//if(d > 1 || d == 0)
				//	var timer = d + "days " + h+"H : "+ m+"M : "+s + "S";
				//else
				//	var timer = d + "day " + h+"H : "+ m+"M : "+s + "S";
				
				
				var timer =	'<span class="lgreen">LAUNCH TIME:</span><br/>\
							<div id="clockdiv">\
							  <div>\
								<span class="days">'+ d +'</span>\
								<div class="smalltext">Days</div>\
							  </div>\
							  <div>\
								<span class="hours">'+ h +'</span>\
								<div class="smalltext">Hours</div>\
							  </div>\
							  <div>\
								<span class="minutes">'+ m +'</span>\
								<div class="smalltext">Minutes</div>\
							  </div>\
							  <div>\
								<span class="seconds">'+ s +'</span>\
								<div class="smalltext">Seconds</div>\
							  </div>\
							</div>';
					
				if(rem <= 0 || (event_time < time_now)){
					clearInterval(active_interval);
					
					timer = '<span class="red">TIME OUT!!!</span>';
					$("div.mst_timer_wrapper").html(timer);
					
				}
				else{
					
					$("div.mst_timer_wrapper").html(timer);
				}
					
				
				
	}
	
	updateMasterTimer();
	var active_interval = setInterval(updateMasterTimer,1000);
	
}


/*****GET ROYAL LAUCH COUNTER*********************************/

function startRoyalCountDown(event_time){
	
	function updateRoyalTimer(){
				
				/////CONVERT JAVASCRIPT TIME NOW TO PHP TIME NOW BOTH IN SECONDS//////////////////////
				var time_now = Math.floor((new Date().getTime())/1000);
				
				if(time_now >= event_time)
					var rem = (time_now - event_time);
				else
					if(time_now < event_time)
						var rem = (event_time - time_now);
					
								
				var s = Math.floor(rem);
				var m = Math.floor(s/60);
				var h = Math.floor(m/60);
				var d = Math.floor(h/24);
				
				h %= 24;
				m %= 60;
				s %= 60;
				
				//if(d > 1 || d == 0)
				//	var timer = d + "days " + h+"H : "+ m+"M : "+s + "S";
				//else
				//	var timer = d + "day " + h+"H : "+ m+"M : "+s + "S";
				
				
				var timer =	'<span class="lgreen">LAUNCH TIME:</span><br/>\
							<div id="clockdiv">\
							  <div>\
								<span class="days">'+ d +'</span>\
								<div class="smalltext">Days</div>\
							  </div>\
							  <div>\
								<span class="hours">'+ h +'</span>\
								<div class="smalltext">Hours</div>\
							  </div>\
							  <div>\
								<span class="minutes">'+ m +'</span>\
								<div class="smalltext">Minutes</div>\
							  </div>\
							  <div>\
								<span class="seconds">'+ s +'</span>\
								<div class="smalltext">Seconds</div>\
							  </div>\
							</div>';
					
				if(rem <= 0 || (event_time < time_now)){
					clearInterval(active_interval);
					
					timer = '<span class="red">TIME OUT!!!</span>';
					$("div.roy_timer_wrapper").html(timer);
					
				}
				else{
					
					$("div.roy_timer_wrapper").html(timer);
				}
					
				
				
	}
	
	updateRoyalTimer();
	var active_interval = setInterval(updateRoyalTimer,1000);
	
}


/*****GET ULTIMATE LAUNCH COUNTER*********************************/

function startUltimateCountDown(event_time){
	
	function updateUltimateTimer(){
				
				/////CONVERT JAVASCRIPT TIME NOW TO PHP TIME NOW BOTH IN SECONDS//////////////////////
				var time_now = Math.floor((new Date().getTime())/1000);
				
				if(time_now >= event_time)
					var rem = (time_now - event_time);
				else
					if(time_now < event_time)
						var rem = (event_time - time_now);
					
								
				var s = Math.floor(rem);
				var m = Math.floor(s/60);
				var h = Math.floor(m/60);
				var d = Math.floor(h/24);
				
				h %= 24;
				m %= 60;
				s %= 60;
				
				//if(d > 1 || d == 0)
				//	var timer = d + "days " + h+"H : "+ m+"M : "+s + "S";
				//else
				//	var timer = d + "day " + h+"H : "+ m+"M : "+s + "S";
				
				
				var timer =	'<span class="lgreen">LAUNCH TIME:</span><br/>\
							<div id="clockdiv">\
							  <div>\
								<span class="days">'+ d +'</span>\
								<div class="smalltext">Days</div>\
							  </div>\
							  <div>\
								<span class="hours">'+ h +'</span>\
								<div class="smalltext">Hours</div>\
							  </div>\
							  <div>\
								<span class="minutes">'+ m +'</span>\
								<div class="smalltext">Minutes</div>\
							  </div>\
							  <div>\
								<span class="seconds">'+ s +'</span>\
								<div class="smalltext">Seconds</div>\
							  </div>\
							</div>';
					
				if(rem <= 0 || (event_time < time_now)){
					clearInterval(active_interval);
					
					timer = '<span class="red">TIME OUT!!!</span>';
					$("div.ult_timer_wrapper").html(timer);
					
				}
				else{
					
					$("div.ult_timer_wrapper").html(timer);
				}
					
				
				
	}
	
	updateUltimateTimer();
	var active_interval = setInterval(updateUltimateTimer,1000);
	
}


//////////FUNCTION TO GET USER VIEW PREFERENCE//////////////////////////////////////////////////////////////////////////////////

function getViewType(){
	
	dom("tmp").innerHTML = 'westy';
	
	if(typeof(storage) !== "undefined"){
		
			var viewType = sessionStorage.user_pref ;
			
			switch(viewType){
				
				case 1:{return "mobile"; break;}
				case 2:{return "computer"; break;}
				default:{return "mobile"; break;}
			
			}
		
	}else
		DOM("res_a").innerHTML = 'Sorry no web storage support for your device';
	
	
}




//////////////////FUNCTION TO MAKE PASSWORD PLANE IN LOGIN PAGE/////////////////////////////////////////////////////////////////////////////


function showpassword(){
	
	var type_val = $(".lpw").attr("type");
	
	
	if(type_val=="password"){
		
		
	 $(".lpw").attr("type", "text");
		
	}
	
	else {
		
		
        $(".lpw").attr("type", "password");
		
		
	}
	
	
}



////////////JQUERY DOCUMENT READY BEGIN/////////////////////////////////////////////////////////////////////////


$(document).ready(function(){
	
	
//////SCROLL TO PAGE TOP AND BOTTOM WITH ANIMATION////////////////////////////////////////////	
	
	$(".topageup").click(function(){
		
		$("html, body").animate({scrollTop:"0"}, 1000);
		
		return false;
		
		
	})
	
	$(".topagedown").click(function(){
		
		$("html, body").animate({scrollTop:"10000"}, 1500);
		
		return false;
		
		
	})
	
	
	$(window).scroll(function(){
		
		
		if($(window).scrollTop() > 800){
			
			$("div.midpage_scroll").fadeIn("slow");
		}
		
		else if($(window).scrollTop() < 800) {
			
			$("div.midpage_scroll").fadeOut("slow");
			
		}
		
	})
	
	
	
	
/////////////FUNCTIONS TO RESEND CONFIRMATION CODES////////////////////////////////////////////////////////////////////////////////////////	
	

$('.resendcode').click(function(){
	
	
	
	//var username=$('#getcuser').html();
	
	var username=$(this).attr("name");
	
	//var email=$('#getcuseremail').html();
	
	var email=$(this).val();
	
	var ajadatas='username=' + username + "&email=" + email;
	
	$('#showcoderesentres').html('Resending your activation code. please wait.....');
	
	
	$.ajax({
		type:'post',
		url:'activate-account',
		data:ajadatas,
		success:function(res){
			
			$('#showcoderesentres').html("<span class='blue'>"+username+"</span><span class='black'> your activation link has been resent.<br/>Thank you.</span>");
			
			
		}
		
		
	})
	
	
	
})
	
	
	
	
$('.resendemailcode').click(function(){
	

	var email = $(this).attr("user_email");
	var rise = $(this).attr("rise");
	
	var ajadatas="email=" + email + "&rise=" + rise;
	
	$('#showemailcoderesentres').html('<span class="black">Resending your confirmation code. please wait.....</span>');
	
	
	$.ajax({
		type:'post',
		url:'resend-email-confirmation-code',
		data:ajadatas,
		success:function(res){
			
			$('#showemailcoderesentres').html(res);
			
			
		}
		
		
	})
		
	
})
		
	


///////////FUNCTION TO HANDLE  TOPNAV DROP DOWN ICON////////////////////////////////////////////////////////////////////////////////

$("a#top_nav_dropicon").click(function(){//////DROP MENU//////////
	
	
	if($("nav.top_nav_drop").is(":visible")){
		
		$(this).html('<img id="dropmenu" alt="icon" src="wealth-island-images/icons/dropmenu.png" />');
		$("nav.top_nav_drop").hide("slideToggle");
		
	}
	else{
		
	$(this).html('<img id="dropmenu" alt="icon" src="wealth-island-images/icons/closemenu.png" />');
	
	$("nav.top_nav_drop").show("slideToggle");
	
	}
		
})
	
//////////////////////DROP MENU///////////////	

$("a#drop_dropicon").click(function(){
	
	$("body").toggleClass("drop_opened"); 
	$(".dropmenu").delay(500).queue(function(reset_scroll) { $(this).scrollTop(0); 
	reset_scroll(); });
	
	
})

///////SLIDE MENU///////////////	

$("a#slide_dropicon").click(function(){
	
	$("body").toggleClass("slide_drop_opened"); 
	$(".slide_drop").delay(500).queue(function(reset_scroll) { $(this).scrollTop(0); 
	reset_scroll(); });
	
	
})
	
	

///////////////////FUNCTION TO REMOVE AVATAR//////////////////////////////////////////////////////////////////////////////////////////////		
	
$(".remove_avatar").click(function(){
	
	var file = $(this).attr("file");
	
	var this_element = $(this);
	
	datas_sent = "file=" + file  ;
			
			$.ajax({
				
				url:"remove-avatar",
				type:"post",
				data:datas_sent,
				success:function(res){
					
				$(this_element).parent().remove();
					
					
				}

				
			})
	
	
})



////////////FUNCTION TO CLEAR COMMNETS FOR USER//////////////////////////////////////////////////////////////////

$("input.clear_comm").click(function(){
	
	$(this).parent().hide();
	var datas_sent = 'clear_comm=clear';
	$.ajax({		
			url:"dash-board",
			type:"post",
			data:datas_sent,
			success:function(res){
	
				
				
			}
		
	})
	
})

////////////FUNCTION TO HIDE AVN FOR USER//////////////////////////////////////////////////////////////////

$("input.hide_avn").click(function(){
	
	$(this).parent().hide();
	var datas_sent = 'hide_avn=true';
	$.ajax({		
			url:"dash-board",
			type:"post",
			data:datas_sent,
			success:function(res){
	
				
				
			}
		
	})
	
})



////////////FUNCTION TO DISPLAY FORM FOR UPLOADING POP AFTER USER CLICK HAVE PAID//////////////////////////////////////////////////////////////////

$("input.have_paid,input.decline_pay,input.confirm_paid,input.purge,input.start_btn").click(function(){
	
	$("div.modal").css("display","none");/////HIDE ANY PREV OPENED MODAL B4 OPENING NEW ONE///////////////////
	$(this).next().css("display","block");
	
})




////////////FUNCTION TO CLOSE MODAL//////////////////////////////////////////////////////////////////

$("span.close_modal,input.close_modal").click(function(){
	
	$(this).parent().parent().parent().css("display","none");
	
})



///////FUNCTION TO  EXECUTE CHECK FOR DELETE OF INBOX AND OLD INBOX MESSAGES///////////////////////////////////////////////////////////////////

/////////////SELECT CHECKBOX TO DELETE/////////////

$('.checkbox_inbox') .click(function(){
	
	
	var message_id = $(this).attr("mid");
	
	var ajadatas="message_id="  + message_id;
	
	$.ajax({
		
		type:'post',
		url:'do-pm-delete-check',
		data:ajadatas,
		success:function(res){
			
			$("#checked_num").html(res);
		
		}
		
		
		
	})
	

	
})


	
	
///////FUNCTION TO TOGGLE VISIBILITY OF JUMP TO PAGE IN PAGINATIONS///////////////////////////////////////////////////////////////////////////////////////////////	
	
	$("a.skippage").click(function(){
		
		
		if($(this).prev("form.jump2page,form#jump2page").is(":visible")){
			
			$(this).prev("form.jump2page").css("display", "none");
			$(this).prev("form#jump2page").css("display", "none");
			$(this).html("<img class='pageskip' src='wealth-island-images/icons/skippage.png' alt='icon' />");
			$(this).attr("title","jump to page");
			
		}
				
		else{
			
			$(this).prev("form.jump2page").css("display", "inline-block");
			$(this).prev("form#jump2page").css("display", "inline-block");
			$(this).html("<img class='pageskip' src='wealth-island-images/icons/closemenu.png' alt='icon' />");
			$(this).attr("title","close");
		
		}
				
		
	})
	
	




/**************ONCLICK FOR CLEAR INBOX MESSAGES************************/
	

$('.clearinbox').click(function(){


if($(this).next().is(":visible"))
$(this).next().hide();

else
$(this).next().show();


$(".confirm_inbox_del").click(function(){
	
	var confirmation = $(this).val();
	

	var grant = confirmation;
	
	
	grant = grant.trim();
	
	
	if(grant == "CANCEL")
	
			$(".inbox_hist").hide();
		
	if(grant == "OK"){

	window.location.assign('clearinbox');
	}
})	

})


////////HIDE THE DROP DOWN IF  IT IS CLICKED ON///////////////////////////////////////////////////

$(".inbox_hist").click(function(){
	
	
	$(this).hide();
	
	
	
})




/////////////ONCLICK FOR DELETE OLD MESSAGES/////////////////////////
	

$('.deleteoldmessages').click(function(){

var currentuser=$('#userondelete').html();
var dp2=$('#dp2').html();


if($(this).next().is(":visible"))
$(this).parent().next().hide();

else
	$(this).next().show();

$(".confirm_old_inbox_del").click(function(){
	
	var confirmation = $(this).val();


	var grant = confirmation;
	
	
	grant = grant.trim();
	
	
	if(grant == "CANCEL")
	
			$(".old_inbox_hist").hide();
	
	
	if(grant == "OK"){

	window.location.assign('deleteoldmessages');
	}
	
	
})
	
})	


////////HIDE THE DROP DOWN IF  IT IS CLICKED ON///////////////////////////////////////////////////

$(".old_inbox_hist").click(function(){
	
	
	$(this).hide();
	
		
})





////////////FUNCTION TO CANCEL USER  ACCOUNT/////////////////////////////////////////////////////////////////////////////////////


$('.cancelaccount').click(function(){	

	//var userunderdelete=$('#userondelete').html();

	var userunderdelete=$(this).attr("name");


	userunderdelete=userunderdelete.toUpperCase();


	if($(".account_cancel").is(":visible"))
	$(".account_cancel").hide();

	else
	$(".account_cancel").show();


		$(".confirm_cancel").click(function(){
			
			var confirmation = $(this).val();

				
				var grant = confirmation;
				
				
				grant = grant.trim();
				
				
				if(grant == "CANCEL")
				
						$(".account_cancel").hide();
				
				//confirm("\n\nWARNING!!!\n\n" + userunderdelete + "\n\n you are about to delete your account with http://eurotech.net16.net \n\n\n\n\n\nplease click OK to confirm or CANCEL to stop\n\n\n\n\n\nNOTE: you will no longer be able to access the account \n once deleted\n\n\n\n" );
				
				if(grant == "OK"){
				

				$('#cancelaccountresponse').html("Your will be redirected shortly, please wait........");

				
				window.location.assign("cancelaccount");
				
				
				
		}
				
	})
	
			
})
	
	
			$(".account_cancel").click(function(){
				
				
				$(this).hide();
				
				
		})
		
		
			
	
	
})////////////////////END OF DOC.READY////////////////////////////

