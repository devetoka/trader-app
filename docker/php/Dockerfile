FROM phpdockerio/php:8.2-fpm
WORKDIR "/application"

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        php8.2-amqp \
        php8.2-ast \
        php8.2-bcmath \
        php8.2-bz2 \
        php8.2-cgi \
        php8.2-dba \
        php8.2-decimal \
        php8.2-ds \
        php8.2-enchant \
        php8.2-gd \
        php8.2-gearman \
        php8.2-gmagick \
        php8.2-gmp \
        php8.2-gnupg \
        php8.2-grpc \
        php8.2-http \
        php8.2-igbinary \
        php8.2-imagick \
        php8.2-imap \
        php8.2-inotify \
        php8.2-interbase \
        php8.2-intl \
        php8.2-ldap \
        php8.2-lz4 \
        php8.2-mailparse \
        php8.2-maxminddb \
        php8.2-mcrypt \
        php8.2-memcache \
        php8.2-memcached \
        php8.2-mongodb \
        php8.2-msgpack \
        php8.2-mysql \
        php8.2-oauth \
        php8.2-odbc \
        php8.2-pcov \
        php8.2-pgsql \
        php8.2-phpdbg \
        php8.2-protobuf \
        php8.2-pspell \
        php8.2-psr \
        php8.2-raphf \
        php8.2-redis \
        php8.2-rrd \
        php8.2-smbclient \
        php8.2-snmp \
        php8.2-soap \
        php8.2-solr \
        php8.2-sqlite3 \
        php8.2-ssh2 \
        php8.2-swoole \
        php8.2-sybase \
        php8.2-tidy \
        php8.2-uuid \
        php8.2-vips \
        php8.2-xdebug \
        php8.2-xhprof \
        php8.2-xmlrpc \
        php8.2-xsl \
        php8.2-yac \
        php8.2-yaml \
        php8.2-zmq \
        php8.2-zstd; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY ./app .
