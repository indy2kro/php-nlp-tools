<?php

declare(strict_types=1);

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Generators\GeneratorInterface;
use NlpTools\Random\Distributions\Normal;

/**
 * Implement the gamma distribution.
 * The implementation is ported to php from c++. C++ is written by John
 * D. Cook and can be found at http://www.johndcook.com/SimpleRNG.cpp
 */
class Gamma extends AbstractDistribution
{
    protected Normal $normal;

    protected Gamma $gamma;

    protected float|int $shape;

    public function __construct($shape, protected $scale, GeneratorInterface $generator = null)
    {
        parent::__construct($generator);
        $this->shape = abs($shape);
        if ($this->shape >= 1) {
            $this->normal = new Normal(0, 1, $this->rnd);
        } else {
            $this->gamma = new Gamma($this->shape + 1, 1, $this->rnd);
        }
    }

    public function sample(): ?float
    {
        if ($this->shape >= 1) {
            $d = $this->shape - 1 / 3;
            $c = 1 / sqrt(9 * $d);
            for (;;) {
                do {
                    $x = $this->normal->sample();
                    $v = 1 + $c * $x;
                } while ($v <= 0);

                $v = $v * $v * $v;
                $u = $this->rnd->generate();
                $xsq = $x * $x;
                if ($u < 1 - .0331 * $xsq * $xsq || log($u) < 0.5 * $xsq + $d * (1 - $v + log($v))) {
                    return $this->scale * $d * $v;
                }
            }
        } else {
            $g = $this->gamma->sample();
            $w = $this->rnd->generate();

            return $this->scale * $g * $w ** (1 / $this->shape);
        }

        return null;
    }
}
