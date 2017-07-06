<?php

namespace ryzhak\gravitel\models;

use ryzhak\gravitel\GravitelModule;
use Yii;
use yii\base\Event;
use yii\base\Exception;
use yii\base\Model;

class InputRequest extends Model
{

    /**
     * Events
     */
    const ON_HISTORY = 'onHistory';
    const ON_EVENT = 'onEvent';
    const ON_CONTACT = 'onContact';

    /**
     * POST data from gravitel request.
     */
    public $params;

    /**
     * Validates token.
     *
     * @param string $realToken Real crm token.
     * @return bool
     */
    public function validateToken($realToken = null) {

        if(!isset($this->params['crm_token'])) return false;

        if($this->params['crm_token'] != $realToken) return false;

        return true;

    }

    /**
     * Validates cmd parameter.
     *
     * @return bool
     */
    public function validateCmd() {

        $validCommands = [
            GravitelModule::CMD_HISTORY,
            GravitelModule::CMD_EVENT,
            GravitelModule::CMD_CONTACT
        ];

        if(!isset($this->params['cmd'])) return false;

        if(!in_array($this->params['cmd'], $validCommands)) return false;

        return true;

    }

    /**
     * Triggers events depending on the input request.
     */
    public function proceedEvents() {

        switch($this->params['cmd']){
            case GravitelModule::CMD_HISTORY:
                $this->trigger(self::ON_HISTORY);
                break;
            case GravitelModule::CMD_EVENT:
                $this->trigger(self::ON_EVENT);
                break;
            case GravitelModule::CMD_CONTACT:
                $this->trigger(self::ON_CONTACT);
                break;
        }

    }

}
