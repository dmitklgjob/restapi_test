<?php

namespace app\services;

use Yii;
use app\models\Leads;

class RestapiService {

    private $Model = '';
    private $items_per_page = 2;
    private $User = null;
    private $arrayBody = [];

    public function __construct($Model){
    	$this->User = Yii::$app->user->identity;
        $this->Model = 'app\\models\\' . $Model; 
        $this->arrayBody = json_decode(Yii::$app->request->getRawBody(), true);    
    }

    public function checkJson() {
    	if (!$this->arrayBody) {
    		throw new \yii\web\HttpException(422 ,'Decode json error');
    	}
    }

    public function create() {

    	$this->checkJson();
    	$Item = new Leads();

    	$Item->name = $this->arrayBody['name'] ?? '';
    	$Item->source_id = $this->arrayBody['source_id'] ?? '';
    	$Item->status = $this->arrayBody['status'] ?? '';
    	$Item->created_by = $this->User->username;



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
    	$page_number = $request->get('page', 1);
    	$status_filter = $request->get('status', null);
    	$source_id_filter = $request->get('source_id', null);

    	$Query = Leads::find();
    	$Query->orderBy('id');

    	if ($status_filter) {
    		$Query->andWhere(['status' => $status_filter]);
    	}

    	if ($source_id_filter) {
    		$Query->andWhere(['source_id' => $source_id_filter]);
    	}

    	$count = $Query->count();

    	$pages = $count > $this->items_per_page ? ceil($count/$this->items_per_page) : 1;

    	if ($page_number > $pages) {
    		$page_number = $pages;
    	}

    	$Query->offset(($page_number-1)*$this->items_per_page)->limit($this->items_per_page);

    	$Items = $Query->asArray()->all();

    	return ['data' => $Items, 'count' => $count, 'pages' => $pages];
    }

}