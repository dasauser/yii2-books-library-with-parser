<?php

namespace common\jobs;

use common\helpers\NameHelper;
use common\models\Author;
use common\models\Book;
use common\models\Category;
use stdClass;
use yii\base\BaseObject;
use yii\log\Logger;
use yii\queue\JobInterface;
use yii\web\ServerErrorHttpException;

class CreateBooksJob extends BaseObject implements JobInterface
{
    public $books;
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
            foreach ($this->getFilteredBooks() as $book) {
                if (empty(NameHelper::removeSpaces($book?->title))) {
                    continue;
                }

                $model = Book::find()
                    ->where(['title' => $book?->title, 'isbn' => $book?->isbn])
                    ->one();

                if ($model === null) {
                    $model = $this->createBook($book);
                }

                $this->linkCategories($book, $model);
                $this->linkAuthors($book, $model);
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->logger->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param mixed $book
     * @return Book
     * @throws ServerErrorHttpException
     */
    public function createBook(stdClass $book): Book
    {
        $model = new Book(['scenario' => Book::SCENARIO_CREATE]);

        $model->load([
            'title' => $book?->title,
            'isbn' => $book?->isbn,
            'pageCount' => $book?->pageCount,
            'publishedDate' => date('Y-m-d H:i:s', strtotime($book?->publishedDate?->{'$date'})),
            'thumbnailUrl' => $book?->thumbnailUrl,
            'thumbnailImage' => $book?->thumbnailImage,
            'shortDescription' => property_exists($book, 'shortDescription')
                ? htmlentities($book->shortDescription) :
                null,
            'longDescription' => property_exists($book, 'longDescription')
                ? htmlentities($book?->longDescription)
                : null,
            'status' => $book?->status,
        ], '');

        if (!$model->save()) {
            throw new ServerErrorHttpException('can not save author');
        }
        return $model;
    }

    /**
     * @param mixed $book
     * @param Book $model
     * @return void
     */
    public function linkCategories(stdClass $book, Book $model): void
    {
        $categories = Category::findAll(['name' => $book?->categories]);
        foreach ($categories as $category) {
            $model->link('categories', $category);
        }
    }

    /**
     * @param mixed $book
     * @param Book $model
     * @return void
     */
    public function linkAuthors(stdClass $book, Book $model): void
    {
        $authors = Author::findAll(['name' => $book?->authors]);
        foreach ($authors as $author) {
            $model->link('authors', $author);
        }
    }

    /**
     * @return mixed
     */
    public function getFilteredBooks()
    {
        $bookTitles = array_map(function ($book) {
            return $book?->title;
        }, $this->books);

        $existingBooks = Book::find()
            ->select(['title'])
            ->indexBy('title')
            ->where(['title' => $bookTitles])
            ->column();

        return array_filter($this->books, function ($book) use ($existingBooks) {
            return !isset($existingBooks[$book->title]) && !empty(NameHelper::removeSpaces($book->title));
        });
    }
}