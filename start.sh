#!/bin/sh -x

printenv

if [ "x${SP_HOSTNAME}" = "x" ]; then
   SP_HOSTNAME="${SERVER_FQDN}"
fi

if [ "x${SP_CONTACT}" = "x" ]; then
   SP_CONTACT="info@${SP_HOSTNAME}"
fi

if [ "x${SP_ABOUT}" = "x" ]; then
   SP_ABOUT="/about"
fi

if [ "x${DEFAULT_LOGIN}" = "x" ]; then
   DEFAULT_LOGIN="md.nordu.net" 
fi

KEYDIR=/etc/ssl
mkdir -p $KEYDIR
export KEYDIR
if [ ! -f "$KEYDIR/private/shibsp-${SP_HOSTNAME}.key" -o ! -f "$KEYDIR/certs/shibsp-${SP_HOSTNAME}.crt" ]; then
   shib-keygen -o /tmp -h $SP_HOSTNAME 2>/dev/null
   mv /tmp/sp-key.pem "$KEYDIR/private/shibsp-${SP_HOSTNAME}.key"
   mv /tmp/sp-cert.pem "$KEYDIR/certs/shibsp-${SP_HOSTNAME}.crt"
fi

cat>/etc/shibboleth/shibboleth2.xml<<EOF
<SPConfig xmlns="urn:mace:shibboleth:2.0:native:sp:config"
    xmlns:conf="urn:mace:shibboleth:2.0:native:sp:config"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"    
    xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata"
    logger="shibboleth/syslog.logger"
    clockSkew="180">

    <ApplicationDefaults entityID="https://${SP_HOSTNAME}/shibboleth"
                         REMOTE_USER="eppn persistent-id targeted-id">

        <Sessions lifetime="28800" timeout="3600" relayState="ss:mem"
                  checkAddress="false" handlerSSL="true" cookieProps="https">
            <Logout>SAML2 Local</Logout>
            <Handler type="MetadataGenerator" Location="/Metadata" signing="false"/>
            <Handler type="Status" Location="/Status" acl="127.0.0.1 ::1"/>
            <Handler type="Session" Location="/Session" showAttributeValues="false"/>
            <Handler type="DiscoveryFeed" Location="/DiscoFeed"/>

            <md:AssertionConsumerService Location="/SAML2/POST"
                                         index="1"
                                         Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
                                         conf:ignoreNoPassive="true" />

            <SessionInitiator type="Chaining" Location="/Login" id="ds" relayState="cookie">
                <SessionInitiator type="SAML2" defaultACSIndex="1" acsByIndex="false" template="bindingTemplate.html"/>
                <SessionInitiator type="Shib1" defaultACSIndex="5"/>
                <SessionInitiator type="SAMLDS" URL="https://${THISS_DOMAIN}/ds/"/>
            </SessionInitiator>

            <SessionInitiator type="Chaining" Location="/REFEDS" id="ds-refeds" relayState="cookie" authnContextClassRef="https://refeds.org/profile/mfa" forceAuthn="true">
                <SessionInitiator type="SAML2" defaultACSIndex="1" acsByIndex="false" template="bindingTemplate.html"/>
                <SessionInitiator type="Shib1" defaultACSIndex="5"/>
                <SessionInitiator type="SAMLDS" URL="https://${THISS_DOMAIN}/ds/"/>
            </SessionInitiator>

            <SessionInitiator type="Chaining" Location="/MS" id="ds-ms" relayState="cookie" authnContextClassRef="http://schemas.microsoft.com/claims/multipleauthn" forceAuthn="true">
                <SessionInitiator type="SAML2" defaultACSIndex="1" acsByIndex="false" template="bindingTemplate.html"/>
                <SessionInitiator type="Shib1" defaultACSIndex="5"/>
                <SessionInitiator type="SAMLDS" URL="https://${THISS_DOMAIN}/ds/"/>
            </SessionInitiator>

            <SessionInitiator type="Chaining" Location="/skolfed" id="ds-skolfed" relayState="cookie" authnContextClassRef="http://id.skolfederation.se/loa/2fa" forceAuthn="true">
                <SessionInitiator type="SAML2" defaultACSIndex="1" acsByIndex="false" template="bindingTemplate.html"/>
                <SessionInitiator type="Shib1" defaultACSIndex="5"/>
                <SessionInitiator type="SAMLDS" URL="https://${THISS_DOMAIN}/ds/"/>
            </SessionInitiator>

        </Sessions>

        <Errors supportContact="${SP_CONTACT}" redirectErrors="/error.php"/>

        <MetadataProvider type="XML" uri="${MD_URL}"
           backingFilePath="swamid-1.0.xml" reloadInterval="7200">
           <SignatureMetadataFilter certificate="${MD_CERT}"/>
        </MetadataProvider>

        <MetadataProvider type="XML" uri="https://idpproxy.dev.eduid.se/idp"
           backingFilePath="idpproxy.dev.xml" reloadInterval="300">
        </MetadataProvider>
        <MetadataProvider type="XML" path="skolverket.eduid.se_dnp_idp.xml"/>
	<MetadataProvider type="XML" path="teknikattan-idpproxy.sunet.se-idp.xml"/>

        <AttributeExtractor type="XML" validate="true" reloadChanges="false" path="attribute-map.xml"/>
        <AttributeResolver type="Query" subjectMatch="true"/>
        <AttributeFilter type="XML" validate="true" path="attribute-policy.xml"/>
        <CredentialResolver type="File" key="$KEYDIR/private/shibsp-${SP_HOSTNAME}.key" certificate="$KEYDIR/certs/shibsp-${SP_HOSTNAME}.crt"/>
    </ApplicationDefaults>
    <SecurityPolicyProvider type="XML" validate="true" path="security-policy.xml"/>
    <ProtocolProvider type="XML" validate="true" reloadChanges="false" path="protocols.xml"/>
</SPConfig>
EOF

augtool -s --noautoload --noload <<EOF
set /augeas/load/xml/lens "Xml.lns"
set /augeas/load/xml/incl "/etc/shibboleth/shibboleth2.xml"
load
defvar si /files/etc/shibboleth/shibboleth2.xml/SPConfig/ApplicationDefaults/Sessions/SessionInitiator[#attribute/id="ds"]
set \$si/#attribute/isDefault "true"
EOF

cat>/etc/apache2/sites-available/default.conf<<EOF
<VirtualHost *:80>
       ServerAdmin noc@sunet.se
       ServerName ${SP_HOSTNAME}
       DocumentRoot /var/www/html

</VirtualHost>
EOF

cat>/etc/apache2/sites-available/default-ssl.conf<<EOF
ServerName ${SP_HOSTNAME}
<VirtualHost *:443>
        ServerName ${SP_HOSTNAME}
        SSLProtocol All -SSLv2 -SSLv3
        SSLCompression Off
        SSLCipherSuite "EECDH+ECDSA+AESGCM EECDH+aRSA+AESGCM EECDH+ECDSA+SHA384 EECDH+ECDSA+SHA256 EECDH+aRSA+SHA384 EECDH+aRSA+SHA256 EECDH+AESGCM EECDH EDH+AESGCM EDH+aRSA HIGH !MEDIUM !LOW !aNULL !eNULL !LOW !RC4 !MD5 !EXP !PSK !SRP !DSS"
        SSLEngine On
        SSLCertificateFile $KEYDIR/certs/${SERVER_FQDN}_infra.crt
        SSLCertificateChainFile $KEYDIR/certs/infra.crt
        SSLCertificateKeyFile $KEYDIR/private/${SERVER_FQDN}_infra.key
        DocumentRoot /var/www/html
        
        Alias /shibboleth-sp/ /usr/share/shibboleth/

        ServerName ${SP_HOSTNAME}
        ServerAdmin noc@nordu.net

        <IfModule mod_headers.c>
           Header always set Strict-Transport-Security "max-age=15768000; includeSubDomains; preload"
           Header always set X-Frame-Options "SAMEORIGIN"
           Header always set X-XSS-Protection "1; mode=block"
        </IfModule>

        ErrorLog /var/log/apache2/error.log
        LogLevel warn
        CustomLog /var/log/apache2/access.log combined
        ServerSignature off

        AddDefaultCharset utf-8

        <Location /secure>
           AuthType shibboleth
           ShibRequireSession On
           require valid-user
           Options +ExecCGI
           AddHandler cgi-script .cgi
        </Location>

        <Location /refeds_mfa>
           AuthType shibboleth
           ShibRequireSession On
           ShibRequestSetting authnContextClassRef https://refeds.org/profile/mfa
           ShibRequestSetting forceAuthn true
           require valid-user
        </Location>

        <Location /MS_mfa>
           AuthType shibboleth
           ShibRequireSession On
           ShibRequestSetting authnContextClassRef http://schemas.microsoft.com/claims/multipleauthn
           ShibRequestSetting forceAuthn true
           require valid-user
        </Location>

        <Location /skolfed_mfa>
           AuthType shibboleth
           ShibRequireSession On
           ShibRequestSetting authnContextClassRef http://id.skolfederation.se/loa/2fa
           ShibRequestSetting forceAuthn true
           require valid-user
        </Location>

</VirtualHost>
EOF

adduser -- _shibd ssl-cert
mkdir -p /var/log/shibboleth
mkdir -p /var/log/apache2 /var/lock/apache2
chown _shibd /var/cache/shibboleth

echo "----"
cat /etc/shibboleth/shibboleth2.xml
echo "----"
cat /etc/apache2/sites-available/default.conf
cat /etc/apache2/sites-available/default-ssl.conf

a2ensite default
a2ensite default-ssl

service shibd start
rm -f /var/run/apache2/apache2.pid

#envsubst < /tmp/index.html > /var/www/html/index.html
#envsubst < /tmp/mfa.html > /var/www/html/mfa.html

env APACHE_LOCK_DIR=/var/lock/apache2 APACHE_RUN_DIR=/var/run/apache2 APACHE_PID_FILE=/var/run/apache2/apache2.pid APACHE_RUN_USER=www-data APACHE_RUN_GROUP=www-data APACHE_LOG_DIR=/var/log/apache2 apache2 -DFOREGROUND
