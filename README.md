Yii2 module for [Gravitel](http://www.gravitel.ru/).
====================================================
Refer the full documentation at http://www.gravitel.ru/upload/gravitel_rest_api.pdf

Installation
============

Step 1. Install module via composer:
```
composer require ryzhak/gravitel
```

Step 2. Configure module in config file:
```
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    ...
    'modules' => [
        'gravitel' => [
            'class' => 'ryzhak\gravitel\GravitelModule',
            'gravitelUrl' => 'http://YOUR_HOST.gravitel.ru/sys/crm_api.wcgp', // you can find url in the dashboard
            'gravitelToken' => 'YOUR_TOKEN', // you can find token in the dashboard
            'crmToken' => 'CRM_TOKEN' // you make this token yourself and set in the dashboard 
        ],
    ],
    ...
];
```

Step 3. Set CRM notifications URL in Gravitel dashboard settings:
```
https://www.YOUR_DOMAIN.com/gravitel/receive
```
This module automaticallly adds 'gravitel/receive' path to your application.

Usage
=====

Methods from CRM to Gravitel
-----------------------------

accounts
--------
Returns employee list.
```
$result = \Yii::$app->getModule('gravitel')->accounts();
var_dump($result);
/* array(3) { [0]=> array(4) { ["email"]=> string(14) "alex@gmail.com" ["ext"]=> string(3) "703" ["name"]=> string(6) "alexey" ["realName"]=> string(6) "Alexey" } [1]=> array(4) { ["email"]=> string(15) "folodia@mail.ru" ["ext"]=> string(3) "701" ["name"]=> string(5) "admin" ["realName"]=> string(26) "Администратор" } [2]=> array(3) { ["ext"]=> string(3) "702" ["name"]=> string(4) "ivan" ["realName"]=> string(8) "Иван" } } */
```

makeCall
--------
Makes phone call from user(admin) to the phone number. User string can be an internal phone number, login or real admin phone number.
```
$result = \Yii::$app->getModule('gravitel')->makeCall('89181234567', '701');
var_dump($result);
/* NULL */
```

history
-------
Returns call history by call type, limit and period. Call type can be: 'all', 'in', 'out', 'missed'. Period is a string constant which can be: 'today', 'yesterday', 'this_week', 'last_week', 'this_month', 'last_month'.
```
$result = \Yii::$app->getModule('gravitel')->history(
    GravitelModule::CALL_TYPE_ALL,
    2,
    GravitelModule::PERIOD_TODAY
);
var_dump($result);
/* array(2) { [0]=> array(9) { [0]=> string(36) "a252179d-fa9b-4b2a-9f4a-05c61af0639f" [1]=> string(3) "out" [2]=> string(12) "+79002445949" [3]=> string(23) "admin@ats23.gravitel.ru" [4]=> string(12) "+74996861574" [5]=> string(25) "2017-07-07T16:14:08+03:00" [6]=> string(1) "0" [7]=> string(2) "11" [8]=> string(0) "" } [1]=> array(9) { [0]=> string(36) "df3a53ac-c58d-4971-91f6-7c0bf37170cb" [1]=> string(3) "out" [2]=> string(12) "+79002445949" [3]=> string(23) "admin@ats23.gravitel.ru" [4]=> string(12) "+74996861574" [5]=> string(25) "2017-07-07T15:43:55+03:00" [6]=> string(1) "0" [7]=> string(2) "11" [8]=> string(0) "" } } */
```

historyByDateRange
------------------
Returns call history by call type, limit, UTC start date and UTC end date. Call type can be: 'all', 'in', 'out', 'missed'.
```
$result = \Yii::$app->getModule('gravitel')->historyByDateRange(
    GravitelModule::CALL_TYPE_ALL,
    2,
    '20170101T120000Z',
    '20200101T120000Z'
);
var_dump($result);
/* array(2) { [0]=> array(9) { [0]=> string(36) "a252179d-fa9b-4b2a-9f4a-05c61af0639f" [1]=> string(3) "out" [2]=> string(12) "+79002445949" [3]=> string(23) "admin@ats23.gravitel.ru" [4]=> string(12) "+74996861574" [5]=> string(25) "2017-07-07T16:14:08+03:00" [6]=> string(1) "0" [7]=> string(2) "11" [8]=> string(0) "" } [1]=> array(9) { [0]=> string(36) "df3a53ac-c58d-4971-91f6-7c0bf37170cb" [1]=> string(3) "out" [2]=> string(12) "+79002445949" [3]=> string(23) "admin@ats23.gravitel.ru" [4]=> string(12) "+74996861574" [5]=> string(25) "2017-07-07T15:43:55+03:00" [6]=> string(1) "0" [7]=> string(2) "11" [8]=> string(0) "" } } */
```

subscribeOnCalls
----------------
Subscribes user(admin) on receieving phone calls. Kind of setting him online and offline. Status can be 'on' or 'off'.
```
$result = \Yii::$app->getModule('gravitel')->subscribeOnCalls('admin', GravitelModule::USER_STATUS_ON);
var_dump($result);
/* string(2) "OK" */
```

Notifications from Gravitel to CRM
---------------------------------
Gravitel sends notifications to CRM via POST methon on IN and OUT phone calls, on phone call status change(event) and when Gravitel wants to know more about the client.

When you attach the module the path for notifications('gravite/receive') is automatically created for you. All you need is to set notifications URL in Gravitel dashboard settings.

There are 3 events to which you can subscribe when your application receives notification from Gravitel.

history
-------
On input and output phone calls Gravitel sends notification to your application.
```
Event::on(InputRequest::className(), InputRequest::ON_HISTORY, function($event){
    var_dump($event->sender->params);
});
/*
array(10) {
  ["cmd"]=>
  string(7) "history"
  ["type"]=>
  string(3) "out"
  ["status"]=>
  string(7) "Success"
  ["phone"]=>
  string(11) "79101234567"
  ["user"]=>
  string(4) "user"
  ["start"]=>
  string(20) "201 7 0703T1211 10 Z"
  ["duration"]=>
  string(3) "124"
  ["link"]=>
  string(21) "https://link/file.mp3"
  ["callid"]=>
  string(27) "B10D0EB124F4E64AF4EA - 1511"
  ["crm_token"]=>
  string(32) "6JDctK8K4CyrTJch3eUeLtEwvFDaHeSm"
}
*/
```

event
-----
On phone call status change Gravitel sends notification to your application.
```
Event::on(InputRequest::className(), InputRequest::ON_EVENT, function($event){
    var_dump($event->sender->params);
});
/*
array(6) {
  ["cmd"]=>
  string(5) "event"
  ["type"]=>
  string(8) "INCOMING"
  ["phone"]=>
  string(11) "79101234567"
  ["user"]=>
  string(4) "andy"
  ["callid"]=>
  string(25) "B10D0EB124F4E64AF4EA-1511"
  ["crm_token"]=>
  string(32) "6JDctK8K4CyrTJch3eUeLtEwvFDaHeSm"
}
*/
```

contact
-------
On input phone call Gravitel needs to know client name and responsible user to answer the phone call. You need to return json with client contact name and responsible user login.
```
Event::on(InputRequest::className(), InputRequest::ON_CONTACT, function($event){
    $response = [
        'contact_name' => 'contact name',
        'responsible' => 'andy'
    ];
    \Yii::$app->response->content = Json::encode($response);
});
/*
Response json:
{
    "contact_name": "contact name",
    "responsible": "andy"
}
*/
```

Tests
=====

Unit tests
----------
Run the following command:
```
vendor/bin/codecept run unit
```

Functional tests
----------------

Step 1. Create a new php file in tests/config/params.php:
```
<?php

return [
    //Gravitel params
    'gravitelUrl' => 'http://YOUR_APP.gravitel.ru/sys/crm_api.wcgp',
    'gravitelToken' => 'GRAVITEL_TOKEN',
    'crmTokenForGravitel' => 'CRM_TOKEN',
    'phoneToCall' => '89181234567' // phone number for testing purposes
];

```

Step 2. Run functional tests:
```
vendor/bin/codecept run functional
```
