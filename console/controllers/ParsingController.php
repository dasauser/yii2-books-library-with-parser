<?php

namespace console\controllers;

use common\jobs\ParseBooksJob;
use Exception;
use JsonMachine\Items;
use yii\console\Controller;
use yii\queue\Queue;

class ParsingController extends Controller
{
    public function actionIndex(string $filePath)
    {
        try {
            $fruits = Items::fromFile($filePath);

            $booksStack = [];

            foreach ($fruits as $bookObject) {
                $booksStack[] = $bookObject;

                if (count($booksStack) === 10) {
                    $this->createParseBooksJob($booksStack);
                    $booksStack = [];
                }
            }

            if (count($booksStack) < 10) {
                $this->createParseBooksJob($booksStack);
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

    }

    /**
     * @param array $booksStack
     * @return void
     */
    public function createParseBooksJob(array $booksStack): void
    {
        \Yii::$app->queue->on(Queue::EVENT_AFTER_ERROR, function ($event) {
            echo 'Error!';
        });

        \Yii::$app->queue->push(new ParseBooksJob([
            'books' => $booksStack,
        ]));

    }
}