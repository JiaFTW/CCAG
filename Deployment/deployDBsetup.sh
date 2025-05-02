#!/bin/bash
set -e

# Run this script to set up the database and tables in MySQLServer. Requires sudo access 

db_name="ccagDeploy"
db_user="ccagUser"
db_pass="12345"  #TODO store password elsewhere (config file?)

#TODO add personal deliverable tables

sql_query=$(cat <<EOF

    CREATE DATABASE IF NOT EXISTS \`${db_name}\`;
    CREATE USER IF NOT EXISTS '${db_user}'@'localhost' IDENTIFIED BY '${db_pass}';
    GRANT ALL ON \`${db_name}\`.* TO '${db_user}'@'localhost';
    FLUSH PRIVILEGES;

    USE \`${db_name}\`;

    CREATE TABLE IF NOT EXISTS bundles (
        name VARCHAR(32) NOT NULL PRIMARY KEY,
        version INT,
        status VARCHAR(16),
        machine VARCHAR(32) NOT NULL,
        path VARCHAR(225) NOT NULL,
        isCurrentVersion BOOL,
        cluster VARCHAR(32)
    );

    CREATE TABLE IF NOT EXISTS machines (
        address VARCHAR(32) NOT NULL PRIMARY KEY,
        type VARCHAR(32) NOT NULL,
        cluster VARCHAR(32) NOT NULL
    );


EOF
)

sudo mysql -e "$sql_query"

echo "Deployment Database created!"
