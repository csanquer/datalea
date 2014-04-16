Fakery Generator - a Random Test Fixtures Data Generator
========================================================

Install
-------

* copy config sample file and edit the copy with your environment values

```bash
cp app/config/parameters.yml.dist app/config/parameters.yml
```

* get composer http://getcomposer.org/ and install dependencies

```bash
    curl -s https://getcomposer.org/installer | php
```

* install dependencies
    
```bash
    php composer.phar install
```

* set your web server document root to `web` directory

* clean cache

```bash
    php app/console cache:clear
```
