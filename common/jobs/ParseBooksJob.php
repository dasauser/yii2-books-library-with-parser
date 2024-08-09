<?php

namespace common\jobs;

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

    private function loadImageFile(\stdClass $book)
    {
        echo "loaded image {$book->thumbnailUrl}\n";
    }
}