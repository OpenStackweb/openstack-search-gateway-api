
# OpenStack Search Gateway API

Api gateway to behave as a Man in the middle between the web search widget and SOLR
to provide an easy facade to this latest and also to collect statistics of search terms usages


## Prerequisites

    * LAMP/LEMP environment
    * Redis
    * PHP >= 5.6
    * composer (https://getcomposer.org/)

## Install

run following commands on root folder
   * curl -s https://getcomposer.org/installer | php
   * php composer.phar install --prefer-dist
   * php composer.phar dump-autoload --optimize
   * php artisan migrate --env=YOUR_ENVIRONMENT
   * php artisan db:seed --env=YOUR_ENVIRONMENT
   * give proper rights to app/storage folder (775 and proper users)
  
## Permissions
   
Laravel may require some permissions to be configured: folders within storage and vendor require write access by the
web server. 
