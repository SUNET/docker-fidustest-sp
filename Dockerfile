FROM debian:stretch
MAINTAINER leifj@sunet.se
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN apt-get -q update
RUN apt-get -y upgrade
RUN apt-get -y install apache2 libapache2-mod-shib2 ssl-cert augeas-tools libapache2-mod-php libcgi-pm-perl libemail-mime-encodings-perl php-htmlpurifier gettext-base libutf8-all-perl
RUN a2enmod rewrite ssl shib2 headers cgi proxy proxy_http
ENV SP_HOSTNAME sp.example.com
ENV SP_CONTACT info@example.com
ENV SP_ABOUT /
ENV MD_CERT signer.crt
ENV MD_URL https://md.example.com
ENV THISS_DOMAIN use.thiss.io
ENV CONTEXT thiss.io
RUN rm -f /etc/apache2/sites-available/*
RUN rm -f /etc/apache2/sites-enabled/*
ADD start.sh /start.sh
RUN chmod a+rx /start.sh
ADD attribute-map.xml /etc/shibboleth/attribute-map.xml
ADD secure /var/www/html/secure
RUN chmod a+rx /var/www/html/secure/index.cgi
COPY /apache2.conf /etc/apache2/
ADD shibd.logger /etc/shibboleth/shibd.logger
ADD index.html /tmp
ADD error.html /var/www/html/
ADD assets /var/www/html/assets
EXPOSE 443
EXPOSE 80
ENTRYPOINT ["/start.sh"]
