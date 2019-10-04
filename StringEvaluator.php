<?php
namespace Arz\StringEvaluationBundle

class StringEvaluator
{
    public function evaluate(string expression)
    {
        return '!'.expression.'!';
    }
}