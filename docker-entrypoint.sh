#!/bin/sh
set +x;
RESP=`echo -e '*1\r\n$4\r\nPING\r\n' | nc rho-redis 6379`

echo "$RESP"
if [ "$RESP" = "+PONG" ]; then 
echo "$RESP"
sed -i -e "s|session.save_handler\s*=\s*files|session.save_handler = redis|g" /etc/php7/php.ini
sed -i -e "s|;session.save_path\s*=\s*""/tmp""|session.save_path = tcp://rho-redis:6379|g" /etc/php7/php.ini
echo "$RESP"
fi

FILE=/run/php-fpm.sock;
if [ -f "$FILE" ]; then
    echo "$FILE exists." 2>> /tmp/fpm-out.txt;
else 
    echo "$FILE does not exist." 2>> /tmp/fpm-out.txt;
    /usr/sbin/php-fpm7 -D 2> /tmp/fpm-out.txt;  
    echo "$FILE started.";
    # 2>> /tmp/fpm-out.txt;
fi

#chown -R nginx.nginx /usr/share/nginx/html || true
#chown -R nginx.nginx /var/run  || true
#chown -R nginx.nginx /opt/apache/adbs || true
FILE_N=/var/run/nginx.pid;
/usr/sbin/nginx -g 'daemon off;' 2>> /tmp/nginx-out.txt;
#chown -R nginx.nginx /var/run  || true
#if [ -f "$FILE_N" ]; then
#    echo "$FILE_N exists." 2>> /tmp/nginx-out.txt;
#else 
#    echo "$FILE_N does not exist." 2>> /tmp/nginx-out.txt;
#    /usr/sbin/nginx -g 'daemon off;' 2>> /tmp/nginx-out.txt;
#    echo "$FILE_N started." 2>> /tmp/nginx-out.txt;
#fi
#do_run_nginx;