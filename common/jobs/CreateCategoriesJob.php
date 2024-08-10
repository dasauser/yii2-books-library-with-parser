<?php

namespace common\jobs;

use common\models\Category;
use yii\base\BaseObject;
use yii\log\Logger;
use yii\queue\JobInterface;
use yii\web\ServerErrorHttpException;

class CreateCategoriesJob extends BaseObject implements JobInterface
{
    public $categories;
    private Logger $logger;

    public function init()
    {
        parent::init();
        $this->logger = \Yii::getLogger();
    }

    public function execute($queue)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($this->categories as $category) {
                $model = new Category(['name' => $category]);
                if (!$model->save()) {
                    throw new ServerErrorHttpException('can not create category');
                }
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->logger->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
    }}