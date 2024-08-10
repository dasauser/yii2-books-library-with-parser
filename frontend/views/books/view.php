<?php

use common\models\Book;
use common\models\Category;
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
                'format' => 'image',
                'value' => function (Book $model) {
                    return '/images/' . $model->thumbnailImage;
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
