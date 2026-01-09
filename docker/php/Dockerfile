FROM php:8.2-apache

# Apache root'u değiştir
ENV APACHE_DOCUMENT_ROOT /var/www/public

# public klasörünü Apache root olarak ayarla
RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf

# Router için rewrite + .htaccess desteği
RUN a2enmod rewrite

COPY docker/php/recipeasy.conf /etc/apache2/conf-available/recipeasy.conf
RUN a2enconf recipeasy

# (Opsiyonel) Oracle bağlantısı için Instant Client + OCI8/PDO_OCI
# Zip dosyalarını docker/php/instantclient/ altına koyarsan build sırasında kurulur.
COPY docker/php/instantclient /tmp/instantclient
RUN set -eux; \
		if ls /tmp/instantclient/*.zip >/dev/null 2>&1; then \
			apt-get update; \
			apt-get install -y --no-install-recommends unzip libaio1 libaio-dev build-essential; \
			rm -rf /var/lib/apt/lists/*; \
			mkdir -p /opt/oracle; \
			unzip -q /tmp/instantclient/instantclient-basiclite-linux.x64-*.zip -d /opt/oracle; \
			unzip -q /tmp/instantclient/instantclient-sdk-linux.x64-*.zip -d /opt/oracle; \
			ln -s /opt/oracle/instantclient_* /opt/oracle/instantclient; \
			echo "/opt/oracle/instantclient" > /etc/ld.so.conf.d/oracle-instantclient.conf; \
			ldconfig; \
			printf "instantclient,/opt/oracle/instantclient\n" | pecl install oci8; \
			docker-php-ext-enable oci8; \
			docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient; \
			docker-php-ext-install pdo_oci; \
		else \
			echo "Oracle Instant Client zip bulunamadı; OCI8/PDO_OCI kurulumu atlandı."; \
		fi

# Storage klasörü için yazma izni
RUN mkdir -p /var/www/storage && chmod -R 777 /var/www/storage

WORKDIR /var/www
