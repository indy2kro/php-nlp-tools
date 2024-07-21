<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * http://en.wikipedia.org/wiki/Jaccard_index
 */
class JaccardIndex implements SimilarityInterface, DistanceInterface
{
    /**
     * The similarity returned by this algorithm is a number between 0,1
     */
    public function similarity(array &$a, array &$b): float
    {
        $aa = array_fill_keys($a, 1);
        $bb = array_fill_keys($b, 1);

        $intersect = count(array_intersect_key($aa, $bb));
        $union = count(array_fill_keys(array_merge($a, $b), 1));

        return $intersect / $union;
    }

    /**
     * Jaccard Distance is simply the complement of the jaccard similarity
     */
    public function dist(array &$a, array &$b): float
    {
        return 1 - $this->similarity($a, $b);
    }
}
