<?php

namespace common\services\BooksParser;

use Generator;
use JsonMachine\Items;
use stdClass;

class Parser
{
    protected Items $books;

    protected int $counter = 0;
    protected int $parsedBooksCount = 0;
    protected DataStorage $dataStorage;

    public function __construct(protected string $fileToParsePath)
    {
        $this->books = Items::fromFile($this->fileToParsePath);

        $this->dataStorage = new DataStorage();
    }

    public function parse(): ?Generator
    {
        /** @var stdClass $book */
        foreach ($this->books as $book) {
            $this->counter++;
            $this->parsedBooksCount++;

            $notPreparedData ??= new DataPreparer($this->dataStorage);
            $notPreparedData->prepare($book);

            if ($this->counter === 10) {
                $this->counter = 0;
                $preparedData = $notPreparedData;
                $notPreparedData = null;
                yield $preparedData;
            }
        }
        if ($this->counter > 0 && $this->counter < 10) {
            yield $notPreparedData;
        }
    }

    public function getParsedBooksCount(): int
    {
        return $this->parsedBooksCount;
    }
}