import os
os.system("php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\"")
os.system("php -r \"if (hash_file('sha384', 'composer-setup.php') === 'c5b9b6d368201a9db6f74e2611495f369991b72d9c8cbd3ffbc63edff210eb73d46ffbfce88669ad33695ef77dc76976') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;\"")
os.system("php composer-setup.php")
os.system("php -r \"unlink('composer-setup.php');\"")
os.system("php composer.phar require blakepro/template:dev-master")
os.system("php -r \"unlink('installer.py');\"")
print "\nLoading... \n"
os.system("curl -s -o template.json https://raw.githubusercontent.com/BlakePro/Template/master/template.json")
os.system("curl -s -o config.php https://raw.githubusercontent.com/BlakePro/Template/master/config.php")
os.system("curl -s -o index.php https://raw.githubusercontent.com/BlakePro/Template/master/index.php")
print "BlakePro Template installed succesfully \n \nFile index.php \n\nPath:\n"
os.system("pwd");
print "\n"
