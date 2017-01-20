<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model derekisbusy\rbacroutes\models\Route */

$this->title = Yii::t('rbac', 'Save As New {modelClass}: ', [
    'modelClass' => 'Route',
]). ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Routes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rbac', 'Save As New');
?>
<div class="route-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
