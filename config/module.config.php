<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'PgSelligent\Client\Selligent' => 'PgSelligent\Factory\SelligentClientFactory',
        ),
    ),
);
