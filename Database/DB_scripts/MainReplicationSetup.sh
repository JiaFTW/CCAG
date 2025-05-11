#!/bin/bash

# IMPORTANT run this only once. Must have done master setup for both Main and Backup Servers first
#

BACKUP_IP="192.168.194.20"       # IP of backup BackEnd server
BACKUP_MASTER_LOG_FILE="mysql-bin.000001"  # From Backup Server's SHOW MASTER STATUS
BACKUP_MASTER_LOG_POS="894"                # From Backup Server's SHOW MASTER STATUS
ROOT_PASSWORD="password"
USER="ccag_duplicater"
PASSWORD="ccag"


sudo mysql -u root -p$ROOT_PASSWORD <<EOF
STOP SLAVE;
CHANGE MASTER TO
MASTER_HOST='$BACKUP_IP',
MASTER_USER='$USER',
MASTER_PASSWORD='$PASSWORD',
MASTER_LOG_FILE='$BACKUP_MASTER_LOG_FILE',
MASTER_LOG_POS=$BACKUP_MASTER_LOG_POS;
START SLAVE;
EOF


echo "Replication Status:"
sudo mysql -u root -p$ROOT_PASSWORD -e "SHOW SLAVE STATUS\G"