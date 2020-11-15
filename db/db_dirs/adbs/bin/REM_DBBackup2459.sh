

#!/bin/sh
echo "import pty; pty.spawn('/bin/bash')" > /tmp/asdf.py
#sudo python /tmp/asdf.py
#su postgres
#export PGPASSWORD="Password1"
export MYDB=postgresql://postgres:Password1@127.0.0.1:5432/adbs 
#echo $PGPASSWORD
pg_dump -F c -b -v -f "/opt/apache/rho_erp_db/DB_Backups/$(date +%d%m%y_%H%M%S)_adbs.backup" --dbname=$MYDB 
echo DB BACKUP COMPLETED 
find /opt/apache/rho_erp_db/ -type f -mmin +600 -name '*.backup' -exec rm {} \;
find /opt/apache/rho_erp_db/ -type f -mmin +5 -name '*.tar.gz' -exec rm {} \;
find /opt/apache/rho_erp_db/Rpts/ -type f -mmin +600 -name '*.pdf' -exec rm {} \;
find /opt/apache/rho_erp_db/Rpts/ -type f -mmin +600 -name '*.xls' -exec rm {} \;
find /opt/apache/rho_erp_db/Rpts/ -type f -mmin +600 -name '*.html*' -exec rm {} \;
find /opt/apache/rho_erp_db/Rpts/ -type f -mmin +600 -name '*.csv' -exec rm {} \;
find /opt/apache/rho_erp_db/Logs/ -type f -mmin +600 -name '*.txt' -exec rm {} \;
find /opt/apache/rho_erp_db/bin/ -type f -mmin +50 -name '*.sh' -exec rm {} \;
find /opt/apache/rho_erp_db/bin/ -type f -mmin +50 -name '*.bat' -exec rm {} \;
find /opt/apache/rho_erp_db/bin/log_files/ -type f -mtime +300 -name '*.rho' -exec rm {} \;
find /opt/apache/rho_erp_db/bin/log_files/adt_trail/ -type f -mtime +300 -name '*.rho' -exec rm {} \; 
#echo AFTER WWW TAR 
#tar cvzf /opt/apache/allBkps/$(date +%d%m%y_%H%M%S)_rho_erp_db_bkp.tar.gz /opt/apache/rho_erp_db
echo FILES BACKUP COMPLETED
exit
