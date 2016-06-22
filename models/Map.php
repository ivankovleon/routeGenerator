<?php

namespace app\models;
use yii\web\UploadedFile;
use yii\validators\ImageValidator;
use yii\base\InvalidParamException;
use Yii;

/**
 * This is the model class for table "maps".
 *
 * @property integer $id
 * @property integer $author_id
 * @property string $name
 * @property integer $status
 * @property integer $control_points_number
 * @property integer $root_route_length
 * @property integer $map_scale
 * @property string $file_name
 *
 * @property User $author
 */
class Map extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $file;

    public static function tableName()
    {
        return 'maps';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author_id', 'name'], 'required'],
            [['author_id', 'status', 'control_points_number', 'root_route_length', 'map_scale'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['file_name'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['file'], 'safe'],
            ['file', 'image', 'extensions'=>'jpg, gif, png',
                'minWidth' => 1, 'maxWidth' => 3000,
                'minHeight' => 1, 'maxHeight' => 2000],
            //['file', 'file', 'maxSize'=>'100000', 'message' => '������ ����� �� ������ ��������� 100000'],,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'name' => 'Название карты',
            'status' => 'Статус',
            'control_points_number' => 'Количество контрольных пунктов',
            'root_route_length' => 'Длина маршрута ',
            'map_scale' => 'Маштаб карты',
            'file_name' => 'Название файла карты',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id,]);
    }
}
