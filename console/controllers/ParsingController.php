<?php

namespace console\controllers;

use common\jobs\ParseBooksJob;
use Exception;
use JsonMachine\Items;
use yii\console\Controller;

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
                    \Yii::$app->queue->push(new ParseBooksJob([
                        'books' => $booksStack,
                    ]));
                    $booksStack = [];
                }
            }

            if (count($booksStack) < 10) {
                \Yii::$app->queue->push(new ParseBooksJob([
                    'books' => $booksStack,
                ]));
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

    }
}