<?php

namespace ryzhak\gravitel\controllers;

use ryzhak\gravitel\models\InputRequest;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UnauthorizedHttpException;

class ReceiveController extends \yii\rest\Controller
{

    public function actionIndex() {

        if(!\Yii::$app->request->isPost) {
            throw new MethodNotAllowedHttpException();
        }

        $mInputRequest = new InputRequest();
        $mInputRequest->params = \Yii::$app->request->post();

        if(!$mInputRequest->validateToken($this->module->crmToken)) {
            throw new UnauthorizedHttpException('Invalid crm_token');
        }

        if(!$mInputRequest->validateCmd()) {
            throw new BadRequestHttpException('Invalid cmd');
        }

        $mInputRequest->proceedEvents();

    }

}
