<?php

namespace App\ERP\Exception;

use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

/**
 * Excepción específica para errores relacionados con la guía de compra.
 * 
 * Uso:
 * - Para errores de inserción, use GuiaCompraException::insertionError(...).
 * - Para errores de actualización, use GuiaCompraException::updateError(...).
 * - Para errores en la comunicación con el WMS, use GuiaCompraException::wmsCommunicationError(...).
 */
class GuiaCompraException extends Exception
{
    private $eventType;
    private $details;

    public const INSERTION_ERROR = 'insertion_error';
    public const UPDATE_ERROR = 'update_error';
    public const WMS_COMMUNICATION_ERROR = 'wms_communication_error';

    public function __construct(string $message, string $eventType, array $details = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->eventType = $eventType;
        $this->details = $details;
        // Aquí puedes agregar código para registrar el error, por ejemplo:

        $this->logError();
        //Log::error('Guia de Compra:', ['error' => $this->getMessage()]);
    }


    /**
     * Guarda el error en el registro.
     */
    private function logError()
    {
        $detailsString = json_encode($this->details);
        $logMessage = "GuiaCompraException - EventType: {$this->eventType}, Message: {$this->getMessage()}, Details: {$detailsString}";
        Log::error($logMessage);
    }

    public static function insertionError(string $message, array $details = [], Exception $previous = null): self
    {
        return new self($message, self::INSERTION_ERROR, $details, 0, $previous);
    }

    public static function updateError(string $message, array $details = [], Exception $previous = null): self
    {
        return new self($message, self::UPDATE_ERROR, $details, 0, $previous);
    }

    public static function wmsCommunicationError(string $message, array $details = [], Exception $previous = null): self
    {
        return new self($message, self::WMS_COMMUNICATION_ERROR, $details, 0, $previous);
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
