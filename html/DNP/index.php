<?php
include '../assets/html.php';
print_header('Skolverkets testsida för inloggning','../');?>
        <h2>Test av inloggningstjänst mot digitala nationella prov</h2>
        <p>Från denna sida kan du testa följande:
        <p><a href="/Shibboleth.sso/Login?target=https://fidustest.skolverket.se/secure&entityID=https://idpproxy.dev.eduid.se/idp"><button class="button">Inloggning utan e-legitimation</button></a></p>
        <!--p><a href="/Shibboleth.sso/Login?target=https://fidustest.skolverket.se/refeds_mfa&entityID=https://idpproxy.dev.eduid.se/idp&authnContextClassRef=https://refeds.org/profile/mfa"><button class="button">Med MFA</button></a></p-->
        <p>För att kunna använda testet behöver ni en inloggningstjänst (IdP) ansluten till en identitetsfederation i FIDUS.</p>
<?php print_footer();
