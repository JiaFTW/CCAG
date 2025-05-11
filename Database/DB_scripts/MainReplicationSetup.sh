#!/bin/bash

# IMPORTANT Must have done master setup for both Main and Backup Servers first


BACKUP_IP="192.168.193.78"       # IP of backup BackEnd server
BACKUP_MASTER_LOG_FILE="mysql-bin.000002"  # Insert From Backup Server's SHOW MASTER STATUS message
BACKUP_MASTER_LOG_POS="864"                # Insert From Backup Server's SHOW MASTER STATUS message
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
#run this command above in command line to check setup issues
