FROM node:lts-alpine3.12
RUN apk update upgrade
RUN apk --no-cache --update --upgrade add nodejs npm openjdk11-jre
# --repository=http://dl-cdn.alpinelinux.org/alpine/v3.10/main

RUN echo "http://dl-cdn.alpinelinux.org/alpine/edge/main" > /etc/apk/repositories
RUN echo "http://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories
RUN echo "http://dl-cdn.alpinelinux.org/alpine/edge/testing" >> /etc/apk/repositories
#RUN echo "http://dl-cdn.alpinelinux.org/alpine/v3.8/community" >> /etc/apk/repositories
#RUN echo "http://dl-cdn.alpinelinux.org/alpine/v3.8/main" >> /etc/apk/repositories

# install chromium
RUN apk -U --no-cache \
  --allow-untrusted add \
  zlib-dev \
  chromium \
  xvfb \
  wait4ports \
  xorg-server \
  dbus \
  ttf-freefont \
  grep \ 
  udev \
  && apk del --purge --force zlib-dev libc-utils \
  #linux-headers binutils-gold gnupg
  && mkdir -p /opt/apache/adbs \
  && mkdir -p /usr/share/nginx/html \
  && rm -rf /var/lib/apt/lists/* \
  /var/cache/apk/* \
  /usr/share/man \
  /tmp/* \
  /usr/lib/node_modules/npm/man \
  /usr/lib/node_modules/npm/doc \
  /usr/lib/node_modules/npm/html \
  /usr/lib/node_modules/npm/scripts

RUN apk --no-cache add msttcorefonts-installer fontconfig && \
  update-ms-fonts && \
  fc-cache -f

ENV CHROME_BIN=/usr/bin/chromium-browser
ENV CHROME_PATH=/usr/lib/chromium/
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=1

RUN addgroup -S nginx --gid 2021
RUN adduser -u 2020 -D -S -h /var/cache/nginx -s /sbin/nologin -G nginx nginx

COPY docker-entrypoint.sh /usr/local/bin/
# Application specific run commands 
RUN npm install -g nodemon

ADD ./code /code

RUN chown -R nginx.nginx /usr/share/nginx/html && \
  chown -R nginx.nginx /opt/apache/adbs && \
  chown -R nginx.nginx /code

USER nginx

EXPOSE 3000

WORKDIR /code
#RUN pm2 ecosystem
RUN npm install

#CMD ["pm2", "start", "/code/ecosystem.config.js"]
#ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/bin/sh","/usr/local/bin/docker-entrypoint.sh"]
