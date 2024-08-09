<?php

namespace common\jobs;

use common\helpers\NameHelper;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class ParseBooksJob extends BaseObject implements JobInterface
{
    public array $books = [];

    public function execute($queue)
    {
        foreach ($this->books as $book) {
            $this->saveNewBook($book);
            $this->loadImageFile($book);
        }
    }

    private function saveNewBook(\stdClass $book)
    {
        echo "saved new book {$book->title}\n";
    }

    private function loadImageFile(\stdClass $book): void
    {
        $imageUrl = parse_url($book->thumbnailUrl, PHP_URL_PATH);
        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);

        $fileData = file_get_contents($book->thumbnailUrl);

        $newFileName = NameHelper::removeSpaces(NameHelper::toLowerCase("{$book->title}_{$book->isbn}")).".$extension";

        $fullFileName = \Yii::getAlias('@imagesDir') . '/' . $newFileName;

        if (false === file_put_contents($fullFileName, $fileData)) {
            echo "failed to write file $newFileName\n";
            return;
        }

        echo "loaded image $fullFileName\n";
    }
}