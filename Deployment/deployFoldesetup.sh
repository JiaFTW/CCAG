#!/bin/bash

BUNDLE_DIR="$HOME/Bundles"
mkdir -p -m 775 "$BUNDLE_DIR" || { echo "Error creating Bundles directory"; exit 1; }

subfolders=("Database" "FrontEnd" "rabbitmq" "DMZ")
for folder in "${subfolders[@]}"; do
    mkdir -p -m 775 "$BASE_DIR/$folder" || { echo "Error creating $folder"; exit 1; }
done

echo "Directories created succesfully"
