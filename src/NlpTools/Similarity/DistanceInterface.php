<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * Distance should return a number proportional to how dissimilar
 * the two instances are(with any metric)
 */
interface DistanceInterface
{
    public function dist(array &$a, array &$b): float;
}
