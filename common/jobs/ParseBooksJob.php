<?php

namespace common\jobs;

use common\helpers\NameHelper;
use common\models\Author;
use common\models\Book;
use common\models\Category;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\web\ServerErrorHttpException;

class ParseBooksJob extends BaseObject implements JobInterface
{
    public array $books = [];

    public function execute($queue)
    {
        foreach ($this->books as $book) {
            $imageFileName = $this->getImageFilename($book);

            $this->saveNewBook($book, $imageFileName);
            $this->loadImageFile($book->thumbnailUrl, $imageFileName);
        }
    }

    private function saveNewBook(\stdClass $book, string $imageFilename): void
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = new Book(['scenario' => Book::SCENARIO_CREATE]);
            $model->load([
                'title' => $book?->title,
                'isbn' => $book?->isbn,
                'pageCount' => $book?->pageCount,
                'publishedDate' => date('Y-m-d H:i:s', strtotime($book?->publishedDate?->{'$date'})),
                'thumbnailUrl' => $book?->thumbnailUrl,
                'thumbnailImage' => $imageFilename,
                'shortDescription' => property_exists($book, 'shortDescription') ? htmlentities($book->shortDescription) : null,
                'longDescription' => property_exists($book, 'longDescription') ? htmlentities($book?->longDescription) : null,
                'status' => $book?->status,
            ], '');

            if ($model->save()) {
                echo "book $book->title saved successfully\n";
            } else {
                throw new ServerErrorHttpException("failed to save book $book->title\n");
            }

            $categories = Category::findAll(['name' => $book?->categories]);
            foreach ($categories as $category) {
                $model->link('categories', $category);
            }

            $authors = Author::findAll(['name' => $book?->authors]);
            foreach ($authors as $author) {
                $model->link('authors', $author);
            }

            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            echo $exception->getMessage();
        }
    }

    private function loadImageFile(string $url, string $imageFilename): void
    {
        $fullFileName = \Yii::getAlias('@imagesDir') . '/' . $imageFilename;
        echo (file_put_contents($fullFileName, file_get_contents($url)) === false
            ?"failed to write file $imageFilename\n"
            : "loaded image $fullFileName\n");
    }

    /**
     * @param \stdClass $book
     * @return string
     */
    private function getImageFilename(\stdClass $book): string
    {
        $imageUrl = parse_url($book->thumbnailUrl, PHP_URL_PATH);
        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
        $newFileName = NameHelper::removeSpaces(NameHelper::toLowerCase("{$book->title}_{$book->isbn}")) . ".$extension";
        return $newFileName;
    }
}