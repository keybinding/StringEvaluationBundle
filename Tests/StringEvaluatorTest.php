<?php


namespace Arz\StringEvaluationBundle\Tests;

use Arz\StringEvaluationBundle\DefaultOperationProvider;
use Arz\StringEvaluationBundle\StringEvaluator;
use PHPUnit\Framework\TestCase;

class StringEvaluatorTest extends TestCase
{
    public function testEvaluate()
    {
        $defaultOperationProvider = new DefaultOperationProvider();
        $stringEvaluator = new StringEvaluator($defaultOperationProvider);
        $result = $stringEvaluator->evaluate('1');
        $this->assertEquals('1', $result);
        $result = $stringEvaluator->evaluate('-1 + 5 / 0');
        $this->assertEquals('Division by zero', $result);
        $result = $stringEvaluator->evaluate('-1 + 5 *(-3+8)/50 ');
        $this->assertEquals('-0.5', $result);
        $result = $stringEvaluator->evaluate('-1 + 5 -*(-3+8)/50 ');
        $this->assertEquals('Неверное выражение', $result);
        $result = $stringEvaluator->evaluate('');
        $this->assertEquals('Неверное выражение', $result);
    }
}