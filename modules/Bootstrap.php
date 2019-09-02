<?php

namespace app\modules;

use Yii;
use yii\base\BootstrapInterface;

use app\models\Leads;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
         $container = \Yii::$container;
         $container->setSingleton('app\services\RestapiService','app\services\RestapiService',[
         	new Leads(),
         	Yii::$app->user,
         	json_decode(Yii::$app->request->getRawBody(), true),
         ]);         
    }
}