#! /usr/bin/bash

# Set permissions
#sudo chmod -R 755 ~/CCAG/FrontEnd

# Ensure symlink exists
#sudo ln -sf ~/CCAG/FrontEnd /var/www/html/ccag

# Restart Apache
#sudo systemctl restart apache2

#echo "Frontend deployed!"


# Clear PHP opcache
#Failed to restart php-fpm.service: Unit php-fpm.service not found.
#so removed it
#sudo service php-fpm restart

# Reset permissions
sudo chmod -R 755 ~/CCAG/FrontEnd
sudo chown -R www-data:www-data ~/CCAG/FrontEnd

# Ensure symlink
sudo rm -f /var/www/html/ccag
sudo ln -sf ~/CCAG/FrontEnd /var/www/html/ccag

# Restart services
sudo systemctl restart apache2
sudo systemctl restart rabbitmq-server

echo "Full deployment complete!"
