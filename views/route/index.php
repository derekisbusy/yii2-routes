<?php
/* @var $this yii\web\View */

use kartik\widgets\SwitchInput;
use derekisbusy\routes\models\Route;
use kartik\tree\Module;
use kartik\tree\TreeView;
use yii\helpers\Url;



$this->title = Yii::t('rbac', 'Routes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-index">

    <?php
    echo TreeView::widget([
        'query' => Route::find()->addOrderBy('root, lft'),
        'headingOptions' => ['label' => 'Route'],
        'rootOptions' => ['label' => '<span class="text-primary">Apps</span>'],
        'fontAwesome' => false,
        'isAdmin' => false, // @TODO : put your isAdmin getter here
        'displayValue' => 0,
        'cacheSettings' => ['enableCache' => true],
        'nodeActions' => [
            Module::NODE_MANAGE => Url::to(['/routes/route/manage']),
            Module::NODE_SAVE => Url::to(['/routes/route/save']),
            Module::NODE_REMOVE => Url::to(['/routes/route/remove']),
            Module::NODE_MOVE => Url::to(['/routes/route/move']),
        ],
        'nodeView' => '@vendor/derekisbusy/yii2-routes/views/route/_form',
        'nodeAddlViews' => [
            Module::VIEW_PART_2 => '@vendor/derekisbusy/yii2-routes/views/route/_assigned'
        ]
    ]);
    ?>

</div>
<div style="display: none"><?= SwitchInput::widget([
    'name' => 'hidden',
    'tristate' => true
]); ?>
</div>