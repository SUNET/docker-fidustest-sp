<?php
if (isset($_GET["eleg"])) {
  header("Location: /Shibboleth.sso/Login?target=https://fidustest.skolverket.se/refeds_mfa/T8.php&entityID=https://skolverket.eduid.se/dnp/idp/&authnContextClassRef=https://refeds.org/profile/mfa");
  die();
} elseif (isset($_GET["noeleg"])) {
  header("Location: /Shibboleth.sso/Login?target=https://fidustest.skolverket.se/secure/T8.php&entityID=https://skolverket.eduid.se/dnp/idp/");
  die();
}
include '../assets/html.php';
print_header('Skolverkets testsida för inloggning','../');?>
        <h2>Test av inloggningstjänst mot digitala nationella prov</h2>
        <p>Från denna sida kan du testa följande:
        <p><a href="/Shibboleth.sso/Login?target=https://fidustest.skolverket.se/secure/T8.php&entityID=https://teknikattan-idpproxy.sunet.se/idp"><button class="button">Testa inlogg</button></a></p>
	<p>För att kunna använda testet behöver ni en inloggningstjänst (IdP) ansluten till en identitetsfederation i FIDUS.</p>
<?php print_footer('', '
');
