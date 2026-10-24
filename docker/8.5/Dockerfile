ARG PHALCON_VERSION=5.23.0
FROM phalconphp/cphalcon:v${PHALCON_VERSION}-php8.5

COPY --from=composer:2.10.3 /usr/bin/composer /usr/bin/composer
COPY --from=node:24.22.1-trixie-slim /usr/local/ /usr/local/

LABEL maintainer=""

ARG WWWGROUP

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV PATH="/var/www/html/vendor/bin:${PATH}"

USER root

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        gosu supervisor curl ca-certificates zip unzip git sqlite3 libcap2-bin dnsutils libpng-dev \
        fonts-liberation libasound2 libatk-bridge2.0-0 libatk1.0-0 libcairo2 libcups2 libdbus-1-3 \
        libfontconfig1 libgbm1 libglib2.0-0 libgtk-3-0 libnspr4 libnss3 libpango-1.0-0 \
        libpangocairo-1.0-0 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxdamage1 libxext6 \
        libxfixes3 libxi6 libxkbcommon0 libxrandr2 libxrender1 libxss1 libxtst6 xdg-utils \
    && apt-get install -y --no-install-recommends default-mysql-client \
    && install-php-extensions psr igbinary msgpack memcached redis pcov xdebug imap ldap intl gd zip soap bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Allow php (non-root) to bind to privileged port 80
RUN setcap "cap_net_bind_service=+ep" /usr/local/bin/php || true

RUN groupadd --force -g ${WWWGROUP} phalcons \
    && useradd -ms /bin/bash --no-user-group -g ${WWWGROUP} -u 1337 phalcons

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000 8081

ENTRYPOINT ["start-container"]
