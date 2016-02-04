GeoLocation module
==========

This module is aimed to store the locational data like Countries and Cities as well as timezones for them.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist veksoftware/yii2-geolocation-ref "*"
```

or add

```
"veksoftware/yii2-geolocation-ref": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
// config/main.php
<?php
    'modules' => [
        'location' => [
            'class' => '\geolocation\GeoLocation',
        ]
    ]
```

Then you can use it in your code :

```php

<?php

?>
```
