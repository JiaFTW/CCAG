#!/bin/bash

#Run this script to setup MASTER
ID=2                     # Uses 1 for Main and 2 for Backup
MAIN_IP="10.0.0.0"       # IP of Main BackEnd server
ROOT_PASSWORD="password"       #IMPORTANT!! change this to your root pw before running
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
CREATE USER '$USER'@'$MAIN_IP' IDENTIFIED BY '$PASSWORD';
GRANT REPLICATION SLAVE ON *.* TO '$USER'@'$MAIN_IP';
FLUSH PRIVILEGES;
EOF

# Ensure your MASTER_LOG_FILE & POS in MainReplicationSetup matches this output
echo "Record the following MASTER STATUS to MainReplicationSetup.sh in LOG_FILE & LOG_POS:"
sudo mysql -u root -p$ROOT_PASSWORD -e "SHOW MASTER STATUS"