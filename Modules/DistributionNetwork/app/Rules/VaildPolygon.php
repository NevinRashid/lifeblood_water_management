<?php

namespace Modules\DistributionNetwork\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class VaildPolygon implements Rule
{
    protected string $message = 'The polygon is invalid.';
    /**
     *
     * @param $attribute, $value
     *
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        if (!is_array($value) || count($value) < 4) {
            $this->message = 'The polygon must contain at least 4 points.';
            return false;
        }

        // Check if the polygon is closed (first == last)
        $first = $value[0];
        $last = $value[count($value) - 1];

        if ($first !== $last) {
            $this->message = 'The polygon must be closed. The first and last point must be identical.';
            return false;
        }

        // Check for self-intersections
        for ($i = 0; $i < count($value) - 1; $i++) {
            for ($j = $i + 1; $j < count($value) - 1; $j++) {
                if (abs($i - $j) <= 1) {
                    continue; // Skip adjacent edges
                }

                if ($i == 0 && $j == count($value) - 2) {
                    continue; // Skip first and last segment (closing edge)
                }

                if ($this->linesIntersect($value[$i], $value[$i + 1], $value[$j], $value[$j + 1])) {
                    $this->message = 'The polygon has self-intersecting edges.';
                    return false;
                }
            }
        }
        return true;
    }

    protected function linesIntersect($a, $b, $c, $d): bool
    {
        [$x1, $y1] = $a;
        [$x2, $y2] = $b;
        [$x3, $y3] = $c;
        [$x4, $y4] = $d;

        $denom = ($y4 - $y3) * ($x2 - $x1) - ($x4 - $x3) * ($y2 - $y1);
        if ($denom == 0) return false;

        $ua = (($x4 - $x3) * ($y1 - $y3) - ($y4 - $y3) * ($x1 - $x3)) / $denom;
        $ub = (($x2 - $x1) * ($y1 - $y3) - ($y2 - $y1) * ($x1 - $x3)) / $denom;

        return ($ua > 0 && $ua < 1) && ($ub > 0 && $ub < 1);
    }

        public function message(): string
    {
        return $this->message;
    }
}
