zf2-selligent
=============

ZF2 module for Selligent Individual API


Installation
------------

### Main Setup

#### By cloning project

1. Install the [zf2-selligent](https://github.com/earlhickey/zf2-selligent) ZF2 module
   by cloning it into `./vendor/`.
2. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "earlhickey/zf2-selligent": "dev-master"
    }
    "repositories" : [
            "type":"vcs",
            "url": "https://github.com/earlhickey/zf2-selligent"
        }
    ]
    ```

2. Now tell composer to download ZfcUser by running the command:

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
            'Selligent',
        ),
        // ...
    );
    ```
2. Copy `./vendor/earlhickey/zf2-selligent/config/selligent.global.php.dist` to `./config/autoload/selligent.global.php` and change the values as desired.


### Usage

1. Opt-in

    ```php
    <?php
    // Selligent email marketing opt-in
    $selligent = $this->selligent()->subscribe($recipient);
    ```
2. Opt-out

    ```php
    <?php
    // Selligent email marketing opt-out
    $selligent = $this->selligent()->unsubscribe($recipient);
    ```


