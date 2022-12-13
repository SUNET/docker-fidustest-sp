<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

showHeader();

$idp = $_SERVER["Shib-Identity-Provider"];
#if (isset($_SERVER["eppn"]) ) {
	#saveToSQL($idp,"EPPN","OK");
#} else {
	#saveToSQL($idp,"EPPN","Saknas");
#}
if ($_SERVER["Shib-Authentication-Method"] == 'http://schemas.microsoft.com/claims/multipleauthn') {
	print '	<h2>Grattis!</h2>' . "\n";
	print '	<p class="largetext">Du har nu lyckats logga in till Skolverkets testsida.</p>' . "\n";
} else {
	print '	<h2>Tyvär lyckades inte inloggningen till 100%</h2>' . "\n";
	print '	<p class="largetext">Förväntade oss att <b>Authentication-Method</b> skulle vara <b>http://schemas.microsoft.com/claims/multipleauthn</b></p>' . "\n";
}
?>
	<p>H&auml;r nedan ser du den information som skickades till oss via din skolas identitetstj&auml;nst (&auml;ven kallad Identity Provider, IDP). D&aring; detta &auml;r en test kommer dessa uppgifter inte att sparas av Skolverket.</p>
	<table cellpadding="0" cellspacing="0">
	  <tr><th id="label">Attribut</th><th>V&auml;rde</th></tr>
<?php
foreach ( $_SERVER as $key => $value ) {
	if (ord($key) > 96 ) { ?>
	  <tr><th id="label"><?=$key?></th><td><?=$value?></td></tr>
<?php	}
}
?>
	</table>
	<br>
	<table cellpadding="0" cellspacing="0">
	  <tr><th id="label">Sessions attribut</th><th>V&auml;rde</th></tr>
<?php
foreach ( array('Identity-Provider','Authentication-Instant','Authentication-Method','AuthnContext-Class') as $key ) { ?>
	  <tr><th><?=$key?></th><td><?=$_SERVER["Shib-".$key]?></td></tr>
<?php } ?>
	</table>
	<p>Informationen skickades till oss fr&aring;n din skolas identitetstj&auml;nst</p>
	<h3>V&auml;rt att veta:</h3>
	<p>Skolverket hanterar inte dina inloggningsuppgifter. Du n&aring;r v&aring;r testsida via en inloggning p&aring; din skola, &auml;ven kallad federerad inloggning. Om du vill l&auml;ra mer om hur federerad inloggning fungerar, bes&ouml;k Internetstiftelsens webbplats om <a href="https://www.skolfederation.se/" target="_blank">Skolfederation</a>.</p>
<?php
showFooter();

function showHeader() { ?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>Skolverkets testsida för inloggning till digitala nationella prov</title>
<meta apple-mobile-web-app-capable="yes" apple-mobile-web-app-status-bar-style="black-translucent" charset="utf-8" content="text/html; charset=utf-8" format-detection="telephone=no" http-equiv="Content-Type" robots="noindex" viewport="width=device-width, initial-scale=0.86, maximum-scale=3.0, minimum-scale=0.86" />
<link href="/assets/style.css" rel="stylesheet" />
<link href="/assets/android-chrome-192x192.png" rel="icon" />
<link href="/assets/manifest.json" rel="manifest" />
<link href="/assets/favicon.ico" rel="shortcut icon" />
<link href="/assets/android-chrome-192x192.png" rel="apple-touch-icon" />
</head>
<body>

  <div class="header">
    <div class="topnav"><img src="/assets/skolverket-logotype.svg" class="logotype" /></div>
  </div><!-- End header -->
  <div class="container">
    <div class="widecontent">
      <div class="page">
<?php }

function showFooter() { ?>
      </div><!-- End page -->
    </div><!-- End widecontent -->
  </div><!-- End container -->
</body>
</html>
<?php }

###
# Sparar ner i SQL
###
function saveToSQL($idp,$test,$testResult) {
	$dbFile = "/var/db/IdPs.db";
	if (! file_exists($dbFile) )  {
		$db = new SQLite3($dbFile);
		$db->exec("CREATE TABLE idpStatus (
			Idp STRING,
			Time STRING,
			Test STRING,
			TestResult STRING);");
	} else
		$db = new SQLite3($dbFile);
	$ifExist = $db->prepare("SELECT * FROM idpStatus WHERE Idp = :idp AND Test = :test;");
	$ifExist->bindValue(":idp",$idp);
	$ifExist->bindValue(":test",$test);
	$result=$ifExist->execute();
	if (! $result->fetchArray()) {
		# Skapar upp raden så att Update i nästa stycke fungerar
		$addRow = $db->prepare("INSERT INTO idpStatus (Idp, Test) VALUES (:idp, :test);");
		$addRow->bindValue(":idp",$idp);
		$addRow->bindValue(":test",$test);
		$addRow->execute();
	}
	$updateRow = $db->prepare("UPDATE idpStatus SET Time = :time, TestResult = :testresultat WHERE Idp = :idp AND Test = :test;");
	$updateRow->bindValue(":idp",$idp);
	$updateRow->bindValue(":test",$test);
	$updateRow->bindValue(":time", date("Y-m-d H:i:s"));
	$updateRow->bindValue(":testresultat", $testResult);
	$updateRow->execute();
}
