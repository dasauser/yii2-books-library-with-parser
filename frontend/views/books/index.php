<?php

use common\models\Book;
use common\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
//            'isbn',
//            'pageCount',
            'publishedDate:date',
            [
                'attribute' => 'categories',
                'format' => 'raw',
                'value' => function (Book $model) {
                    return implode(', ', array_map(function (Category $category) {
                        return Html::a($category->name, ['/categories/view', 'id' => $category->id]);
                    }, $model->categories));
                }
            ],
            //'thumbnailUrl',
            [
                'attribute' => 'thumbnailImage',
                'format' => 'image',
                'value' => function (Book $model) {
                    return '/images/'.$model->thumbnailImage;
                },
            ],
            //'shortDescription:ntext',
            //'longDescription:ntext',
            //'status',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'template' => '{view}',
                'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                    return Url::to(['books/' . $action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>
