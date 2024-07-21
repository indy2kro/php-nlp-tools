<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * Similarity should return a number that is proportional to how
 * similar those two instances are (with any metric).
 *
 */
interface SimilarityInterface
{
    public function similarity(array &$a, array &$b): float;
}
