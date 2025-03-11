#! /usr/bin/bash
sudo rm -rf /var/www/CCAG/

sudo cp -r ~/CCAG/ /var/www/

sudo service apache2 restart