<?php

use common\models\Book;
use common\models\Category;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Book $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'isbn',
            [
                'attribute' => 'categories',
                'format' => 'raw',
                'value' => function (Book $model) {
                    return implode(', ', array_map(function (Category $category) {
                        return Html::a($category->name, ['/categories/view', 'id' => $category->id]);
                    }, $model->categories));
                }
            ],
            'pageCount',
            'publishedDate',
            'thumbnailUrl',
            [
                'attribute' => 'thumbnailImage',
                'format' => 'html',
                'value' => function (Book $model) {
                    return Html::img('/images/' . $model->thumbnailImage);
                }
            ],
            'shortDescription:ntext',
            'longDescription:ntext',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
