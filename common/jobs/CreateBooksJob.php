<?php

namespace common\jobs;

use common\helpers\BookHelper;
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
            $filteredBooks = $this->getFilteredBooks();

            $categoriesModels = $this->getCategoriesModels($filteredBooks);
            $authorsModels = $this->getAuthorsModels($filteredBooks);
            $booksModels = $this->getBooksModels($filteredBooks);

            foreach ($filteredBooks as $book) {
                $cleanTitle = NameHelper::removeSpaces($book?->title);

                if (empty($cleanTitle)) {
                    continue;
                }

                $model = $booksModels[$cleanTitle] ?? $this->createBook($book);

                $this->linkCategories($book?->categories ?? [], $model, $categoriesModels);
                $this->linkAuthors($book?->authors ?? [], $model, $authorsModels);
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->logger->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    public function createBook(stdClass $book): Book
    {
        $model = new Book(['scenario' => Book::SCENARIO_CREATE]);

        $model->load([
            'title' => $book->title,
            'isbn' => BookHelper::getPropertyOrNull($book, 'isbn'),
            'pageCount' => BookHelper::getPropertyOrNull($book, 'pageCount'),
            'thumbnailUrl' => BookHelper::getPropertyOrNull($book, 'thumbnailUrl'),
            'thumbnailImage' => BookHelper::getPropertyOrNull($book, 'thumbnailImage'),
            'shortDescription' => BookHelper::getPropertyOrNull($book, 'shortDescription'),
            'longDescription' => BookHelper::getPropertyOrNull($book, 'longDescription'),
            'status' => BookHelper::getPropertyOrNull($book, 'status'),

            'publishedDate' => BookHelper::isPropertyValid($book, 'publishedDate')
                ? date('Y-m-d H:i:s', strtotime($book->publishedDate?->{'$date'}))
                : null,
        ], '');

        if (!$model->save()) {
            throw new ServerErrorHttpException('can not save author');
        }
        return $model;
    }

    public function linkCategories(array $categories, Book $model, array $categoriesModels): void
    {
        foreach ($categories as $category) {
            if (!isset($categoriesModels[$category])) {
                continue;
            }
            $model->link('categories', $categoriesModels[$category]);
        }
    }

    public function linkAuthors(array $authors, Book $model, array $authorsModels): void
    {
        foreach ($authors as $author) {
            if (!isset($authorsModels[$author])) {
                continue;
            }
            $model->link('authors', $authorsModels[$author]);
        }
    }

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

    public function getCategoriesModels(array $filteredBooks): array
    {
        return Category::find()
            ->indexBy('name')
            ->where(['name' => $this->getAllCategories($filteredBooks)])
            ->all();
    }

    public function getAuthorsModels(array $filteredBooks): array
    {
        return Author::find()
            ->indexBy('name')
            ->where(['name' => $this->getAllAuthors($filteredBooks)])
            ->all();
    }

    public function getBooksModels(array $filteredBooks): array
    {
        [$booksTitles, $bookIsbns] = $this->getAllBooksTitlesAndIsbns($filteredBooks);
        return Book::find()
            ->indexBy('title')
            ->where(['title' => $booksTitles, 'isbn' => $bookIsbns])
            ->all();
    }

    public function getAllCategories(array $filteredBooks): array
    {
        $categories = [];

        foreach ($filteredBooks as $filteredBook) {
            foreach ($filteredBook?->categories ?? [] as $category) {
                $categories[$category] = $category;
            }
        }

        return $categories;
    }

    public function getAllAuthors(array $filteredBooks): array
    {
        $authors = [];

        foreach ($filteredBooks as $filteredBook) {
            foreach ($filteredBook?->authors ?? [] as $author) {
                $authors[$author] = $author;
            }
        }

        return $authors;
    }

    public function getAllBooksTitlesAndIsbns(array $filteredBooks): array
    {
        $titles = [];
        $isbns = [];
        foreach ($filteredBooks as $filteredBook) {
            if (property_exists($filteredBook, 'title') && !empty($filteredBook->title)) {
                $titles[$filteredBook->title] = $filteredBook->title;
            }
            if (property_exists($filteredBook, 'isbn') && !empty($filteredBook->isbn)) {
                $isbns[$filteredBook->isbn] = $filteredBook->isbn;
            }
        }
        return [$titles, $isbns];
    }
}