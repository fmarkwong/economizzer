<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

class Cashbook extends \yii\db\ActiveRecord
{
    public $file;
    public $filename;

    public static function tableName()
    {
        return 'cashbook';
    }

    public function rules()
    {
        return [
            [['category_id', 'type_id', 'budgeted_value', 'date'], 'required'],
            [['category_id', 'type_id', 'user_id', 'is_pending'], 'integer'],
            [['value', 'budgeted_value'], 'number'],
            [['file'], 'file', 'extensions'=>'jpg, png, pdf', 'maxSize' => 1024 * 1024 * 2],
            [['date', 'attachment', 'file', 'filename', 'inc_datetime', 'edit_datetime'], 'safe'],
            [['description'], 'string', 'max' => 100],
            [['attachment'], 'string', 'max' => 255],
        ];
    }

    public function getImageFile()
    {
        return isset($this->attachment) ? Yii::$app->params['uploadPath'] . $this->user_id ."/". $this->attachment : null;
    }
    public function getImageUrl()
    {
        // return a default image placeholder if your source attachment is not found
        $attachment = isset($this->attachment) ? $this->attachment : 'default-attachment.png';
        return Yii::$app->params['uploadUrl'] . $attachment;
    }
    public function uploadImage() {
        // get the uploaded file instance. for multiple file uploads
        // the following data will return an array (you may need to use
        // getInstances method)
        $file = UploadedFile::getInstance($this, 'file');
 
        // if no image was uploaded abort the upload
        if (empty($file)) {
            return false;
        }
 
        // store the source file name
        $this->filename = $file->name;
        $ext = end((explode(".", $file->name)));
 
        // generate a unique file name
        $this->attachment = Yii::$app->security->generateRandomString().".{$ext}";
 
        // the uploaded image instance
        return $file;
    }
    public function deleteImage() {
        $file = $this->getImageFile();
 
        // check if file exists on server
        if (empty($file) || !file_exists($file)) {
            return false;
        }
 
        // check if uploaded file can be deleted on server
        if (!unlink($file)) {
            return false;
        }
 
        // if deletion successful, reset your file attributes
        $this->attachment = null;
        $this->filename = null;
 
        return true;
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Sub Category'),
            'parent_category_id' => Yii::t('app', 'Parent Category'),
            'type_id' => Yii::t('app', 'Type'),
            'value' => Yii::t('app', 'Value'),
            'description' => Yii::t('app', 'Description'),
            'date' => Yii::t('app', 'Date'),
            'is_pending' => Yii::t('app', 'Pending'),
            'attachment' => Yii::t('app', 'Attach'),
            'inc_datetime' => Yii::t('app', 'Created'),
            'edit_datetime' => Yii::t('app', 'Changed'),
            'file' => Yii::t('app', 'File'),
            'filename' => Yii::t('app', 'Filename'),
        ];
    }

    public function getType()
    {
        return $this->hasOne(Type::className(), ['id_type' => 'type_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id_category' => 'category_id']);
    }

    public function getParentCategory()
    {
        return Category::findOne($this->getCategory()->one()->parent_id) ;
    }
    

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function pageTotal($provider, $value)
    {
        $total=0;
        foreach($provider as $item){
            $total+=$item[$value];
        }
        return $total;
    }

    public static function footerColor($value)
    {
        $green = "#18bc9c";
        $red = "#e74c3c";
        $color = $value >= 0 ? $green : $red;
        if ($value == 0) $color = "#000000";
        return $color;
    }

    public static function getTotalBudgetedValue()
    {
        return (self::findBySql('SELECT SUM(budgeted_value) as total_budgeted_value FROM cashbook WHERE user_id = :user_id', [':user_id' => Yii::$app->user->id])->asArray()->one());

    }

    
}
