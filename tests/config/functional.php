<?php

$params = require(__DIR__ . '/params.php');

return [
    'id' => 'gravitelApp',
    'basePath' => dirname(__DIR__),
    'modules' => [
        'gravitel' => [
            'class' => 'ryzhak\gravitel\GravitelModule',
            'gravitelUrl' => $params['gravitelUrl'],
            'gravitelToken' => $params['gravitelToken'],
            'crmToken' => $params['crmTokenForGravitel']
        ],
    ],
];
