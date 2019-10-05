<?php


namespace Arz\StringEvaluationBundle;


interface SolverInterface
{
    public function add($a_l, $a_r );
    public function subtract($a_l, $a_r);
    public function multiply($a_l, $a_r );
    public function divide($a_l, $a_r);
    public function isValidNumber($a_num);
}