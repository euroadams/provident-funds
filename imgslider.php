
<?php


?>

<!DOCTYPE HTML>

<html>

<head>
<?php require_once("include-html-headers.php")   ?>
<script>

$(document).ready(function(){
var currentIndex = 0,
  items = $('.container div'),
  itemAmt = items.length;

function cycleItems() {
	
  var item = $('.container div').eq(currentIndex);
 // items.hide();
 // item.css('display','inline-block');
  items.hide().fadeOut(1000);
  item.fadeIn(1200);
}

var autoSlide = setInterval(function() {
  currentIndex += 1;
  if (currentIndex > itemAmt - 1) {
    currentIndex = 0;
  }
  cycleItems();
}, 3000);

$('.next').click(function() {
  clearInterval(autoSlide);
  currentIndex += 1;
  if (currentIndex > itemAmt - 1) {
    currentIndex = 0;
  }
  cycleItems();
  autoSlide();
});

$('.prev').click(function() {
  clearInterval(autoSlide);
  currentIndex -= 1;
  if (currentIndex < 0) {
    currentIndex = itemAmt - 1;
  }
  cycleItems();
});

})

</script>

<style>

.container {
  max-width: 400px;
  background-color: black;
  margin: 0 auto;
  text-align: center;
  position: relative;
}
.container div {
  background-color: white;
  width: 100%;
  display: inline-block;
  display: none;
}
.container img {
  width: 100%;
  height: auto;
}

button {
  position: absolute;
}

.next {
  right: 5px;
}

.prev {
  left: 5px;
}

</style>

</head>
<body>

<section class="demo">
  
  <a class="prev">&#10094;</a>
  <a class="next">&#10095;</a>
  <div class="container">
    <div>
      <img src="wealth-island-images/poli-logo2.jpeg"/>
    </div>
    <div>
     <img src="wealth-island-images/poli-logo3.jpeg"/>
    </div>
    <div>
      <img src="wealth-island-images/poli-logo4.jpeg"/>
    </div>    
  </div>
</section>

<div class="explanation">
  Building a slideshow like pattern that can accurately cycle through a number of unknown divs, forwards and backwards. Trying to use as little code as possible. Leave a comment if you see a way to do it better!
</div>
</body>
</html>