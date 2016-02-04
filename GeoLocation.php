<?php

namespace geolocation;

use Yii;

use yii\helpers\Url;

class GeoLocation extends \yii\base\Module
{
    public $controllerNamespace = 'geolocation\controllers';
    
    /**
     * Database component to use in the module
     */
    public $db;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->registerTranslations();

    }

    /**
     * Initialization of the i18n translation module
     */
    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['geolocation'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => '@geolocation/messages',

            'fileMap' => [
                'location' => 'geolocation.php',
            ],

        ];
    }
}
