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
            $count = 0;
            foreach ($parser->parse() as $dataPreparer) {
                if ($dataPreparer === null) {
                    continue;
                }
                $this->createAuthorsJob($dataPreparer);
                $this->createCategoriesJob($dataPreparer);
                $this->createImagesJob($dataPreparer);
                $this->createBooksJob($dataPreparer);

                $count += count($dataPreparer->getBooks());

                echo "Parsed $count" . PHP_EOL;
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

    }

    private function createAuthorsJob(DataPreparer $dataPreparer): void
    {
        $this->queue->push(new CreateAuthorsJob([
            'authors' => $dataPreparer->getAuthors(),
        ]));
    }

    private function createCategoriesJob(DataPreparer $dataPreparer): void
    {
        $this->queue->push(new CreateCategoriesJob([
            'categories' => $dataPreparer->getCategories(),
        ]));
    }

    private function createBooksJob(DataPreparer $dataPreparer): void
    {
        $this->queue->push(new CreateBooksJob([
            'books' => $dataPreparer->getBooks(),
        ]));
    }

    private function createImagesJob(DataPreparer $dataPreparer): void
    {
        $this->queue->push(new CreateImagesJob([
            'images' => $dataPreparer->getImages(),
        ]));
    }
}