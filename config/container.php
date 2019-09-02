<?php 

Yii::$container->setSingleton('apiLeadsService',
	[
	    'class' => 'app\services\RestapiService',
	], 
	[
		'Leads'
	]
);
