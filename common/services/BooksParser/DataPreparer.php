<?php

namespace common\services\BooksParser;

use common\helpers\NameHelper;
use stdClass;

class DataPreparer
{

    public function __construct(
        protected DataStorage $storage,
        protected array       $images = [],
        protected array       $categories = [],
        protected array       $authors = [],
        protected array       $books = [],
    )
    {
    }

    public function prepare(stdClass $book): void
    {
        $imageName = $this->createImageName($book);
        $this->setImageName($book, $imageName);
        $this->prepareAuthors($book);
        $this->prepareCategories($book);
        $this->prepareImage($imageName, $book);
        $this->prepareBook($book);
    }

    protected function createImageName(stdClass $book): ?string
    {
        if (!static::isPropertyValid($book, 'thumbnailUrl')) {
            return null;
        }

        $imageUrl = parse_url($book->thumbnailUrl, PHP_URL_PATH);

        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);

        $postfix = static::isPropertyValid($book, 'isbn') ? $book->isbn : time();

        $lowered = NameHelper::toLowerCase("{$book->title}_".$postfix);

        return NameHelper::removeSpaces($lowered) . ".$extension";
    }

    protected function setImageName(stdClass $book, ?string $imageName)
    {
        $book->thumbnailImage = $imageName;
    }

    protected function prepareAuthors(stdClass $book): void
    {
        if (!static::isPropertyValid($book, 'authors')) {
            return;
        }
        foreach ($book->authors as $author) {
            if (!$this->storage->isAuthorExists($author)) {
                $this->storage->addAuthor($author);
                $this->authors[$author] = $author;
            }
        }
    }

    protected function prepareCategories(stdClass $book): void
    {
        if (!static::isPropertyValid($book, 'categories')) {
            return;
        }
        $categories = empty($book->categories) ? ['New'] : $book->categories;
        foreach ($categories as $category) {
            if (!$this->storage->isCategoryExists($category)) {
                $this->storage->addCategory($category);
                $this->categories[$category] = $category;
            }
        }
    }

    protected function prepareImage(?string $imageName, stdClass $book): void
    {
        if (!static::isPropertyValid($book, 'thumbnailUrl')) {
            return;
        }

        if ($this->storage->isImageExists($book->thumbnailUrl)) {
            return;
        }

        $this->storage->addImage($book->thumbnailUrl);
        $this->images[$book->thumbnailUrl] = $imageName;
    }

    protected function prepareBook(stdClass $book): void
    {
        $this->books[] = $book;
    }

    protected static function isPropertyValid(stdClass $book, string $property): bool
    {
        return property_exists($book, $property) && !empty($book?->{$property});
    }

    public function getImages(): array
    {
        return array_unique($this->images);
    }

    public function getCategories(): array
    {
        return array_unique($this->categories);
    }

    public function getAuthors(): array
    {
        return array_unique($this->authors);
    }

    public function getBooks(): array
    {
        return $this->books;
    }
}