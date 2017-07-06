<?php

namespace ryzhak\gravitel;

use yii\base\Exception;
use yii\base\Module;
use yii\helpers\Json;

class GravitelModule extends Module
{

    /**
     * Commands.
     */
    const CMD_ACCOUNTS = 'accounts';
    const CMD_MAKE_CALL = 'makeCall';
    const CMD_HISTORY = 'history';
    const CMD_EVENT = 'event';
    const CMD_CONTACT = 'contact';

    /**
     * Call types.
     */
    const CALL_TYPE_ALL = 'all';
    const CALL_TYPE_IN = 'in';
    const CALL_TYPE_OUT = 'out';
    const CALL_TYPE_MISSED = 'missed';

    /**
     * History periods.
     */
    const PERIOD_TODAY = 'today';
    const PERIOD_YESTERDAY = 'yesterday';
    const PERIOD_THIS_WEEK = 'this_week';
    const PERIOD_LAST_WEEK = 'last_week';
    const PERIOD_THIS_MONTH = 'this_month';
    const PERIOD_LAST_MONTH = 'last_month';

    /**
     * User statuses.
     */
    const USER_STATUS_ON = 'on';
    const USER_STATUS_OFF = 'off';

    /**
     * Gravitel backend url.
     */
    public $gravitelUrl = null;

    /**
     * Gravitel token.
     */
    public $gravitelToken = null;

    /**
     * CRM token which gravitel will send in requests.
     */
    public $crmToken = null;

    /**
     * On module init.
     * @throws Exception
     */
    public function init() {

        parent::init();

        if(!$this->gravitelUrl) throw new Exception('gravitelUrl property is not set in GravitelModule settings');
        if(!$this->gravitelToken) throw new Exception('gravitelToken property is not set in GravitelModule settings');
        if(!$this->crmToken) throw new Exception('crmToken property is not set in GravitelModule settings');

    }

    /**
     * Makes http request.
     *
     * @param string $cmd Command name.
     * @param array $data Request parameters.
     * @return mixed
     * @throws Exception
     */
    public function makeRequest($cmd = null, $data = []) {

        if(!$cmd) throw new Exception('cmd name can not be empty');

        $data['cmd'] = $cmd;
        $data['token'] = $this->gravitelToken;

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($this->gravitelUrl, false, $context);

        return $cmd == self::CMD_HISTORY ? $this->parseCsv($result) : Json::decode($result);

    }

    /**
     * Parses csv string into array.
     *
     * @param string $csvStr CSV input string.
     * @return array
     * @throws Exception
     */
    public function parseCsv($csvStr = '') {

        $rows = str_getcsv($csvStr, "\n");
        foreach($rows as &$row) $row = str_getcsv($row);

        return $rows;

    }

    /**
     * Gravitel API function.
     * Returns all company employees.
     *
     * @return mixed
     * @throws Exception
     */
    public function accounts() {
        return $this->makeRequest(self::CMD_ACCOUNTS);
    }

    /**
     * Gravitel API function.
     * Makes phone call from $user to $phone.
     *
     * @param string $phone Whom we phone.
     * @param string $user Who phones. Login or internal phone or direct phone number.
     * @return mixed
     * @throws Exception
     */
    public function makeCall($phone = null, $user = null) {

        if(!$phone) throw new Exception('phone can not be empty');
        if(!$user) throw new Exception('user can not be empty');

        $data = [
            'phone' => $phone,
            'user' => $user
        ];

        return $this->makeRequest(self::CMD_MAKE_CALL, $data);

    }

    /**
     * Gravitel API function.
     * Returns call history by period constant.
     *
     * @param string $type Call type.
     * @param integer $limit Limit count.
     * @param string $period Period.
     * @return mixed
     * @throws Exception
     */
    public function history($type = self::CALL_TYPE_ALL, $limit = null, $period = self::PERIOD_TODAY) {

        $data = [];

        if($type) $data['type'] = $type;
        if($limit) $data['limit'] = $limit;
        if($period) $data['period'] = $period;

        return $this->makeRequest(self::CMD_HISTORY, $data);

    }

    /**
     * Gravitel API function.
     * Returns call history by date range.
     *
     * @param string $type Call type.
     * @param integer $limit Limit count.
     * @param string $start UTC start date.
     * @param string $end UTC end date.
     * @return mixed
     * @throws Exception
     */
    public function historyByDateRange(
        $type = self::CALL_TYPE_ALL,
        $limit = null,
        $start = '20170101T120000Z',
        $end = '20200101T120000Z')
    {

        $data = [];

        if($type) $data['type'] = $type;
        if($limit) $data['limit'] = $limit;
        if($start) $data['start'] = $start;
        if($end) $data['end'] = $end;

        return $this->makeRequest(self::CMD_HISTORY, $data);

    }

    // TODO: fix
    public function subscribeOnCalls($user = null, $status = self::USER_STATUS_ON) {

        if(!$user) throw new Exception('user can not be empty');
        if(!$status) throw new Exception('status can not be empty');

        $data = [
            'user' => $user,
            'status' => $status
        ];

        return $this->makeRequest(self::CMD_ACCOUNTS, $data);

    }

}
