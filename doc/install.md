Fakery Generator - Installation
===============================

* copy config sample file and edit the copy with your environment values

```bash
cp app/config/parameters.yml.dist app/config/parameters.yml
```

* get [composer](http://getcomposer.org/) PHP Package manager and install dependencies

```bash
    curl -s https://getcomposer.org/installer | php
    php composer.phar install
```

* set your web server document root to `web` directory (see [Silex documentation](http://silex.sensiolabs.org/doc/web_servers.html))

* clean cache

```bash
    php app/console cache:clear
```


