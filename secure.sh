#!/bin/bash
cd src/
find . -type f -exec chmod 0644 {} \;
find . -type d -exec chmod 0755 {} \;
find . -type d -name "tmp" -exec chmod -R 777 {} \;
find . -type d -name "pem" -exec chmod -R 777 {} \;
find . -type d -name "samples" -exec chmod -R 777 {} \;
find . -type d -name "images" -exec chmod -R 777 {} \;
find . -type d -name "wp-content" -exec chmod -R 777 {} \;
cd ../db/db_dirs/
find . -type f -exec chmod 0644 {} \;
find . -type d -exec chmod 0777 {} \;
find . -type f -name "superAdminConfigFile.rhocnfg" -exec chmod 0777 {} \;
find . -type d -name "bin" -exec chmod -R 777 {} \;
find . -type d -name "bin" -exec chmod -R g+s {} \;
find . -type f -name "*.jar" -exec chmod +x {} \;
echo SCRIPT COMPLETED
exit

