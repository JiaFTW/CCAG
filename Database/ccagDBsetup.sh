#!/bin/bash
set -e

# Run this script to set up the database and tables in MySQLServer. Requires sudo access 

db_name="ccagDB"
db_user="ccagUser"
db_pass="12345"  #TODO store password elsewhere (config file?)

#TODO add session table

sql_query=$(cat <<EOF

    CREATE DATABASE IF NOT EXISTS \`${db_name}\`;
    CREATE USER IF NOT EXISTS '${db_user}'@'localhost' IDENTIFIED BY '${db_pass}';
    GRANT ALL ON \`${db_name}\`.* TO '${db_user}'@'localhost';
    FLUSH PRIVILEGES;

    USE \`${db_name}\`;

    CREATE TABLE IF NOT EXISTS accounts (
        uid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(32) UNIQUE NOT NULL,
        email VARCHAR(320) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS sessions (
        sid INT AUTO_INCREMENT PRIMARY KEY,
        uid INT NOT NULL,
        cookie_token VARCHAR(255) NOT NULL,
        start_time INT NOT NULL,
        end _time INT NOT NULL,
        FOREIGN KEY (uid) REFERNCES accounts(uid)
     );
EOF
)

sudo mysql -e "$sql_query"

echo "Databased created!"
