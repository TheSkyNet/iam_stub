ARG PHALCON_DOCKER_TAG=v5.9.2-php8.5
FROM phalconphp/cphalcon:${PHALCON_DOCKER_TAG}

LABEL maintainer=""

ARG WWWGROUP
ARG NODE_VERSION=20
ARG POSTGRES_VERSION=16

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

USER root

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        gosu supervisor curl ca-certificates zip unzip git gnupg sqlite3 libcap2-bin dnsutils libpng-dev \
    && curl -sLS https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && npm install -g npm \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /usr/share/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends yarn mysql-client postgresql-client-${POSTGRES_VERSION} \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && install-php-extensions psr igbinary msgpack memcached redis pcov xdebug imap ldap intl gd zip soap bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Allow php (non-root) to bind to privileged port 80
RUN setcap "cap_net_bind_service=+ep" /usr/local/bin/php || true

RUN groupadd --force -g ${WWWGROUP} phalcons \
    && useradd -ms /bin/bash --no-user-group -g ${WWWGROUP} -u 1337 phalcons

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
