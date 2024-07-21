<?php

declare(strict_types=1);

namespace NlpTools\Analysis;

use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use PHPUnit\Framework\TestCase;

class IdfTest extends TestCase
{
    public function testIdf(): void
    {
        $trainingSet = new TrainingSet();
        $trainingSet->addDocument(
            "",
            new TokensDocument(["a", "b", "c", "d"])
        );
        $trainingSet->addDocument(
            "",
            new TokensDocument(["a", "c", "d"])
        );
        $trainingSet->addDocument(
            "",
            new TokensDocument(["a"])
        );

        $idf = new Idf($trainingSet);

        $this->assertEquals(
            0.405,
            $idf["c"],
            null
        );
        $this->assertEquals(
            1.098,
            $idf["b"],
            null
        );
        $this->assertEquals(
            1.098,
            $idf["non-existing"],
            null
        );
        $this->assertEquals(
            0,
            $idf["a"]
        );
    }
}
