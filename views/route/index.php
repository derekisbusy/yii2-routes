<?php
/* @var $this yii\web\View */

use derekisbusy\routes\models\Route;
use kartik\tree\Module;
use kartik\tree\TreeView;
use kartik\widgets\SwitchInput;
use yii\helpers\Url;
use yii\widgets\Pjax;


$this->title = Yii::t('rbac', 'Routes');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->beginContent('@dektrium/rbac/views/layout.php') ?>

<?php Pjax::begin() ?>
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
        ],
        'toolbar' => [
            TreeView::BTN_CREATE => [
                'icon' => 'plus',
                'options' => ['title' => Yii::t('kvtree', 'Add new'), 'disabled' => true]
            ],
            TreeView::BTN_CREATE_ROOT => [
                'icon' => false ? 'tree' : 'tree-conifer',
                'options' => ['title' => Yii::t('kvtree', 'Add new root')]
            ],
            TreeView::BTN_REMOVE => [
                'icon' => 'trash',
                'options' => ['title' => Yii::t('kvtree', 'Delete'), 'disabled' => true]
            ],
            TreeView::BTN_SEPARATOR,
            TreeView::BTN_MOVE_UP => [
                'icon' => 'arrow-up',
                'options' => ['title' => Yii::t('kvtree', 'Move Up'), 'disabled' => true, 'style' => "display: none"]
            ],
            TreeView::BTN_MOVE_DOWN => [
                'icon' => 'arrow-down',
                'options' => ['title' => Yii::t('kvtree', 'Move Down'), 'disabled' => true, 'style' => "display: none"]
            ],
            TreeView::BTN_MOVE_LEFT => [
                'icon' => 'arrow-left',
                'options' => ['title' => Yii::t('kvtree', 'Move Left'), 'disabled' => true, 'style' => "display: none"]
            ],
            TreeView::BTN_MOVE_RIGHT => [
                'icon' => 'arrow-right',
                'options' => ['title' => Yii::t('kvtree', 'Move Right'), 'disabled' => true, 'style' => "display: none"]
            ],
            TreeView::BTN_SEPARATOR,
            TreeView::BTN_REFRESH => [
                'icon' => 'refresh',
                'options' => ['title' => Yii::t('kvtree', 'Refresh')],
                'url' => Yii::$app->request->url
            ],
        ]
    ]);
    ?>

</div>
<div style="display: none"><?= SwitchInput::widget([
    'name' => 'hidden',
    'tristate' => true
]); ?>
</div>
<?php Pjax::end() ?>

<?php $this->endContent() ?>