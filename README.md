# Monolog SendinBlue Handler 

Allow Monolog to send logs via SendinBlue Transactional API V3

## Usage
```php

use Monolog\Logger;
use Svbk\Monolog\Sendinblue\Handler as SendinblueHandler;

$logger = new Logger( 'wordpress' );

$logger->pushHandler( 
    new SendinblueHandler( 
        'your-sendinblue-v3-apikey',
        'sender@example.com', 
        'recipient@example.com', 
        'My Log Emails Subject',
        Monolog\Logger::ERROR
    )
);

```

To get the API key go to  [https://account.sendinblue.com/advanced/api](https://account.sendinblue.com/advanced/api) adn create/select a V3 key.