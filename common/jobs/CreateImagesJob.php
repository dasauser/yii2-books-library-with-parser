<?php

namespace common\jobs;

use Yii;
use yii\base\BaseObject;
use yii\log\Logger;
use yii\queue\JobInterface;
use yii\web\ServerErrorHttpException;

class CreateImagesJob extends BaseObject implements JobInterface
{
    public $images;
    private Logger $logger;

    public function init()
    {
        parent::init();
        $this->logger = Yii::getLogger();
    }

    public function execute($queue)
    {
        try {
            foreach ($this->images as $imageUrl => $imageName) {
                $this->loadImageFile($imageUrl, $imageName);
            }
        } catch (\Throwable $e) {
            $this->logger->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    private function loadImageFile(string $url, string $imageFilename): void
    {
        $fullFileName = Yii::getAlias('@imagesDir') . '/' . $imageFilename;
        if (file_put_contents($fullFileName, file_get_contents($url)) !== false) {
            return;
        }
        throw new ServerErrorHttpException("failed to write file $imageFilename\n");
    }

}