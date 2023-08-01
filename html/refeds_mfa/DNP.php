<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../assets/html.php';
print_header('Skolverkets testsida för digitala nationella prov','../');

$idp = $_SERVER["Shib-Identity-Provider"];
if ($_SERVER["Shib-Authentication-Method"] == 'https://refeds.org/profile/mfa') {
	if (isset($_SERVER["eppn"]) ) {
		saveToSQL($idp,"EPPN","OK");
		printf('	<h2>Grattis!</h2>%s	<p class="largetext">Du har nu lyckats logga in till testsidan.</p>%s',"\n", "\n");
	} else {
		saveToSQL($idp,"EPPN","Saknas");
		printf('	<h2>Tyvär lyckades inte inloggningen till 100%</h2>%s	<p class="largetext">Du saknar eppn/eduPersonPrincipalName.</p>%s',"\n", "\n");
	}
} else {
	print '	<h2>Tyvär lyckades inte inloggningen till 100%</h2>' . "\n";
	print '	<p class="largetext">Authentication-Method var inte LOA2 eller högre</p>' . "\n";
	if (! isset($_SERVER["eppn"]) ) {
		printf('	<p class="largetext">Du saknar eppn/eduPersonPrincipalName.</p>%s',"\n");
		saveToSQL($idp,"EPPN","Saknas");
	}
}

?>
	<p>Här nedan ser du den information som skickades till testsidan via din identitetstjänst (även kallad Identity Provider, IDP).</p>
	<table cellpadding="0" cellspacing="0">
	  <tr><th id="label">Nödvändigt attribut</th><th>V&auml;rde</th></tr>
	  <tr><th id="label">eppn/eduPersonPrincipalName</th><td><?php if (isset($_SERVER['eppn'])) { print $_SERVER['eppn']; unset ($_SERVER['eppn']); } ?></td></tr>
	  <tr><th id="label">Övriga attribut</th><th></th></tr>
<?php
	foreach ( $_SERVER as $key => $value ) {
		if (ord($key) > 96 ) {
			
			printf ('	  <tr><th id="label">%s</th><td>%s</td></tr>%s', $key, str_replace(';' , '<br>',$value), "\n");
	}
}
?>
	</table>
	<br>
	<p>Du når testsidan via en inloggning på din skola, även kallad federerad inloggning. Skolverket hanterar inte dina inloggningsuppgifter, och inga uppgifter sparas av Skolverket.</p>
<?php print_footer();

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
