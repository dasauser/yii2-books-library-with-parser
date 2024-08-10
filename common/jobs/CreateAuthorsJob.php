<?php

namespace common\jobs;

use common\helpers\NameHelper;
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
            foreach ($this->getFilteredAuthors() as $author) {
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

    public function getFilteredAuthors(): array
    {
        $existingAuthors = Author::find()
            ->select('name')
            ->indexBy('name')
            ->where(['name' => $this->authors])
            ->column();

        return array_filter($this->authors, function ($author) use ($existingAuthors) {
            return !isset($author, $existingAuthors[$author]) && !empty(NameHelper::removeSpaces($author));
        });
    }
}