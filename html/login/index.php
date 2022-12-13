<?php
include '../assets/html.php';
print_header('Skolverkets testsida för inloggning','../');?>
        <h2>Test av inloggningstjänst</h2>
        <p>Denna sida är avsedd för att skolor ska kunna testa inloggningstjänsten. Testen förutsätter att skolan är medlem i Skolfederation.</p>
        <h3>Verifiering av skolans inloggningstjänst</h3>
        <div id="login"></div>
        <p>Dessa tester kan användas för att verifiera om skolans inloggningstjänst (IdP) klarar av att hantera kommunikationen om AuthnContextClassRef (ACCR) med de profiler som stödjs i Skolverkets provtjänst. Välj den ACCR som du vill testa.Nedan följer de profiler som stödjs. Välj den AuthnContextClassRef du vill testa.</p>
        <hr>
        <p>Test med ACCR = https://refeds.org/profile/mfa<div id="REFEDS"></div></p>
        <hr>
        <p>Test med ACCR = http://schemas.microsoft.com/claims/multipleauthn<div id="MS"></div></p>
        <hr>
        <p>Test med ACCR = http://id.skolfederation.se/loa/2fa<div id="SKOLFED"></div></p>
<?php
$script="
  <script>
      window.onload = function() {
          thiss.DiscoveryComponent.render({
              loginInitiatorURL: 'https://fidustest.skolverket.se/Shibboleth.sso/Login?target=https://fidustest.skolverket.se/secure',
              context: 'fidus.skolverket.se',
              backgroundColor: 'white',
          }, '#login');
          thiss.DiscoveryComponent.render({
              loginInitiatorURL: 'https://fidustest.skolverket.se/Shibboleth.sso/REFEDS?target=https://fidustest.skolverket.se/refeds_mfa',
              context: 'fidus.skolverket.se',
              backgroundColor: 'white',
          }, '#REFEDS');
          thiss.DiscoveryComponent.render({
              loginInitiatorURL: 'https://fidustest.skolverket.se/Shibboleth.sso/MS?target=https://fidustest.skolverket.se/MS_mfa',
              context: 'fidus.skolverket.se',
              backgroundColor: 'white',
          }, '#MS');
          thiss.DiscoveryComponent.render({
              loginInitiatorURL: 'https://fidustest.skolverket.se/Shibboleth.sso/skolfed?target=https://fidustest.skolverket.se/skolfed_mfa',
              context: 'fidus.skolverket.se',
              backgroundColor: 'white',
          }, '#SKOLFED');
      };
  </script>
";
print_footer($script);
