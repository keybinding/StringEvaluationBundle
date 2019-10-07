<?php


namespace Arz\StringEvaluationBundle;


class DefaultOperationProvider implements IOperationsProvider
{

    public function getOperationCallback($operation)
    {
        switch ($operation){
            case '+':
                return function ($l, $r) {return $l + $r;};
                break;
            case '-':
                return function ($l, $r) {return $l - $r;};
                break;
            case '*':
                return function ($l, $r) {return $l * $r;};
                break;
            case '/':
                return function ($l, $r) {return $l / $r;};
                break;
            default:
                throw new \Exception('Неопределенная операция');
        }
    }
}