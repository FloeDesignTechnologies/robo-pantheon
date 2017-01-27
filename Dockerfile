FROM composer/composer

# Create the account for the user to use when running the container
ARG USER_UID=1000
ARG USER=php
RUN useradd --uid ${USER_UID} --create-home ${USER} &&\
    # Make sure the running user own the $COMPOSER_HOME
    chown ${USER_UID} -R ${COMPOSER_HOME}

# Install docker and docker-compose inside the container
ARG DOCKER_VERSION=latest
ARG DOCKER_COMPOSE_VERSION=1.10.0
RUN cd /tmp &&\
    curl "https://get.docker.com/builds/`uname -s`/`uname -m`/docker-${DOCKER_VERSION}.tgz" | tar xz &&\
    mv docker/* /usr/bin &&\
    rm -rf docke* &&\
    curl  -L "https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-`uname -s`-`uname -m`" > /usr/local/bin/docker-compose &&\
    chmod +x /usr/local/bin/docker-compose &&\
    addgroup --gid 999 docker &&\
    usermod -a -G docker ${USER}

# Run container as the non-root user configured and created above
USER ${USER_UID}
