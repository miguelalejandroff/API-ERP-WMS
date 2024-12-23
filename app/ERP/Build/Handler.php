<?php

namespace App\ERP\Build;

use App\ERP\Context\OrdenEntradaContext;
use App\Exceptions\CustomException;
use Exception;

abstract class Handler
{
    private $next;

    public function setNext(Handler $handler): Handler
    {
        $this->next = $handler;
        return $handler;
    }

    public function execute($context)
    {
        try {
            $this->handle($context);
            //$context->addLog(get_class($this) . " executed successfully.");

            if ($this->next) {
                $this->next->execute($context);
            }
        } catch (Exception $e) {

            $exception = new CustomException("Error en " . get_class($this) . ": " . $e->getMessage(), [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 500);

            $exception->saveToDatabase();
            throw $exception;
        }
    }

    abstract protected function handle(OrdenEntradaContext $context);
}
