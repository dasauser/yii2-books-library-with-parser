<?php

namespace common\jobs;

use common\helpers\NameHelper;
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
            foreach ($this->getFilteredCategories() as $category) {
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
    }

    public function getFilteredCategories()
    {
        $existingCategories = Category::find()
            ->select('name')
            ->indexBy('name')
            ->where(['name' => $this->categories])
            ->column();

        return array_filter($this->categories, function ($category) use ($existingCategories) {
            return !isset($category, $existingCategories[$category]) && !empty(NameHelper::removeSpaces($category));
        });
    }
}