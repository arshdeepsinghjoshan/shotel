
 @copyright : ASk. < http://arshresume.epizy.com/ >
 @author	 : Arshdeep Singh < arshdeepsinghjoshan84@gmail.com >
 
  All Rights Reserved.
  Proprietary and confidential :  All information contained herein is, and remains
  the property of ASK. and its partners.
  Unauthorized copying of this file, via any medium is strictly prohibited.
  
# logger

#### About Brief
* It is exception logger.

## INSTALLATION 

## If you need to install single module 


run module migrations

```
-php artisan migrate --path=modules/Logger/Migrations
```

config/logging.php configuration file:

  'stack' => [
            'driver' => 'stack',
            'channels' => ['ask_logger', 'single'],
            'ignore_exceptions' => false,
        ],
        'ask_logger' => [
            'driver' => 'custom',
            'handler' => MySQLLoggingHandler::class,
            'via' => MySQLCustomLogger::class,
            'level' => 'debug',
        ],
