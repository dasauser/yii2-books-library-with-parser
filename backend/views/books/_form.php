<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $categories */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'categoriesList')->widget(Select2::class, [
        'data' => $categories,
        'options' => [
            'placeholder' => 'Select categories',
            'multiple' => true
        ],
    ]) ?>

    <?= $form->field($model, 'pageCount')->textInput() ?>

    <?= $form->field($model, 'publishedDate')->textInput() ?>

    <?= $form->field($model, 'thumbnailUrl')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thumbnailImage')->fileInput() ?>

    <?= $form->field($model, 'shortDescription')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'longDescription')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
