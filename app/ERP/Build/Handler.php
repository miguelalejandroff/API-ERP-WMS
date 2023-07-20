<?php

namespace App\ERP\Build;

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
            //$context->addLog(get_class($this) . " failed with message: " . $e->getMessage());
            throw $e;
        }
    }

    abstract protected function handle($context);
}
