<?php

use ryzhak\gravitel\GravitelModule;
use ryzhak\gravitel\models\InputRequest;
use yii\base\Event;

class InputRequestTest extends \Codeception\Test\Unit
{

    protected $tester;

    private $_inputRequest;

    protected function _before()
    {
        $this->_inputRequest = new InputRequest();
    }

    protected function _after()
    {
    }

    public function testValidateToken_OnEmptyCrmToken_ReturnsFalse()
    {
        $this->_inputRequest->params = [];

        $this->tester->assertFalse($this->_inputRequest->validateToken());
    }

    public function testValidateToken_OnInvalidRealToken_ReturnsFalse()
    {
        $this->_inputRequest->params = [
            'crm_token' => 'ANY_TOKEN'
        ];

        $this->tester->assertFalse($this->_inputRequest->validateToken('NOT_MATCHING_TOKEN'));
    }

    public function testValidateToken_OnValidRealToken_ReturnsTrue()
    {
        $this->_inputRequest->params = [
            'crm_token' => 'TOKEN'
        ];

        $this->tester->assertTrue($this->_inputRequest->validateToken('TOKEN'));
    }

    public function testValidateCmd_OnEmptyCmd_ReturnsFalse()
    {
        $this->_inputRequest->params = [];

        $this->tester->assertFalse($this->_inputRequest->validateCmd());
    }

    public function testValidateCmd_OnInvalidCmd_ReturnsFalse()
    {
        $this->_inputRequest->params = [
            'cmd' => 'INVALID_CMD'
        ];

        $this->tester->assertFalse($this->_inputRequest->validateCmd());
    }

    public function testValidateCmd_OnValidCmd_ReturnsTrue()
    {
        $this->_inputRequest->params = [
            'cmd' => GravitelModule::CMD_HISTORY
        ];

        $this->tester->assertTrue($this->_inputRequest->validateCmd());
    }

    public function testProceedEvents_OnCmdHistory_OnHistoryIsTriggered()
    {

        $isTriggered = false;

        Event::on(InputRequest::className(), InputRequest::ON_HISTORY, function($event) use (&$isTriggered) {
            $isTriggered = true;
        });

        $this->_inputRequest->params = [
            'cmd' => GravitelModule::CMD_HISTORY
        ];

        $this->_inputRequest->proceedEvents();

        $this->tester->assertTrue($isTriggered);

    }

    public function testProceedEvents_OnCmdEvent_OnEventIsTriggered()
    {

        $isTriggered = false;

        Event::on(InputRequest::className(), InputRequest::ON_EVENT, function($event) use (&$isTriggered) {
            $isTriggered = true;
        });

        $this->_inputRequest->params = [
            'cmd' => GravitelModule::CMD_EVENT
        ];

        $this->_inputRequest->proceedEvents();

        $this->tester->assertTrue($isTriggered);

    }

    public function testProceedEvents_OnCmdContact_OnContactIsTriggered()
    {

        $isTriggered = false;

        Event::on(InputRequest::className(), InputRequest::ON_CONTACT, function($event) use (&$isTriggered) {
            $isTriggered = true;
        });

        $this->_inputRequest->params = [
            'cmd' => GravitelModule::CMD_CONTACT
        ];

        $this->_inputRequest->proceedEvents();

        $this->tester->assertTrue($isTriggered);

    }

    public function testProceedEvents_OnUnknownCmd_EventsAreNotTriggered()
    {

        $isTriggered = false;

        $func = function($event) use (&$isTriggered) {
            $isTriggered = true;
        };

        Event::on(InputRequest::className(), InputRequest::ON_HISTORY, $func);
        Event::on(InputRequest::className(), InputRequest::ON_EVENT, $func);
        Event::on(InputRequest::className(), InputRequest::ON_CONTACT, $func);

        $this->_inputRequest->params = [
            'cmd' => 'UNKNOWN_CMD'
        ];

        $this->_inputRequest->proceedEvents();

        $this->tester->assertFalse($isTriggered);

    }

}
