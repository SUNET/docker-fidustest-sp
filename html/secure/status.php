<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
showHeader();
if (verifyEPPN()) {
	print '        <h2>Status</h2>
        <table cellpadding="0" cellspacing="0">
          <tr><th>IdP</th><th>Tidpunkt (UTC)</th><th>Resultat EPPN</td></tr>' . "\n";

	$dbFile = "/var/db/IdPs.db";
	$db = new SQLite3($dbFile);
	$IdPs = $db->prepare("SELECT * FROM idpStatus;");
	$result=$IdPs->execute();
	while ( $idp = $result->fetchArray()) {
		printf('          <tr><td class="no-overflow">%s</td><td>%s</td><td>%s</td></tr>%s', $idp["Idp"], $idp["Time"], $idp["TestResult"], "\n");
	}
	print "        </table>\n";
}
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
function verifyEPPN() {
	if (isset($_SERVER["eppn"]) ) {
		switch ($_SERVER["eppn"]) {
			case 'bjorn@sunet.se':	// Bjorn@sunet.se
			case 'pax@sunet.se':	// pax@sunet.se
			case 'jocar@sunet.se':	// jocar@sunet.se
			case 'hilil-podor@eduid.se': // rasmus.larsson@internetstiftelsen.se
			case 'rizij-kurod@eduid.se': // Tomas.Ericsson@skolverket.se
			case 'konoh-mogag@eduid.se': // Mattias.Hyll@skolverket.se
			case 'fovop-pozad@eduid.se': // Richard är mottagare av verifieringstestet
				return true;
				break;
			default:
				print "Wrong EPPN";
				return false;
				break;
		}
	}
	print "Missing EPPN";
	return false;
}
