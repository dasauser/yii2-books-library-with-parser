<?php

namespace common\services\BooksParser;

use Generator;
use JsonMachine\Items;
use stdClass;

class Parser
{
    protected Items $books;

    protected int $counter = 0;
    protected DataStorage $dataStorage;

    public function __construct(protected string $fileToParsePath)
    {
        $this->books = Items::fromFile($this->fileToParsePath);

        $this->dataStorage = new DataStorage();
    }

    public function parse(): ?Generator
    {
        $dataCollection = null;

        /** @var stdClass $book */
        foreach ($this->books as $book) {
            $this->counter++;

            $dataCollection ??= new DataCollection($this->dataStorage);
            $dataCollection->add($book);

            if ($this->counter % 10 === 0) {
                yield $dataCollection;
                $dataCollection = null;
            }
        }

        if ($this->counter % 10 !== 0) {
            yield $dataCollection;
        }
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}