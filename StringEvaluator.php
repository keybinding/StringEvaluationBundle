<?php
namespace Arz\StringEvaluationBundle;

use Exception;

class StringEvaluator
{
    public function evaluate($expression)
    {
        $this->operationsProvider = new DefaultOperationProvider();
        $this->operations = ['*', '/', '+', '-'];
        try {
            $res = $this->solve($expression);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $res;
    }

    private $operationsProvider;
    private $operations;
    /**
     * @param $expression
     * @return string
     * @throws Exception
     */
    private function solve($expression){
        $flatExpression = $this->flatten($expression);
        if (!$this->isValid($flatExpression)) throw new Exception('Неверное выражение');
        $res = $this->solveFlat($flatExpression);
        return $res;
    }

    private function isValid($expresstion) {
        if (preg_match('#(\s*-?[0-9]+(\.[0-9]+)?\s*[-,+,\/,*])*\s*-?[0-9]+(\.[0-9]+)?\s*#', $expresstion, $matches) !== 1) return false;
        if ($matches[0] == $expresstion) return true;
        return false;
    }

    private  function solveFlat($expression)
    {
        $expression = trim($expression);
        $numOperMap = $this->prepareFlat($expression);
        $this->processOperations($numOperMap, ['*', '/']);
        $this->processOperations($numOperMap, ['+', '-']);
        return $numOperMap['numbers'][0];
    }

    private function processOperations(&$numOperMap, $operationsToProcess){
        $callbacks = [];
        foreach ($this->operations as $o){
            $callbacks[$o] = $this->operationsProvider->getOperationCallback($o);
        }
        $operCnt = count($numOperMap['operations']);
        $i = 0;
        while($i < $operCnt) {
            $operation = $numOperMap['operations'][$i];
            if (in_array($operation, $operationsToProcess)) {
                $numOperMap['numbers'][$i] = $callbacks[$operation]($numOperMap['numbers'][$i], $numOperMap['numbers'][$i + 1]);
                array_splice($numOperMap['numbers'], $i + 1, 1);
                array_splice($numOperMap['operations'], $i, 1);
                $operCnt -= 1;
            }
            else ++$i;
        }
    }

    private function prepareFlat($flatExpression){
        preg_match_all('#[0-9]+(\.[0-9]+)?#', $flatExpression, $matches, PREG_OFFSET_CAPTURE);
        $numbers = $matches[0];
        $cnt = count($numbers);
        $firstNumPos = $numbers[0][1];
        if ($firstNumPos > 0) {
            if (trim(substr($flatExpression, 0, $firstNumPos)) == '-') {
                $numbers[0][0] = '-'.$numbers[0][0];
                $numbers[0][1] -= 1;
            }
        }
        if ($cnt == 1) return ['numbers' => [$numbers[0][0]], 'operations' => []];
        $operations = [];
        for ($i = 0, $j = 1; $j < $cnt; ++$i, ++$j) {
            $startPos = $numbers[$i][1] + strlen($numbers[$i][0]);
            $endPos = $numbers[$j][1];
            $operationHolder = substr($flatExpression, $startPos, $endPos - $startPos);
            foreach ($this->operations as $operation){
                $pos = strpos($operationHolder, $operation);
                if ($pos !== false){
                    $operations[] = $operation;
                    if ($pos < strlen($operationHolder) - 1){
                        if ($operationHolder[strlen($operationHolder) - 1] == '-'){
                            $numbers[$j][0] = '-'.$numbers[$j][0];
                            $numbers[$j][1] -= 1;
                        }
                    }
                }
                break;
            }
        }
        $numbers = array_column($numbers, 0);
        return ['numbers' => $numbers, 'operations' => $operations];
    }

    private function flatten($expression){
        $pos = strpos($expression, '(');
        while ($pos !== false) {
            $oPos = strpos($expression, ')');
            if ($oPos < $pos) throw new \Exception('Лишняя открывающая скобка');
            $closing_pos = $this->findClosingBracket(substr($expression, $pos + 1));
            $value = $this->solve(substr($expression, $pos + 1, $closing_pos));
            $expression = substr($expression, 0, $pos) . $value . substr($expression, $pos + $closing_pos + 2);
            $pos = strpos($expression, '(');
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
}