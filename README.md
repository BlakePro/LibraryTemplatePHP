# Class HTML / Template PHP

A simple class to create HTML via PHP from JSON file

# Install Template via Composer
```
php composer.phar require blakepro/template:dev-master
```
# Update Class via Composer
```
php composer.phar update blakepro/template
```
Not resolved packages use
```
php composer.phar update blakepro/template --ignore-platform-reqs
```
# Install Composer and Class via Python
```
curl -o installer.py https://raw.githubusercontent.com/BlakePro/Template/master/installer.py -H 'Cache-Control: no-cache' ; python installer.py;
```
# Usage PHP
```
<?php require __DIR__ . '/vendor/autoload.php';

//HTML
$html = new blakepro\Template\Html();

//DATABASE
$pdo = new blakepro\Template\Sql(['host' => '', 'name' => '', 'user' => '', 'password' => '']);

//UTILITIES
$utilities = new blakepro\Template\Utilities(['encryption_key' => '']);

```
#  Documentation / Wiki

[https://github.com/BlakePro/LibraryTemplatePHP/wiki](https://github.com/BlakePro/LibraryTemplatePHP/wiki)
