<?php

declare(strict_types=1);

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Generators\GeneratorInterface;

/**
 * Implement a k-dimensional Dirichlet distribution using draws from
 * k gamma distributions and then normalizing.
 */
class Dirichlet extends AbstractDistribution
{
    protected array $gamma;

    public function __construct($a, $k, GeneratorInterface $generator = null)
    {
        parent::__construct($generator);

        $k = (int) abs($k);
        if (!is_array($a)) {
            $a = array_fill_keys(range(0, $k - 1), $a);
        }

        $generator = $this->rnd;
        $this->gamma = array_map(
            fn($a): \NlpTools\Random\Distributions\Gamma => new Gamma($a, 1, $generator),
            $a
        );
    }

    public function sample(): array
    {
        $y = [];
        foreach ($this->gamma as $g) {
            $y[] = $g->sample();
        }

        $sum = array_sum($y);

        return array_map(
            fn($y): int|float => $y / $sum,
            $y
        );
    }
}
