#!/bin/bash

#Run this script to setup 
ID=1                     # Uses 1 for Main and 2 for Backup
BACKUP_IP="10.0.0.0"       # IP of backup BackEnd server
ROOT_PASSWORD="password"
USER="ccag_duplicater"
PASSWORD="ccag"


sudo tee -a /etc/mysql/mysql.conf.d/mysqld.cnf > /dev/null <<EOF
[mysqld]
server-id = $ID
log_bin = /var/log/mysql/mysql-bin.log
binlog_format = ROW
auto_increment_increment = 2
auto_increment_offset = $ID
relay_log = /var/log/mysql/mysql-relay-bin.log
log_slave_updates = ON
skip_slave_start = 1
EOF

sudo systemctl restart mysql
sudo mysql -u root -p$ROOT_PASSWORD <<EOF
CREATE USER '$USER'@'$BACKUP_IP' IDENTIFIED BY '$PASSWORD';
GRANT REPLICATION SLAVE ON *.* TO '$USER'@'$BACKUP_IP';
FLUSH PRIVILEGES;
EOF

# Ensure you edit BackupReplicationSetup to match this output
echo "Record the following MASTER STATUS to BackUpReplicationSetup.sh :"
sudo mysql -u root -p$ROOT_PASSWORD -e "SHOW MASTER STATUS"