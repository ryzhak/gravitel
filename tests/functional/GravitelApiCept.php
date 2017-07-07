<?php

use ryzhak\gravitel\GravitelModule;

$params = require(__DIR__ . '/../config/params.php');

$I = new FunctionalTester($scenario);

$moduleId = 'gravitel';

/**
 * Tests
 */
$I->wantTo('test accounts method');
$resp = \Yii::$app->getModule($moduleId)->accounts();
$I->assertNotEmpty($resp);


$I->wantTo('test makeCall method');
$resp = \Yii::$app->getModule($moduleId)->makeCall($params['phoneToCall'], 'admin');
$I->assertNull($resp);


$I->wantTo('test history method');
$resp = \Yii::$app->getModule($moduleId)->history(
    GravitelModule::CALL_TYPE_ALL,
    10,
    GravitelModule::PERIOD_THIS_MONTH
);
$I->assertNotEmpty($resp);


$I->wantTo('test historyByDateRange method');
$resp = \Yii::$app->getModule($moduleId)->historyByDateRange(
    GravitelModule::CALL_TYPE_ALL,
    10,
    '20170101T120000Z',
    '20200101T120000Z'
);
$I->assertNotEmpty($resp);


$I->wantTo('test subscribeOnCalls method');
$resp = \Yii::$app->getModule($moduleId)->subscribeOnCalls('admin', GravitelModule::USER_STATUS_ON);
$I->assertEquals($resp, 'OK');
