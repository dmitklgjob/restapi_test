<?php

namespace app\controllers;

use Yii;
use app\models\User;
use \yii\filters\auth\HttpBasicAuth;
use yii\filters\AccessControl;

class RestapiController extends \yii\web\Controller
{
	public $enableCsrfValidation = false;

	public function init()
	{
	    parent::init();
	    \Yii::$app->user->enableSession = false;
	    Yii::$app->errorHandler->errorAction = 'restapi/error';
	}

	public function behaviors()
	{
	    return [
	        'authenticator' => [
	            'class' => HttpBasicAuth::className(),
	            'except' => ['error'],
	            'auth' => function ($username, $password) {
                	$user = User::findByUsername($username);
	                if ($user && $user->validatePassword($password)) {
	                	return $user;
	                }

                 	return null;
 	            },
 	        ],
	    ];
	}

	public function actionLeadsCreate()
    {
    	$service = Yii::$container->get('apiLeadsService');
    	$result = $service->create();
    	return $this->asJson($result);
    }

	public function actionLeads()
    {
    	$service = Yii::$container->get('apiLeadsService');
    	$result = $service->items();
    	return $this->asJson(['success' => true, 'count' => $result['count'], 'pages' => $result['pages'],'data' => $result['data']]);
    }

	public function actionError()
	{
	    $exception = Yii::$app->errorHandler->exception;
	    if ($exception !== null) {
	    	return $this->asJson(['success' => false, 'statusCode' => $exception->statusCode,'message' => $exception->getMessage()]);
	    }
	}
}
