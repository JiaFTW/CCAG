#!/bin/bash
set -e

# Run this script to set up the database and tables in MySQLServer. Requires sudo access 

db_name="ccagDB"
db_user="ccagUser"
db_pass="12345"  #TODO store password elsewhere (config file?)

#TODO add personal deliverable tables

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
        end_time INT NOT NULL,
        FOREIGN KEY (uid) REFERENCES accounts(uid)
    );


    CREATE TABLE IF NOT EXISTS recipes (
        rid INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(75) NOT NULL,
        num_ingredients INT,
        ingredients TEXT,
        calories INT,
        servings INT
    );
    
    CREATE TABLE IF NOT EXISTS labels (
        label_id INT AUTO_INCREMENT PRIMARY KEY,
        label_name VARCHAR(25) NOT NULL,
        description TEXT
    );

    CREATE TABLE IF NOT EXISTS recipe_labels (
        rid INT NOT NULL,
        label_id INT NOT NULL,
        PRIMARY KEY (rid, label_id), 
        FOREIGN KEY (rid) REFERENCES recipes(rid) ON DELETE CASCADE,
        FOREIGN KEY (label_id) REFERENCES labels(label_id) ON DELETE CASCADE
    );
    
    CREATE TABLE IF NOT EXISTS bookmarks (
        uid INT NOT NULL,
        rid INT NOT NULL,
        PRIMARY KEY (uid, rid),
        FOREIGN KEY (uid) REFERENCES accounts(uid) ON DELETE CASCADE,
        FOREIGN KEY (rid) REFERENCES recipes(rid) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS reviews (
        rate_id INT AUTO_INCREMENT PRIMARY KEY,
        uid INT NOT NULL,
        rid INT NOT NULL,
        rating TINYINT NOT NULL,
        description TEXT,
        FOREIGN KEY (uid) REFERENCES accounts(uid),
        FOREIGN KEY (rid) REFERENCES recipes(rid)
    );

    CREATE TABLE IF NOT EXISTS mealplans (
        cid INT AUTO_INCREMENT PRIMARY KEY,
        uid INT NOT NULL,
        FOREIGN KEY (uid) REFERENCES accounts(uid)
    );

    CREATE TABLE IF NOT EXISTS mealplan_entries (
        cid INT NOT NULL,
        rid INT NOT NULl,
        day VARCHAR(9) NOT NULL,
        meal_type VARCHAR(9) NOT NULL,
        FOREIGN KEY (cid) REFERENCES mealplans(cid) ON DELETE CASCADE,
        FOREIGN KEY (rid) REFERENCES recipes(rid)
    );
EOF
)

sudo mysql -e "$sql_query"

echo "Databased created!"
