FROM debian:bullseye
MAINTAINER bjorn@sunet.se
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN apt-get -q update  && \
	apt-get -y upgrade  && \
	apt-get -y install apache2 libapache2-mod-shib ssl-cert augeas-tools libapache2-mod-php libcgi-pm-perl libemail-mime-encodings-perl php-htmlpurifier gettext-base libutf8-all-perl php-sqlite3  && \
	a2enmod rewrite ssl shib2 headers cgi proxy proxy_http  && \
	rm -f /etc/apache2/sites-available/*  && \
	rm -f /etc/apache2/sites-enabled/*
ENV SP_HOSTNAME sp.example.com
ENV SP_CONTACT info@example.com
ENV SP_ABOUT /
ENV MD_CERT signer.crt
ENV MD_URL https://md.example.com
ENV THISS_DOMAIN use.thiss.io
ENV CONTEXT thiss.io
ADD start.sh /start.sh
ADD attribute-map.xml /etc/shibboleth/attribute-map.xml
ADD attribute-policy.xml /etc/shibboleth/attribute-policy.xml
ADD index.php /var/www/html/index.php
ADD assets /var/www/html/assets
ADD error.php /var/www/html/
ADD DNP /var/www/html/DNP
ADD login /var/www/html/login
ADD secure /var/www/html/secure
ADD refeds_mfa /var/www/html/refeds_mfa
ADD MS_mfa /var/www/html/MS_mfa
ADD skolfed_mfa /var/www/html/skolfed_mfa
RUN chmod a+rx /start.sh /var/www/html/secure/index.php /var/www/html/refeds_mfa/index.php /var/www/html/MS_mfa/index.php /var/www/html/skolfed_mfa/index.php /var/www/html/DNP/index.php /var/www/html/login/index.php 
COPY /apache2.conf /etc/apache2/
ADD shibd.logger /etc/shibboleth/shibd.logger
EXPOSE 443
EXPOSE 80
ENTRYPOINT ["/start.sh"]
