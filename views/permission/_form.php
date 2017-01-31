<?php

/**
 * @var $this  yii\web\View
 * @var $model derekibusy\routes\models\Route
 */

?>
<div class="form-group">
<?= $form->field($model, 'route_ids[]')->widget(\derekisbusy\routes\widgets\TreeViewInput::className(), [
    'value' => true, // preselected values
    'selected' => $model->getRouteIds(),
    'query' => \derekisbusy\routes\models\Route::find()->addOrderBy('root, lft'),
    'headingOptions' => ['label' => 'Routes'],
    'rootOptions' => ['label' => '<span class="text-primary">Apps</span>'],
    'fontAwesome' => false,
    'asDropdown' => false,
    'multiple' => true,
    'options' => ['disabled' => false]
])->label(false);
?>
</div>
