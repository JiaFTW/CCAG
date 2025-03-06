#!/bin/bash
set -e

#IMPORTANT: ONLY RUN THIS ONCE AFTER USING ccagDBsetup!!!
# Run this script to add Indexing and populate labels table. Requires sudo access 


db_pass="12345"  

sql_query=$(cat <<EOF

 

    USE \`${db_name}\`;

    CREATE FULLTEXT INDEX idx_recipes_name ON recipes(name);
    CREATE FULLTEXT INDEX idx_labels_name ON labels(labels_name);

    INSERT INTO labels(label_name) VALUES
    ('balanced'), ('high-fiber'), ('high-protien'), ('low-carb'), ('low-fat'), ('low-sodium'),
    ('alcohol-cocktail'), ('alcohol-free'), ('celery-free'), ('crustacean-free'), ('dairy-free'), 
    ('DASH'), ('egg-free'), ('fish-free'), ('fodmap-free'), ('gluten-free'), ('immuno-supportive'),
    ('keto-friendly'), ('kidney-friendly'), ('kosher'), ('low-potassium'), ('low-sugar'), ('lupine-free'),
    ('Mediterranean'), ('mollusk-free'), ('No-oil-added'), ('paleo'), ('peanut-free'), ('pecatarian'),
    ('pork-free'), ('red-meat-free'), ('sesame-free'), ('shellfish-free'), ('soy-free'), ('sugar-conscious'),
    ('sulfite-free'), ('tree-nut-free'), ('vegan'), ('vegetarian'), ('wheat-free');
    
EOF
)


sudo mysql -e "$sql_query"

echo "One time setup successful"
