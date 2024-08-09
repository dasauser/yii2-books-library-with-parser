<?php

namespace backend\controllers;

use common\helpers\BookHelper;
use common\models\Book;
use common\models\Category;
use frontend\models\BookSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BooksController implements the CRUD actions for Book model.
 */
class BooksController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access-control' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['index', 'view'],
                            'allow' => true,
                            'roles' => ['?', '@'],
                        ],
                        [
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $transaction = Yii::$app->db->beginTransaction();

        $model = new Book(['scenario' => Book::SCENARIO_CREATE]);

        if ($this->request->isPost) {
            try {
                $model->load($this->request->post());
                if (BookHelper::loadPhoto($model)->save()) {
                    $categories = Category::findAll(['id' => $model->categoriesList]);
                    foreach ($categories as $category) {
                        $model->link('categories', $category);
                    }
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } catch (\Exception $exception) {
                $transaction->rollBack();
                throw $exception;
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => $this->getCategoriesList(),
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Book::SCENARIO_UPDATE);

        if ($this->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->load($this->request->post());
                if (BookHelper::loadPhoto($model)->save()) {
                    $model->unlinkAll('categories', true);
                    $categories = Category::findAll(['id' => $model->categoriesList]);
                    foreach ($categories as $category) {
                        $model->link('categories', $category);
                    }
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } catch (\Exception $exception) {
                $transaction->rollBack();
                throw $exception;
            }
        } else {
            $model->categoriesList = ArrayHelper::getColumn($model->categories, 'id');
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => $this->getCategoriesList(),
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return Category[]
     */
    public function getCategoriesList(): array
    {
        return Category::find()
            ->select('name')
            ->indexBy('id')
            ->column();
    }
}
