<?php


namespace Arz\StringEvaluationBundle;


interface IOperationsProvider
{
    public function getOperationCallback($operation);
}