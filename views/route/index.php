<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use derekisbusy\rbacroutes\models\Route;
use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\web\View;


$this->title = Yii::t('rbac', 'Routes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-index">

    <h1><?=Html::encode($this->title) ?></h1>

    <?php
    echo TreeView::widget([
        'query' => Route::find()->addOrderBy('root, lft'),
        'headingOptions' => ['label' => 'Route'],
        'rootOptions' => ['label' => '<span class="text-primary">Root</span>'],
        'fontAwesome' => false,
        'isAdmin' => true, // @TODO : put your isAdmin getter here
        'displayValue' => 0,
        'cacheSettings' => ['enableCache' => true],
        'nodeAddlViews' => [
            Module::VIEW_PART_2 => '@vendor/derekisbusy/yii2-rbac-routes/views/route/_form'
        ]
    ]);
    ?>

</div>
