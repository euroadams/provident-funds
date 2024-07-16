<?php
require 'Mobile_Detect.php';
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="Mobile Detect,Mobile Detect Using PHP">
    <meta name="author" content="BhavinShiroya">
    <title>Mobile Detect Using PHP</title>
    <link rel='stylesheet' href='style.css' type='text/css'/>
  </head>
  <body>
    <div id="container">
      <div id="body">
        <div class="mainTitle" >Mobile Detect Using PHP</div>
        <div class="height20"> </div>
        <div class="title" style="text-align: center;">Your device is <span style="text-decoration: underline; font-style: italic;"><?php echo $deviceType; ?></div>
        <div class="height20"></div>
        <a href="http://www.techsofttutorials.com/how-to-detect-mobile-and-tablet-device-using-php-code/">Click here for tutorials</a>
        <article>
          <div class="height10">&nbsp;</div>
          <table cellspacing="0" cellpadding="0" class="bordered" style="width: 100%;">
        <tbody>
        <tr>
          <th colspan="2" style="text-align: left; color: #FFFFFF;">Basic detection methods</th>
        </tr>
        <tr>
            <td>isMobile()</td>
            <td <?php $check = $detect->isMobile(); if($check): ?>class="true"<?php endif; ?>><?php var_dump($check); ?></td>
        </tr>
        <tr>
          <td>isTablet()</td>
          <td <?php $check = $detect->isTablet(); if($check): ?>class="true"<?php endif; ?>><?php var_dump($check); ?></td>
        </tr>
        </tbody>
        <tbody>
        <tr>
            <th colspan="2" style="text-align: left; color: #FFFFFF;">Custom detection methods</th>
        </tr>
        <?php foreach($detect->getRules() as $name => $regex):
                        $check = $detect->{'is'.$name}();
        ?>
            <tr>
                    <td>is<?php echo $name; ?>()</td>
                    <td <?php if($check): ?>class="true"<?php endif; ?>><?php var_dump($check); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tbody>
            <tr>
                <th colspan="2" style="text-align: left; color: #FFFFFF;">Experimental version() method</th>
            </tr>
            <?php
            foreach($detect->getProperties() as $name => $match):
                $check = $detect->version($name);
                if($check!==false):
            ?>
            <tr>
                <td>version(<?php echo $name; ?>)</td>
                <td><?php var_dump($check); ?></td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
        <tbody>
            <tr>
                <th colspan="2" style="text-align: left; color: #FFFFFF;">Other tests</th>
            </tr>
            <tr>
                <td>isiphone()</td>
                <td><?php var_dump($detect->isiphone()); ?></td>
            </tr>
            <tr>
                <td>isIphone()</td>
                <td><?php var_dump($detect->isIphone()); ?></td>
            </tr>
            <tr>
                <td>istablet()</td>
                <td><?php var_dump($detect->istablet()); ?></td>
            </tr>
            <tr>
                <td>isIOS()</td>
                <td><?php var_dump($detect->isIOS()); ?></td>
            </tr>
            <tr>
                <td>isWhateverYouWant()</td>
                <td class="randomcrap"><?php var_dump($detect->isWhateverYouWant()); ?></td>
            </tr>
        </tbody>
    </table>
          

        </article>
        <div class="height30"></div>
        <footer>
          <div class="copyright"> &copy; 2014 - <?php echo date('Y') ?> <a href="http://www.techsofttutorials.com/" target="_blank">TechsoftTutorials</a>. All rights reserved </div>
          <div class="footerlogo"><a href="http://www.techsofttutorials.com/" target="_blank"><img src="http://techsofttutorials.com/wp-content/uploads/2014/12/new2.png" width="200" height="47" alt="TechsoftTutorials Logo" /></a> </div>
        </footer>
      </div>
    </div>
  </body>
</html>
