<?php

use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $node derekisbusy\routes\models\Route */

?>

<div class="route-form">
    <div class="row">
        <div class="col-sm-8">
<?= $form->field($node, 'status')->widget(SwitchInput::classname(), [
    'type' => SwitchInput::CHECKBOX,
//    'type' => SwitchInput::RADIO,
    'items' => [
        ['label' => 'Public', 'value' => 0],
        ['label' => 'Protectedw', 'value' => 1],
    ],
    'pluginOptions' => [
        'handleWidth'=>120,
        'size' => 'large',
        'onColor' => 'success',
        'offColor' => 'danger',
        'offText' => 'Public',
        'onText' => 'Protected'
    ]

]);?>
        </div>
    </div>
</div>
