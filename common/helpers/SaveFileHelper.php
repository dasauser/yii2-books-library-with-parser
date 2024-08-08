<?php

namespace common\helpers;

use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class SaveFileHelper
{

    /**
     * Save book image to images directory.
     *
     * @param UploadedFile $file File instance.
     * @param string $newFileName New file name, without extension.
     *
     * @return string Returns created file name (without file path).
     *
     * @throws ServerErrorHttpException Throws exception when can't save book image file.
     */
    public static function saveMainPhoto(UploadedFile $file, string $newFileName, string $ditPath): string
    {
        $filename = "$newFileName.$file->extension";

        if (!$file->saveAs("$ditPath/$filename")) {
            throw new ServerErrorHttpException('failed to save file');
        }

        return $filename;
    }
}