<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "leads".
 *
 * @property int $id
 * @property string $name
 * @property int $source_id
 * @property int $status
 * @property string $created_at
 * @property string $created_by
 */
class Leads extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'source_id', 'status', 'created_by'], 'required'],
            [['source_id', 'status'], 'default', 'value' => null],
            [['source_id', 'status'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'created_by'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'source_id' => 'Source ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }
}
