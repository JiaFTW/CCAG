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
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR NOT NULL,
        version INT NOT NULL,
        status VARCHAR NOT NULl,
        container VARCHAR NOT NULL,
        path VARCHAR NOT NULL
    );


EOF
)

sudo mysql -e "$sql_query"

echo "Deployment Database created!"
