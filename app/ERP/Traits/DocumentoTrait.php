<?php

namespace App\ERP\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Exception;

trait DocumentoTrait
{
    /**
     * @var Model|null $documento La instancia del documento.
     */
    private $documento;

    /**
     * Obtiene el documento completo o un valor específico por clave.
     *
     * @param string|null $clave La clave específica a obtener.
     * @return mixed|null Devuelve el documento completo o el valor de la clave especificada.
     */
    public function getDocumento($clave = null)
    {
        if (is_null($clave)) {
            return $this->documento;
        }

        return $this->documento->{$clave} ?? null;
    }

    /**
     * Establece el documento.
     *
     * @param Model $documento Instancia del documento.
     * @throws \InvalidArgumentException
     */
    public function setDocumento($documento)
    {
        if (!$documento instanceof Model) {
            throw new \InvalidArgumentException("El documento debe ser una instancia de Illuminate\Database\Eloquent\Model.");
        }

        $this->documento = $documento;
    }

    /**
     * Guarda el documento aplicando un callback para actualizar sus valores.
     *
     * @param callable $callback Callback para establecer los valores del documento.
     * @return Model Devuelve la instancia del documento guardado.
     * @throws \Throwable
     */
    public function guardarDocumento(callable $callback): Model
    {
        try {
            // Cargar el documento actual o crear una nueva instancia
            $documento = $this->documento ?? new $this->modeloDocumento;

            // Aplicar el callback para definir valores
            $callback($documento);

            // Guardar el documento
            $documento->saveOrFail();

            // Recargar el documento después de guardarlo
            $this->cargarDocumento($documento->{$this->claveDocumento});

            Log::info("Documento guardado exitosamente.", ['documento' => $documento]);

            return $documento;
        } catch (Exception $e) {
            Log::error("Error al guardar el documento.", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Carga el documento usando su clave primaria.
     *
     * @param mixed $claveDocumento Valor de la clave primaria del documento.
     * @return void
     */
    protected function cargarDocumento($claveDocumento)
    {
        $modelo = $this->modeloDocumento;

        $this->documento = $modelo::findOrFail($claveDocumento);

        Log::info("Documento recargado con éxito.", ['documento_id' => $claveDocumento]);
    }
}
