<?php if (!defined('PARENT')) { die('Permission Denied'); }
if ($SETTINGS->enableBBCode == 'no') {
  exit;
}
?>

<div id="content">

<div class="fieldHeadWrapper">
  <p><?php echo $msg_bbcode16; ?>:</p>
</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[b]</b> <?php echo $msg_bbcode3; ?> <b>[/b]</b></p>
    <hr>
    <p><b><?php echo $msg_bbcode3; ?></b></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[u]</b> <?php echo $msg_bbcode4; ?> <b>[/u]</b></p>
    <hr>
    <p><span style="text-decoration:underline"><?php echo $msg_bbcode4; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[i]</b> <?php echo $msg_bbcode5; ?> <b>[/i]</b></p>
    <hr>
    <p><span style="font-style:italic"><?php echo $msg_bbcode5; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[s]</b> <?php echo $msg_bbcode6; ?> <b>[/s]</b></p>
    <hr>
    <p><span style="text-decoration:line-through"><?php echo $msg_bbcode6; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[del]</b> <?php echo $msg_bbcode7; ?> <b>[/del]</b></p>
    <hr>
    <p><span style="text-decoration:line-through;color:red"><?php echo $msg_bbcode7; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[ins]</b> <?php echo $msg_bbcode8; ?> <b>[/ins]</b></p>
    <hr>
    <p><span style="background:yellow"><?php echo $msg_bbcode8; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[em]</b> <?php echo $msg_bbcode9; ?> <b>[/em]</b></p>
    <hr>
    <p><span style="font-style:italic;font-weight:bold"><?php echo $msg_bbcode9; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[color=#FF0000]</b> <?php echo $msg_bbcode10; ?><b> [/color]</b></p>
    <hr>
    <p><span style="color:red"><?php echo $msg_bbcode10; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[color=blue]</b> <?php echo $msg_bbcode11; ?> <b>[/color]</b></p>
    <hr>
    <p><span style="color:blue"><?php echo $msg_bbcode11; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[h1]</b> <?php echo $msg_bbcode12; ?> <b>[/h1]</b></p>
    <hr>
    <p><span style="font-weight:bold;font-size:22px"><?php echo $msg_bbcode12; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[h2]</b> <?php echo $msg_bbcode13; ?> <b>[/h2]</b></p>
    <hr>
    <p><span style="font-weight:bold;font-size:20px"><?php echo $msg_bbcode13; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[h3]</b> <?php echo $msg_bbcode14; ?> <b>[/h3]</b></p>
    <hr>
    <p><span style="font-weight:bold;font-size:18px"><?php echo $msg_bbcode14; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[h4]</b> <?php echo $msg_bbcode15; ?> <b>[/h4]</b></p>
    <hr>
    <p><span style="font-weight:bold;font-size:16px"><?php echo $msg_bbcode15; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[center]</b> <?php echo $msg_bbcode31; ?> <b>[/center]</b></p>
    <hr>
    <p><span style="display:block;text-align:center"><?php echo $msg_bbcode31; ?></span></p>
  </div>

</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_bbcode17; ?>:</p>
</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[url=http://www.google.co.uk]</b> Google <b>[/url]</b></p>
    <hr>
    <p><a href="http://www.google.co.uk">Google</a></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[url]</b> http://www.google.co.uk <b>[/url]</b></p>
    <hr>
    <p><a href="http://www.google.co.uk">http://www.google.co.uk</a></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[urlnew=http://www.google.co.uk]</b> Google <b>[/urlnew]</b></p>
    <hr>
    <p><a href="http://www.google.co.uk">Google</a> (<?php echo $msg_bbcode27; ?>)</p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[urlnew]</b> http://www.google.co.uk <b>[/urlnew]</b></p>
    <hr>
    <p><a href="http://www.google.co.uk">http://www.google.co.uk</a> (<?php echo $msg_bbcode27; ?>)</p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[email]</b> myname@mydomain.com <b>[/email]</b></p>
    <hr>
    <p><a href="mailto:myname@mydomain.com">myname@mydomain.com</a></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[email=myname@mydomain.com]</b> My Email Address <b>[/email]</b></p>
    <hr>
    <p><a href="mailto:myname@mydomain.com"><?php echo $msg_bbcode26; ?></a></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[img]</b> http://www.mydomain.com/images/logo.png <b>[/img]</b></p>
    <hr>
    <p><img src="templates/images/test-image.png" alt="" title=""></p>
  </div>

</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_bbcode28; ?>:</p>
</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[youtube]</b>ABC123<b>[/youtube]</b></p>
    <hr>
    <p><?php echo $msg_bbcode29; ?></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[vimeo]</b>ABC123<b>[/vimeo]</b></p>
    <hr>
    <p><?php echo $msg_bbcode29; ?></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[dailymotion]</b>ABC123<b>[/dailymotion]</b></p>
    <hr>
    <p><?php echo $msg_bbcode33; ?></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[soundcloud]</b>123456<b>[/soundcloud]</b></p>
    <hr>
    <p><?php echo $msg_bbcode32; ?></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[mp3]</b>filepath/to/file.mp3<b>[/mp3]</b></p>
    <hr>
    <p><?php echo $msg_bbcode30; ?></p>
  </div>

</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_bbcode18; ?>:</p>
</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[list]</b><br><b>&nbsp;[*]</b> <?php echo $msg_bbcode20; ?> 1 <b>[/*]<br>&nbsp;[*]</b> <?php echo $msg_bbcode20; ?> 2 <b>[/*]<br>&nbsp;[*]</b> <?php echo $msg_bbcode20; ?> 3 <b>[/*]<br>[/list]</b></p>
    <hr>
    <div><ul class="bbUl"><li><?php echo $msg_bbcode20; ?> 1</li><li><?php echo $msg_bbcode20; ?> 2</li><li><?php echo $msg_bbcode20; ?> 3</li></ul></div>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[list=n]</b><br><b>&nbsp;[*]</b> <?php echo $msg_bbcode21; ?> 1 <b>[/*]<br>&nbsp;[*]</b> <?php echo $msg_bbcode21; ?> 2 <b>[/*]<br>&nbsp;[*]</b> <?php echo $msg_bbcode21; ?> 3 <b>[/*]<br>[/list]</b></p>
    <hr>
    <div><ul class="bbUlNumbered"><li><?php echo $msg_bbcode21; ?> 1</li><li><?php echo $msg_bbcode21; ?> 2</li><li><?php echo $msg_bbcode21; ?> 3</li></ul></div>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[list=a]</b><br><b>&nbsp;[*]</b> <?php echo $msg_bbcode22; ?> 1 <b>[/*]<br>&nbsp;[*]</b> <?php echo $msg_bbcode22; ?> 2 <b>[/*]<br>&nbsp;[*]</b> <?php echo $msg_bbcode22; ?> 3 <b>[/*]<br>[/list]</b></p>
    <hr>
    <div><ul class="bbUlAlpha"><li><?php echo $msg_bbcode22; ?> 1</li><li><?php echo $msg_bbcode22; ?> 2</li><li><?php echo $msg_bbcode22; ?> 3</li></ul></div>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[list=ua]</b><br><b>&nbsp;[*]</b> <?php echo $msg_bbcode22; ?> 1 <b>[/*]<br>&nbsp;[*]</b> <?php echo $msg_bbcode22; ?> 2 <b>[/*]<br>&nbsp;[*]</b> <?php echo $msg_bbcode22; ?> 3 <b>[/*]<br>[/list]</b></p>
    <hr>
    <div><ul class="bbUlUpperAlpha"><li><?php echo $msg_bbcode22; ?> 1</li><li><?php echo $msg_bbcode22; ?> 2</li><li><?php echo $msg_bbcode22; ?> 3</li></ul></div>
  </div>

</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_bbcode19; ?>:</p>
</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[b][u]</b><?php echo $msg_bbcode23; ?> <b>[/u][/b]</b></p>
    <hr>
    <p><span style="text-decoration:underline;font-weight:bold"><?php echo $msg_bbcode23; ?></span></p>
  </div>

</div>

<div class="panel panel-default">

  <div class="panel-body">
    <p><b>[color=blue][b][u]</b> <?php echo $msg_bbcode24; ?> <b>[/u][/b][/color]</b></p>
    <hr>
    <p><span style="text-decoration:underline;font-weight:bold;color:blue"><?php echo $msg_bbcode24; ?></span></p>
  </div>

</div>

</div>