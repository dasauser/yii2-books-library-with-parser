<?php

namespace console\controllers;

use common\jobs\CreateAuthorsJob;
use common\jobs\CreateBooksJob;
use common\jobs\CreateCategoriesJob;
use common\jobs\CreateImagesJob;
use common\services\BooksParser\DataPreparer;
use common\services\BooksParser\Parser;
use Exception;
use Yii;
use yii\console\Controller;
use yii\queue\Queue;

class ParsingController extends Controller
{
    private Queue $queue;

    public function init()
    {
        parent::init();
        $this->queue = Yii::$app->queue;
    }

    public function actionStart(string $filePath)
    {
        try {
            $parser = new Parser($filePath);
            foreach ($parser->parse() as $dataPreparer) {
                if ($dataPreparer === null) {
                    continue;
                }
                $this->createAuthorsJob($dataPreparer);
                $this->createCategoriesJob($dataPreparer);
                $this->createImagesJob($dataPreparer);
                $this->createBooksJob($dataPreparer);

                echo "Parsed {$parser->getParsedBooksCount()}" . PHP_EOL;
            }
        } catch (Exception $e) {
            throw $e;
        }

    }

    private function createAuthorsJob(DataPreparer $dataPreparer): void
    {
        $authors = $dataPreparer->getAuthors();
        if (!empty($authors)) {
            $this->queue->push(new CreateAuthorsJob([
                'authors' => $authors,
            ]));
        }
    }

    private function createCategoriesJob(DataPreparer $dataPreparer): void
    {
        $categories = $dataPreparer->getCategories();
        if (!empty($categories)) {
            $this->queue->push(new CreateCategoriesJob([
                'categories' => $categories,
            ]));
        }
    }

    private function createBooksJob(DataPreparer $dataPreparer): void
    {
        $books = $dataPreparer->getBooks();
        if (!empty($books)) {
            $this->queue->push(new CreateBooksJob([
                'books' => $books,
            ]));
        }
    }

    private function createImagesJob(DataPreparer $dataPreparer): void
    {
        $images = $dataPreparer->getImages();
        if (!empty($images)) {
            $this->queue->push(new CreateImagesJob([
                'images' => $images,
            ]));
        }
    }
}