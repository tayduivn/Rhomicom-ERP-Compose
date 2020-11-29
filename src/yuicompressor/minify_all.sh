#!/bin/sh
for file in `find ../RHO_ERP_WEB/app/ -name "*.js"`
do
echo "Compressing $file …"
java -jar yuicompressor-2.4.8.jar --type js -o $file $file
done

for file in `find ../RHO_ERP_WEB/cmn_scrpts/ -name "*_scripts.js"`
do
echo "Compressing $file …"
java -jar yuicompressor-2.4.8.jar --type js -o $file $file
done

for file in `find ../RHO_ERP_WEB/cmn_scrpts/ -maxdepth 1 -name "*.css"`
do
echo "Compressing $file …"
java -jar yuicompressor-2.4.8.jar --type css -o $file $file
done
