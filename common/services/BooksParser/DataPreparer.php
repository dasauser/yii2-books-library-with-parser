<?php

namespace common\services\BooksParser;

use common\helpers\NameHelper;
use stdClass;

class DataPreparer
{
    protected array $images;

    protected array $categories;

    protected array $authors;
    
    protected array $books;
    
    public function __construct(protected DataStorage $storage)
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
        if (!$this->isPropertyValid($book, 'thumbnailUrl')) {
            return null;
        }

        $imageUrl = parse_url($book->thumbnailUrl, PHP_URL_PATH);

        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);

        $lowered = NameHelper::toLowerCase("{$book->title}_{$book->isbn}");

        return NameHelper::removeSpaces($lowered).".$extension";
    }

    protected function setImageName(stdClass $book, ?string $imageName)
    {
        $book->thumbnailImage = $imageName;
    }

    protected function prepareAuthors(stdClass $book): void
    {
        if (!$this->isPropertyValid($book, 'authors')) {
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
        if (!$this->isPropertyValid($book, 'categories')) {
            return;
        }
        foreach ($book->categories as $category) {
            if (!$this->storage->isCategoryExists($category)) {
                $this->storage->addCategory($category);
                $this->categories[$category] = $category;
            }
        }
    }

    protected function prepareImage(?string $imageName, stdClass $book): void
    {
        if (!$this->isPropertyValid($book, 'thumbnailUrl')) {
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
        $this->booksQueue[] = $book;
    }

    protected function isPropertyValid(stdClass $book, string $property): bool
    {
        return property_exists($book, $property) && !empty($book?->{$property});
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function getBooks(): array
    {
        return $this->books;
    }
}