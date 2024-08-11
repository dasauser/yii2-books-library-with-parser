<?php

namespace common\helpers;

use common\models\Book;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class BookHelper
{
    /**
     * Save book image file in provided model.
     *
     * @param Book $book
     * @return Book
     * @throws ServerErrorHttpException
     */
    public static function loadPhoto(Book $book): Book
    {
        $file = UploadedFile::getInstance($book, 'thumbnailImage');

        if ($file !== null) {
            $postfix = strlen($book->isbn > 0) ? $book->isbn : time();
            $ditPath = \Yii::getAlias('@imagesDir');
            $newFileName = NameHelper::toLowerCase(NameHelper::removeSpaces($book->title)) . "_" . $postfix;
            $book->thumbnailImage = SaveFileHelper::saveMainPhoto($file, $newFileName, $ditPath);
        }

        return $book;
    }

    public static function isPropertyValid(object $book, string $property): bool
    {
        return property_exists($book, $property) && !empty($book?->{$property});
    }
}