<?php

namespace common\jobs;

use common\models\Author;
use yii\base\BaseObject;
use yii\log\Logger;
use yii\queue\JobInterface;
use yii\web\ServerErrorHttpException;

class CreateAuthorsJob extends BaseObject implements JobInterface
{
    public $authors;
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
            foreach ($this->authors as $author) {
                $model = new Author(['name' => $author]);
                if (!$model->save()) {
                    throw new ServerErrorHttpException('can not save author');
                }
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->logger->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
    }
}