<?php

namespace derekisbusy\routes\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 */
class RouteAsset extends AssetBundle
{
    public $sourcePath = '@vendor/derekisbusy/yii2-routes/assets';
    public $css = [
    ];
    public $js = [
        'js/routes.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
