#!/bin/bash

#Run this script to setup MASTER
ID=1                           # Uses 1 for Main and 2 for Backup
MAIN_IP="192.168.193.161"       # IP of Main BackEnd server
ROOT_PASSWORD="password"       #IMPORTANT!! change this to your root pw before running
USER="ccag_duplicater"
PASSWORD="ccag"

#sudo ufw allow from $BACKUP_IP to any port 3306

: << 'COMMENT'
sudo tee -a /etc/mysql/mysql.conf.d/mysqld.cnf > /dev/null <<EOF
[mysqld]
server-id = $ID
log_bin = /var/log/mysql/mysql-bin.log
binlog_do_db = ccagDB
auto_increment_increment = 2
auto_increment_offset = $ID   
relay_log = /var/log/mysql/mysql-relay-bin.log
bind-address = $MAIN_IP
EOF
COMMENT

sudo systemctl restart mysql
sudo mysql -u root -p$ROOT_PASSWORD <<EOF
CREATE USER '$USER'@'%' IDENTIFIED WITH mysql_native_password BY '$PASSWORD';
GRANT REPLICATION SLAVE ON *.* TO '$USER'@'%';
FLUSH PRIVILEGES;
EOF

# Ensure your MASTER_LOG_FILE & POS in BackupReplicationSetup matches this output
echo "Record the following MASTER STATUS to BackUpReplicationSetup.sh in LOG_FILE & LOG_POS:"
sudo mysql -u root -p$ROOT_PASSWORD -e "SHOW MASTER STATUS"