#!/usr/bin/perl
##
##  printenv -- demo CGI program which just prints its environment
##

use MIME::Base64;
use utf8::all;
use CGI qw/-utf8 :standard *table *td *tr *ul/;

print header(-type=>'text/html',-charset=>'utf-8'),
      start_html(-title=>'Skolverkets testsida för inloggning till digitala nationella prov',
                 -head=>[meta({-http_equiv => 'Content-Type',
                               -robots => 'noindex',
                               -viewport => "width=device-width, initial-scale=0.86, maximum-scale=3.0, minimum-scale=0.86",
                               "apple-mobile-web-app-capable" => "yes",
                               "apple-mobile-web-app-status-bar-style" => "black-translucent",
                               "format-detection" => "telephone=no",
                               "charset" => "utf-8",
                               -content    => 'text/html; charset=utf-8'}),
                         Link({-rel=>'stylesheet',-href=>'/assets/style.css'}),
                         Link({-rel=>'icon',-href=>"/assets/android-chrome-192x192.png"}),
                         Link({-rel=>'manifest',-href=>'/assets/manifest.json'}),
                         Link({-rel=>'shortcut icon',-href=>'/assets/favicon.ico'}),
                         Link({-rel=>'apple-touch-icon',-href=>'/assets/android-chrome-192x192.png'})
                         ]);

print <<EOH;

<div class="header"><div class="topnav"><img src="/assets/skolverket-logotype.svg" class="logotype" /></div></div>
  <div class="container"><div class="widecontent"><div class="page"><h2>Grattis!</h2>
    <p class="largetext">Du har nu lyckats logga in till Skolverkets testsida.</p>
        <p>H&auml;r nedan ser du den information som skickades till oss via din skolas identitetstj&auml;nst (&auml;ven kallad Identity Provider, IDP). D&aring; detta &auml;r en test kommer dessa uppgifter inte att sparas av Skolverket.</p> 
        <table cellpadding="0" cellspacing="0">
        <tr>
          <th id="label">Attribut</th><th>V&auml;rde</th>
        </tr>
EOH

foreach $var (sort(keys(%ENV))) {
    #next unless ($var =~ /^[a-z]/ || $var =~ /^Shib/);
    next unless $var =~ /^[a-z]/;
    $val = $ENV{$var};
    $val =~ s|\n|\\n|g;
    $val =~ s|"|\\"|g;
    print "<tr><th id=\"label\">$var</th><td class=\"ellipsis\">$val</td></tr>\n";
}
print "</table>\n";

print<<EOH;
<p>Informationen skickades till oss fr&aring;n din skolas identitetstj&auml;nst</p>
        <h3>V&auml;rt att veta:</h3>
        <p>Skolverket hanterar inte dina inloggningsuppgifter. Du n&aring;r v&aring;r testsida via en inloggning p&aring; din skola, 
           &auml;ven kallad federerad inloggning. Om du vill l&auml;ra mer om hur federerad inloggning fungerar, bes&ouml;k 
           Internetstiftelsens webbplats om <a href="https://www.skolfederation.se/" target="_blank">Skolfederation</a>.</p>
        <p>&nbsp;<p></div>
        <p>&nbsp;<p></div></div>
  </div>
EOH

print end_html;
