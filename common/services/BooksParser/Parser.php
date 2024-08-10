<?php

namespace common\services\BookParser;

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

    public function parse(): Generator
    {
        /** @var stdClass $book */
        foreach ($this->books as $book) {
            $this->counter++;

            $queue = new DataPreparer($this->dataStorage);
            $queue->prepare($book);

            if ($this->counter === 10) {
                $this->counter = 0;
                yield $queue;
            }
        }

        yield $queue;
    }
}