#!/bin/sh
set -e

if [ "${1#-}" != "${1}" ] || [ -z "$(command -v "${1}")" ]; then
  set -- node "$@"
fi
#sexec "$@"
#chown -R nginx.nginx /code/ || true
#chown -R nginx.nginx /usr/share/nginx/html/ || true
#chown -R nginx.nginx /opt/apache/adbs || true
#su nginx
#npm start 
npm run startdev
#/code/index.js
