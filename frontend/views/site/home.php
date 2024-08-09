<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'My Yii Application';
$user = Yii::$app->user;
$isGuest = $user->isGuest;
?>
<div class="site-index">
    <div class="p-5 mb-4 bg-transparent rounded-3">
        <div class="container-fluid py-5 text-center">
            <div class="row">
                <div class="col-lg-4">
                </div>
            </div>
        </div>
    </div>

    <div class="body-content">
    </div>
</div>
