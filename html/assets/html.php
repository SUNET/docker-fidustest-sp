<?php
function print_header($title,$prefix='') {
	print '<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8"> 
  <title>' . $title . '</title>
  <meta name="robots" content="noindex"> 
  <meta name="viewport" content="width=device-width, initial-scale=0.86, maximum-scale=3.0, minimum-scale=0.86"> 
  <meta name="apple-mobile-web-app-capable" content="yes"> 
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"> 
  <meta name="format-detection" content="telephone=no"> 
    <link rel="apple-touch-icon" href="'.$prefix.'assets/android-chrome-192x192.png"/> 
  <meta name="mobile-web-app-capable" content="yes"> 
    <link rel="icon" href="'.$prefix.'assets/android-chrome-192x192.png"/> 
    <link rel="manifest" href="'.$prefix.'assets/manifest.json">
    <link rel="shortcut icon" href="'.$prefix.'assets/favicon.ico"/> 
  <link rel="stylesheet" type="text/css" href="'.$prefix.'assets/style.css"> 
  <script src="https://ds.fidus.skolverket.se/thiss.js"></script>
  <style>
    .button {
      border: none;
      color: white;
      padding: 15px 32px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 4px 2px;
      cursor: pointer;
      background-color: #008CBA;
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="topnav">
      <img src="'.$prefix.'assets/skolverket-logotype.svg" class="logotype" />
    </div>
  </div>
  <div class="container">
    <div class="content">
      <div class="popup">';
}

function print_footer($extra='', $extraRelated='') {
	print '
        <hr>
	<p><b>Relaterad information</b></p>' . $extraRelated .'
	<p><a href="/Behandling_av_personuppgifter.html">Behandling av personuppgifter</a></p>
        <hr>
	<p><b>Kontakta oss</b></p>
        <p>Du kan kontakta Skolverket angående Fidus.</p>
        <p><a href="https://www.skolverket.se/om-oss/kontakta-oss">Kontakta Skolverket</a><p>
      </div>
    </div>
  </div>
'.$extra.'</body>
</html>';
}

