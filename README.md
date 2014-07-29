PgSelligent
=============

ZF2 module for Selligent Individual API


Installation
------------

### Main Setup

#### By cloning project

1. Install the [zf2-selligent](https://github.com/earlhickey/PgSelligent) ZF2 module
   by cloning it into `./vendor/`.
2. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "earlhickey/pg-selligent": "dev-master"
    }
    ```

2. Now tell composer to download PgSelligent by running the command:

    ```bash
    $ php composer.phar update
    ```

#### Post installation

1. Enabling it in your `application.config.php` file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'PgSelligent',
        ),
        // ...
    );
    ```
2. Copy `./vendor/earlhickey/pg-selligent/config/pg-selligent.global.php.dist` to `./config/autoload/pg-selligent.global.php` and change the values as desired.


### Usage

1. Subscribe

    ```php
    //  create recipient
    $recipient = new \stdClass();
    $recipient->firstname = 'John';
    $recipient->lastname = 'Doe';
    $recipient->gender = '';
    $recipient->dateOfBirth = '';
    $recipient->email = 'johndoe@domain.com';

    // Selligent email marketing opt-in
    $subscribe = $this->selligent()->subscribe($recipient);
    ```

3. Unsubscribe

    ```php
    //  create recipient
    $recipient = new \stdClass();
    $recipient->email = 'johndoe@domain.com';

    // Selligent email marketing opt-out
    $unsubscribe = $this->selligent()->unsubscribe($recipient);
    ```
