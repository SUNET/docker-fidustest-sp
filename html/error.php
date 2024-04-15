<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8"> 
  <title>Skolverkets testsida för inloggning till digitala nationella prov</title>
  <meta name="robots" content="noindex"> 
  <meta name="viewport" content="width=device-width, initial-scale=0.86, maximum-scale=3.0, minimum-scale=0.86"> 
  <meta name="apple-mobile-web-app-capable" content="yes"> 
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"> 
  <meta name="format-detection" content="telephone=no"> 
    <link rel="apple-touch-icon" href="assets/android-chrome-192x192.png"/> 
  <meta name="mobile-web-app-capable" content="yes"> 
    <link rel="icon" href="assets/android-chrome-192x192.png"/> 
    <link rel="manifest" href="assets/manifest.json">
    <link rel="shortcut icon" href="assets/favicon.ico"/> 
  <link rel="stylesheet" type="text/css" href="assets/style.css"> 
</head>

<body>
  <div class="header"><div class="topnav"><img src="assets/skolverket-logotype.svg" class="logotype" /></div></div>
  <div class="container"><div class="content"><div class="popup"><h2>Åtkomst nekades!</h2>
<?php
switch($_GET['RelayState']) {
	case 'https://fidustest.skolverket.se/secure':
		$error = 'SFA';
		$retry = 'index.php';
		break;
	case 'https://fidustest.skolverket.se/refeds_mfa':
	case 'https://fidustest.skolverket.se/refeds_mfa/':
		$expected = 'https://refeds.org/profile/mfa';
		$error = 'MFA';
		$retry = 'mfa.html';
		break;
	case 'https://fidustest.skolverket.se/MS_mfa':
	case 'https://fidustest.skolverket.se/MS_mfa/':
		$expected='http://schemas.microsoft.com/claims/multipleauthn';
		$error = 'MFA';
		$retry = 'mfa.html';
		break;
	default:
		$error = '?';
		$retry = 'index.php';
}
?>
    <p class="largetext">Vi kunde inte ge dig åtkomst till Skolverkets testsida.</p>
	<p>När du ser denna text innebär det att inloggningen misslyckades. Detta kan ha orsakats av ett temporärt fel,
	   men det kan också bero på att din skola inte lyckades verifiera dina behörigheter för åtkomst till 
	   Skolverkets inloggningstjänst.</p> 
<?php
if ($error == 'MFA') {
	print "	<p>Antingen supportar er IdP inte MFA via <b>$expected</b> eller så är inte användaren aktiverad för denna profil</p>\n";
}
?>
	<p>Följ länken här för att <a href="<?=$retry?>">testa inloggningen en gång till</a> och om felet kvarstår kontakta 
	   teknisk support på din skola för vidare hjälp.</p>
<p>
	<h3>Felkoder:</h3>
	<p>Skicka denna info för att underlätta felsökning</p>
<table>
<?php
foreach ($_GET as $key => $value) {
	printf('<tr><td>%s = %s</td></tr>', $key, $value);
}
?>

</table>

	<h3>Värt att veta:</h3>
	<p>Skolverket hanterar inte dina inloggningsuppgifter. Du når vår testsida via en inloggning på din skola, 
	   även kallad federerad inloggning. För att testet ska fungera måste du därför ha giltiga inloggningsuppgifter 
	   från din organisations IT-avdelning. Om du vill lära mer om hur federerad inloggning fungerar, besök 
	   Internetstiftelsens webbplats om <a href="https://www.skolfederation.se/" target="_blank">Skolfederation</a>.</p></div>
	<p>&nbsp;<p></div></div>
  </div> 
</body>
</html>
