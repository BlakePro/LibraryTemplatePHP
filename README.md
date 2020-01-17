# Template BlakePro

A simple class to create HTML PHP from JSON file

# Install Template via Composer
```
php composer.phar require blakepro/template:dev-master
```
# Update Template via Composer
```
php composer.phar update blakepro/template
```
Not resolved packages use
```
php composer.phar update blakepro/template --ignore-platform-reqs
```
# Install Composer and Template via Python
```
curl -o installer.py https://raw.githubusercontent.com/BlakePro/Template/master/installer.py -H 'Cache-Control: no-cache' ; python installer.py;
```
#Usage PHP
```
<?php require __DIR__ . '/vendor/autoload.php';

$html = new blakepro\Template\Html();
$pdo = new blakepro\Template\Sql(['host' => '', 'name' => '', 'user' => '', 'password' => '']);
$utilities = new blakepro\Template\Utilities(['encryption_key' => '']);

```
