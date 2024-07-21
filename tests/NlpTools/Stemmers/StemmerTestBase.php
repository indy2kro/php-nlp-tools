<?php

declare(strict_types=1);

namespace NlpTools\Stemmers;

use PHPUnit\Framework\TestCase;

/**
 * This class simply provides a bit of functionality to test
 * a stemmer against two lists of words and stems just to keep
 * the test code a bit DRY
 */
class StemmerTestBase extends TestCase
{
    protected function checkStemmer(Stemmer $stemmer, \Iterator $words, \Iterator $stems)
    {
        foreach ($words as $word) {
            if ($word === false) {
                continue;
            }

            $stem = $stems->current();
            $this->assertEquals(
                $stemmer->stem($word),
                $stem,
                sprintf("The stem for '%s' should be '%s' not '%s'", $word, $stem, $stemmer->stem($word))
            );
            $stems->next();
        }
    }
}
