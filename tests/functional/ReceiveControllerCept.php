<?php

use ryzhak\gravitel\GravitelModule;

$params = require(__DIR__ . '/../config/params.php');

$I = new FunctionalTester($scenario);

$url = 'gravitel/receive';

$requestHistory = [
    'cmd' => GravitelModule::CMD_HISTORY,
    'type' => 'out',
    'status' => 'Success',
    'phone' => '79101234567',
    'user' => 'user',
    'start' => '20170703T121110Z',
    'duration' => '124',
    'link' => 'https://link/file.mp3',
    'callid' => 'B10D0EB124F4E64AF4EA-1511',
    'crm_token' => $params['crmTokenForGravitel']
];

$requestEvent = [
    'cmd' => GravitelModule::CMD_EVENT,
    'type' => 'INCOMING',
    'phone' => '79101234567',
    'user' => 'andy',
    'callid' => 'B10D0EB124F4E64AF4EA-1511',
    'crm_token' => $params['crmTokenForGravitel']
];

$requestContact = [
    'cmd' => GravitelModule::CMD_CONTACT,
    'phone' => '79101234567',
    'callid' => 'B10D0EB124F4E64AF4EA-1511',
    'crm_token' => $params['crmTokenForGravitel']
];

/**
 * Tests
 */
$I->wantTo('test that on not POST method returns NotAllowed error');
$I->sendPUT($url);
$I->seeResponseCodeIs(405);


$I->wantTo('test that on invalid token returns Unauthorized error');
$I->sendPOST($url, ['crm_token' => 'INVALID_TOKEN']);
$I->seeResponseCodeIs(401);


$I->wantTo('test that on invalid cmd returns BadRequest error');
$I->sendPOST($url, [
    'cmd' => 'INVALID_CMD',
    'crm_token' => $params['crmTokenForGravitel']
]);
$I->seeResponseCodeIs(400);


$I->wantTo('test that history request returns 200');
$I->sendPOST($url, $requestHistory);
$I->seeResponseCodeIs(200);


$I->wantTo('test that event request returns 200');
$I->sendPOST($url, $requestEvent);
$I->seeResponseCodeIs(200);


$I->wantTo('test that event contact returns 200');
$I->sendPOST($url, $requestContact);
$I->seeResponseCodeIs(200);
