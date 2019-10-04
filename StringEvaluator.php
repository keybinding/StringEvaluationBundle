<?php
namespace Arz\StringEvaluationBundle;

class StringEvaluator
{
    public function evaluate(expression)
    {
        return '!'.expression.'!';
    }
}