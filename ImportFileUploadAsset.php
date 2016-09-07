<?php

/**
 * Created by PhpStorm.
 * User: zhengpenglin
 * Date: 2016/7/29
 * Time: 15:05
 */

namespace zplzpl\uploader;

use yii\web\AssetBundle;

class ImportFileUploadAsset extends AssetBundle
{

    public $css = [
        'webuploader.css'
    ];

    public $js = [
        'webuploader.min.js',
    ];

    public $depends = [
        'backend\assets\CommonAsset'
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
}