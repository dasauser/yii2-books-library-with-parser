<?php

namespace common\services\BooksParser;

use common\helpers\BookHelper;
use common\helpers\NameHelper;
use stdClass;

class DataCollection
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

    public function add(stdClass $book): void
    {
        $imageName = $this->createImageName($book);

        $this->setImageName($book, $imageName);

        $this->addAuthors($book);
        $this->addCategories($book);
        $this->addImage($imageName, $book);
        $this->addBook($book);
    }

    protected function createImageName(stdClass $book): ?string
    {
        if (!BookHelper::isPropertyValid($book, 'thumbnailUrl')) {
            return null;
        }

        $imageUrl = parse_url($book->thumbnailUrl, PHP_URL_PATH);

        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);

        $postfix = BookHelper::getPropertyOrNull($book, 'isbn') ?? time();

        $lowered = NameHelper::toLowerCase("{$book->title}_".$postfix);

        return NameHelper::removeSpaces($lowered) . ".$extension";
    }

    protected function setImageName(stdClass $book, ?string $imageName)
    {
        $book->thumbnailImage = $imageName;
    }

    protected function addAuthors(stdClass $book): void
    {
        $book->authors = BookHelper::getPropertyOrNull($book, 'authors') ?? [];

        foreach ($book->authors as $author) {
            if ($this->storage->isAuthorExists($author)) {
                continue;
            }

            $this->storage->addAuthor($author);
            $this->authors[$author] = $author;
        }
    }

    protected function addCategories(stdClass $book): void
    {
        $book->categories = BookHelper::getPropertyOrNull($book, 'categories') ?? ['New'];

        foreach ($book->categories as $category) {
            if ($this->storage->isCategoryExists($category)) {
                continue;
            }

            $this->storage->addCategory($category);
            $this->categories[$category] = $category;
        }
    }

    protected function addImage(?string $imageName, stdClass $book): void
    {
        if (!BookHelper::isPropertyValid($book, 'thumbnailUrl')) {
            $book->thumbnailUrl = null;
            return;
        }

        if ($this->storage->isImageExists($book->thumbnailUrl)) {
            return;
        }

        $this->storage->addImage($book->thumbnailUrl);
        $this->images[$book->thumbnailUrl] = $imageName;
    }

    protected function addBook(stdClass $book): void
    {
        $this->books[] = $book;
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