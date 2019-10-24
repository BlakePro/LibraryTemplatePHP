import os
os.system("php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\"")
os.system("php -r \"if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;\"");
os.system("php composer-setup.php");
os.system("php -r \"unlink('composer-setup.php');\"");
os.system("php composer.phar require blakepro/template:dev-master");
os.system("php -r \"unlink('installer.py');\"");
os.system("curl -o config.php https://raw.githubusercontent.com/BlakePro/Template/master/config.php");
os.system("curl -o index.php https://raw.githubusercontent.com/BlakePro/Template/master/index.php");
print "BlakePro Template installed succesfully"
print "View file index.php"
