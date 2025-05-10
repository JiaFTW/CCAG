#!/bin/bash

# IMPORTANT run this after doing master setup for both Main and Backup Servers first
#

MAIN_IP="192.168.194.15"       # IP of Main BackEnd server
MAIN_MASTER_LOG_FILE="mysql-bin.000001"  # From Main Server's SHOW MASTER STATUS
MAIN_MASTER_LOG_POS="919"                # From Main Server's SHOW MASTER STATUS
ROOT_PASSWORD="password"
USER="ccag_duplicater"
PASSWORD="ccag"


sudo mysql -u root -p$ROOT_PASSWORD <<EOF
STOP SLAVE;
CHANGE MASTER TO
MASTER_HOST='$MAIN_IP',
MASTER_USER='$USER',
MASTER_PASSWORD='$PASSWORD',
MASTER_LOG_FILE='$MAIN_MASTER_LOG_FILE',
MASTER_LOG_POS=$MAIN_MASTER_LOG_POS;
START SLAVE;
EOF


echo "Replication Status:"
sudo mysql -u root -p$ROOT_PASSWORD -e "SHOW SLAVE STATUS\G"