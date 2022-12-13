<?php
include 'assets/html.php';
print_header('Skolverkets testsida för inloggning');?>
        <h2>Test av inloggningstjänst</h2>
        <p>Från denna sida kan du testa följande:
	<p><a href="login"><button class="button">Test av inloggningstjänst</button></a></p>	
	<p><a href="DNP"><button class="button">Test inför DNP</button></a></p>
	<p>Du når testsidan via en inloggning på din skola, även kallad federerad inloggning. Skolverket hanterar inte dina inloggningsuppgifter, och inga uppgifter sparas av Skolverket.</p>
<?php print_footer();
