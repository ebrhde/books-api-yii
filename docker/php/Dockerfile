FROM php:8.1-fpm

RUN apt-get update -y
RUN apt-get -y install gcc make autoconf libc-dev pkg-config libzip-dev

RUN apt-get install -y --no-install-recommends \
	git \
	libssl-dev libssl-doc libsasl2-dev \
	libxml2-dev \
	curl libcurl4-openssl-dev

RUN docker-php-ext-install bcmath ctype curl dom
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql phar posix
RUN docker-php-ext-install session simplexml sockets

RUN apt-get update -y && apt-get install -y apt-transport-https locales gnupg

# set locale to utf-8
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && locale-gen
ENV LANG='en_US.UTF-8' LANGUAGE='en_US:en' LC_ALL='en_US.UTF-8'

#--------------------------------------------------------------------------
# Final Touches
#--------------------------------------------------------------------------

# install required libs for health check
#RUN apt-get -y install libfcgi0ldbl nano htop iotop lsof cron mariadb-client redis-tools wget
RUN apt-get -y install wget

# install composer
RUN EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)" && \
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
	ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")" && \
	if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then >&2 echo 'ERROR: Invalid installer checksum' && exit 1; fi

RUN php composer-setup.php --quiet && rm composer-setup.php && \
	mv composer.phar /usr/local/sbin/composer && \
	chmod +x /usr/local/sbin/composer

# Set default work directory
#ADD scripts/* /usr/local/bin/
RUN chmod +x  /usr/local/bin/*

# Add default configuration files
#ADD configs/php.ini /usr/local/etc/php/
#ADD configs/www.conf /usr/local/etc/php-fpm.d/

# Health check
RUN echo '#!/bin/bash' > /healthcheck && \
	echo 'env -i SCRIPT_NAME=/health SCRIPT_FILENAME=/health REQUEST_METHOD=GET cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1' >> /healthcheck && \
	chmod +x /healthcheck

# Clean up
RUN apt-get remove -y git && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /