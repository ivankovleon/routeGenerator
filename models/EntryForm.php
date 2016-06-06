<?php
/**
 * Created by PhpStorm.
 * User: Leo
 * Date: 24.04.2016
 * Time: 6:55
 */

namespace app\models;

use yii;
use yii\base\Model;

class EntryForm extends Model
{
    public $name;
    public $email;

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            ['email', 'email'],
        ];
    }
}