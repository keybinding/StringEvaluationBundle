<?php
namespace Arz\StringEvaluationBundle;

use Exception;

class StringEvaluator
{
    public function evaluate($expression)
    {
        try {
            $res = $this->solve($expression);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $res;
    }

    private $solver;

    /**
     * @param $expression
     * @return string
     * @throws Exception
     */
    private function solve($expression){
        $flatExpression = $this->flatten($expression);
        $flatExpression = trim($flatExpression);
        if (strlen($flatExpression) == 0) throw new Exception('Пустое выражение');
        $res = $this->solveFlat($flatExpression);
        return $res;
    }

    private function solveFlat($flatExpression){
        $operationsMap = $this->getOperationsMap();
        foreach ($operationsMap as $priorityLevel){
            foreach ($priorityLevel as $operationDesc){
                while(($pos = strpos($flatExpression, $operationDesc[0])) !== false) {
                    if ($operationDesc[1] === 'b') {
                        $rPos = $this->parseNumberRight($flatExpression, $pos + 1);
                        $lPos = $this->parseNumberLeft($flatExpression, $pos - 1);
                        $r = trim(substr($flatExpression, $pos + 1, $rPos - $pos));
                        $l = trim(substr($flatExpression, $pos - 1, $pos - $lPos));
                        $res = $operationDesc[2]($l, $r);
                        $flatExpression = substr($flatExpression, 0, $lPos) . $res . substr($flatExpression, $rPos + 1, strlen($flatExpression) - $rPos - 2);
                    } else {
                        throw new Exception('Встречен неизвестный вид операции');
                    }
                }
            }
        }
        return $flatExpression;
    }

    private function parseNumberRight($expression, $pos){
        return 0;
    }

    private function parseNumberLeft($expression, $pos){
        return 0;
    }



    private function flatten($expression){
        $pos = strpos($expression, '(');
        $oPos = strpos($expression, ')');
        if ($oPos < $pos) throw new \Exception('Лишняя открывающая скобка');
        while ($pos !== false) {
            $closing_pos = $this->findClosingBracket(substr($expression, $pos + 1));
            $value = $this->solve(substr($expression, $pos + 1, $closing_pos));
            $expression = substr($expression, 0, $pos) . ' ' . $value . ' ' . substr($expression, $pos + $closing_pos + 1);
            $pos = strpos($expression, '(');
            $oPos = strpos($expression, ')');
            if ($oPos < $pos) throw new Exception('Лишняя открывающая скобка');
        }
        return $expression;
    }

    private function findClosingBracket($expression) {
        $trigger = 1;
        $cnt = strlen($expression);
        for ($i = 0; $i != $cnt; ++$i) {
            if ($expression[$i] == '(') ++$trigger;
            elseif ($expression[$i] == ')') --$trigger;
            if ($trigger == 0) break;
        }
        if ($trigger > 0) throw new Exception('Отсутствует закрывающая скобка');
        return $i;
    }

    public function getOperationsMap()
    {
        $operationsMap = [
            [
                ['*', 'b', function($l, $r){return $this->solver->multiply($l, $r);}],
                ['/', 'b', function($l, $r){return $this->solver->divide($l, $r);}],
            ],
            [
                ['+', 'b', function($l, $r){return $this->solver->add($l, $r);}],
                ['-', 'b', function($l, $r){return $this->solver->subtract($l, $r);}],
            ]
        ];
        return $operationsMap;
    }
}