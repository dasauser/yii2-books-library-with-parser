<?php

namespace common\services\BooksParser;

class DataStorage
{
    protected array $authors = [];
    protected array $categories = [];
    protected array $images = [];

    public function addAuthor(string $author): void
    {
        $this->authors[$author] = $author;
    }

    public function addCategory(string $category): void
    {
        $this->categories[$category] = $category;
    }

    public function addImage(string $imageUrl): void
    {
        $this->images[$imageUrl] = $imageUrl;
    }

    public function isAuthorExists(string $author): bool
    {
        return isset($this->authors[$author]);
    }

    public function isCategoryExists(string $category): bool
    {
        return isset($this->categories[$category]);
    }

    public function isImageExists(string $imageUrl): bool
    {
        return isset($this->images[$imageUrl]);
    }
}