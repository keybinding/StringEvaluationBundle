<?php


namespace Arz\StringEvaluationBundle;


class DefaultSolver implements SolverInterface
{

    public function add($l, $r)
    {
        return $l + $r;
    }

    public function subtract($l, $r)
    {
        return $l - $r;
    }

    public function multiply($l, $r)
    {
        return $l * $r;
    }

    public function divide($l, $r)
    {
        return $l / $r;
    }

    public function isValidNumber($a_num)
    {
        return true;
    }
}