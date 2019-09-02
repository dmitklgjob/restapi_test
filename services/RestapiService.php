<?php

namespace app\services;

use Yii;
use app\models\Leads;
use yii\data\ActiveDataProvider;

class RestapiService {

    private $Model = null;
    private $items_per_page = 2;
    private $User = null;
    private $arrayBody = [];

    public function __construct($Model, $User, $arrayBody){
    	$this->User = $User;
        $this->Model = $Model; 
        $this->arrayBody = $arrayBody; 
    }

    public function checkJson() {
    	if (!$this->arrayBody) {
    		throw new \yii\web\HttpException(422 ,'Decode json error');
    	}
    }

    public function create() {

    	$this->checkJson();
    	$Item = $this->Model;

    	$Item->name = $this->arrayBody['name'] ?? '';
    	$Item->source_id = $this->arrayBody['source_id'] ?? '';
    	$Item->status = $this->arrayBody['status'] ?? '';
    	$Item->created_by = $this->User->identity->username;



    	if (!$Item->save()) {

    		$message = '';
    		$message_array = [];
    		foreach ($Item->errors as $key => $val) {
    			array_push($message_array, $val[0]);
    		}

    		$message = implode(', ',$message_array);

    		throw new \yii\web\HttpException(422 ,'Create item error: ' . $message);
    	} else {
    		return [
    			'success' => true,
    			'data' => $Item,
    		];
    	}

    }

    public function items() {

    	$request = Yii::$app->request;
    	$status_filter = $request->get('status', null);
    	$source_id_filter = $request->get('source_id', null);

    	$Query = $this->Model::find();

    	if ($status_filter) {
    		$Query->andWhere(['status' => $status_filter]);
    	}

    	if ($source_id_filter) {
    		$Query->andWhere(['source_id' => $source_id_filter]);
    	}


        $provider = new ActiveDataProvider([
            'query' => $Query,
            'pagination' => [
                'pageSize' => $this->items_per_page,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);


    	$count = $provider->getTotalCount();

    	$Items = $provider->getModels();

    	return ['data' => $Items, 'count' => $count];
    }

}