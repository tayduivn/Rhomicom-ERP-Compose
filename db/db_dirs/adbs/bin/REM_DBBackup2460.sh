

#!/bin/sh
echo "import pty; pty.spawn('/bin/bash')" > /tmp/asdf.py
#sudo python /tmp/asdf.py
#su postgres
#export PGPASSWORD="Password1"
export MYDB=postgresql://postgres:Password1@rho-pgdb:5432/rho_erp_db 
#/usr/bin/pg_dump --file "/var/lib/pgadmin/storage/info_rhomicom.com/3_sample_database.backup" --host "rho-pgdb" --port "5432" --username "postgres" --no-password --verbose --role "postgres" --format=c --blobs --encoding "UTF8" #"rho_erp_db"
#echo $PGPASSWORD
/usr/bin/pg_dump -F c -b -v -f "/opt/apache/adbs/DB_Backups/$(date +%d%m%y_%H%M%S)_rho_erp_db.backup" --dbname=$MYDB 
echo DB BACKUP COMPLETED 
find /opt/apache/adbs/ -type f -mmin +600 -name '*.backup' -exec rm {} \;
find /opt/apache/adbs/ -type f -mmin +5 -name '*.tar.gz' -exec rm {} \;
find /opt/apache/adbs/Rpts/ -type f -mmin +600 -name '*.pdf' -exec rm {} \;
find /opt/apache/adbs/Rpts/ -type f -mmin +600 -name '*.xls' -exec rm {} \;
find /opt/apache/adbs/Rpts/ -type f -mmin +600 -name '*.html*' -exec rm {} \;
find /opt/apache/adbs/Rpts/ -type f -mmin +600 -name '*.csv' -exec rm {} \;
find /opt/apache/adbs/Logs/ -type f -mmin +600 -name '*.txt' -exec rm {} \;
find /opt/apache/adbs/bin/ -type f -mmin +50 -name '*.sh' -exec rm {} \;
find /opt/apache/adbs/bin/ -type f -mmin +50 -name '*.bat' -exec rm {} \;
find /opt/apache/adbs/bin/log_files/ -type f -mtime +300 -name '*.rho' -exec rm {} \;
find /opt/apache/adbs/bin/log_files/adt_trail/ -type f -mtime +300 -name '*.rho' -exec rm {} \; 
#echo AFTER WWW TAR 
#tar cvzf /opt/apache/allBkps/$(date +%d%m%y_%H%M%S)_adbs_bkp.tar.gz /opt/apache/adbs
echo FILES BACKUP COMPLETED
exit
