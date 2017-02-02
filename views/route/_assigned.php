<?php

use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\rbac\Item;
use derekisbusy\routes\models\Route;
/* @var $this yii\web\View */
/* @var $node derekisbusy\routes\models\Route */




$roles = [];
if ($node->status == Route::STATUS_PROTECTED) {
?>
<h4><?= Yii::t('routes', "Permissions") ?></h4>
<?php
//var_dump($node->permissions);
    if(empty($node->permissions)) {
?>
    <div class="alert alert-warning show">
      <strong><?= Yii::t('routes', "No permissions found!") ?></strong> <?= Yii::t('routes', "The route is protected but doesn't have any permissions associated with it.") ?>
    </div>
<?php
    } else {
        displayPermissionsRecursive($node->permissions, $roles);
    }
?>
<h4>Roles</h4>
<?php

    if (!empty($roles)) {
        echo "<ul>";
        foreach ($roles as $role) {
            echo "<li>{$role->name}</li>";
        }
        echo "<ul>";
    } else {
        ?>
        <div class="alert alert-warning show">
          <strong><?= Yii::t('routes', "No roles found!") ?></strong> <?= Yii::t('routes', "The route is not accessible to any roles.") ?>
        </div>
    <?php
    }
} elseif (!empty($node->permissions)) {
        ?>
        <div class="alert alert-notice show">
          <strong><?= Yii::t('routes', "Permissions found!") ?></strong> <?= Yii::t('routes', "The route is marked public yet has perimssions assigned. You should mark the route as protected or remove the permissions.") ?>
        </div>
    <?php
}







function displayPermissionsRecursive($items, &$roles)
{
    if(empty($items)) {
        return;
    }
    $i = null;
    foreach($items as $item) {
        if ($item->type == Item::TYPE_ROLE) {
            $roles[] = $item;
        } else {
            if (!$i) {
                echo "<ul>";
                $i = 1;
            }
            echo "<li>{$item->name}</li>";
        }
        displayPermissionsRecursive($item->parents, $roles);
    }
    if ($i) {
        echo "</ul>";
    }
}