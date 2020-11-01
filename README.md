# IN A CLOUD VM
1. Install Docker
2. Custom Build our java app/Other Rhomicom Node JS APIs as container image
   -Build from node-js:alpine
   -Listen Port Range for Different Customers [3000-4000]
   -Install openjdk
   -Accepts API calls from other containers/servers to pass onto our Java Runner
   -Mounts /opt/apache/adbs folder
3. Run Main Nginx Reverse Proxy Container and configure virtual hosting ports[80,443]
4. Run docker-compose up to start necessary containers per client
5. List of Containers
   - redis:alpine container
   * php-fpm-nginx:alpine container
     -References Redis in Config
     -Mounts PHP Project Folder
     -Mounts /opt/apache/adbs folder
     -
   * postgresql container
   * mysql container
   * java container
     -Mounts /opt/apache/adbs folder

# DOCKER COMMANDS
docker build -t rho-erp-api:v1.0 .
docker run -p 3000:3000 --name rho-erp-api-1 rho-erp-api:v1.0


docker build -t rho-erp-base:v1.0 .
docker run -dp 8000:8080 --name rho-erp-base-1 rho-erp-base:v1.0

docker-compose -p RHO-ERP-SET-1 up -d --remove-orphans 
docker-compose -p RHO-ERP-SET-1 down

docker-compose -p RHO-ERP-SET-1 -f ./compose/php.yml up -d --remove-orphans 
docker-compose -p RHO-ERP-SET-1 -f ./compose/php.yml down

docker-compose -f ./compose/php.yml down --remove-orphans
docker logs --tail 50 --follow --timestamps rho-nginx

docker build -t rhomicom-erp:v1.0 .
docker run --name rho-erp-1 rhomicom-erp:v1.0  
docker run -p 8000:80 --name rho-erp-1 rhomicom-erp:v1.0
docker run -dp 8000:80 --name rho-erp-1 rhomicom-erp:v1.0
docker run --name my-nginx-pagespeed -d -p 8080:80 some-content-ngxpagespeed  
docker exec -it rho-erp-1 sh
tar -czvhf nginx.tar.gz \*

# docker exec rho-erp-1 tar Ccf $(dirname /etc/nginx/) - $(basename /etc/nginx/) | tar Cxf nginx_conf -

docker cp -L rho-erp-1:/etc/nginx/nginx.tar.gz ./nginx_conf
docker cp cc7fedd66eb3:/etc/nginx/ - | tar -x
docker cp <containerId>:/file/path/within/container /host/path/target
docker run -dp -e MYSQL_ROOT_PASSWORD=my-secret-pw

docker build --build-arg "NGINX_VERSION=1.15.5" -t openbridge/nginx .

docker-compose -f ./compose/html.yml up -d --remove-orphans
docker-compose -f ./compose/html.yml down --remove-orphans

docker pull pagespeed/nginx-pagespeed
docker run --name pagespeed-nginx -v /Users/richa/Downloads/nginx-master/nginx-master/conf/static/index.html:/usr/share/nginx/html:rw -d pagespeed/nginx-pagespeed
docker run -p 8000:80 --name rho-erp-1 rhomicom-erp:v1.0 -v C://RICHARD/DOCKER/RHO_WEB_ERP_COMPOSE/src/static:/usr/share/nginx/html:rw
docker cp -L C:/RICHARD/DOCKER/RHO_WEB_ERP_COMPOSE/src/static rho-erp-1:/usr/share/nginx/html
