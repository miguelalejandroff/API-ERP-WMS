<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class JsonDB
{
    private string $dbPath;
    private string $collection;

    public function __construct($dbPath = 'json')
    {
        $this->dbPath = $dbPath;
        if (!Storage::disk('local')->exists($this->dbPath)) {
            Storage::disk('local')->makeDirectory($this->dbPath);
        }
    }

    /**
     * Establece la colección actual para operaciones.
     */
    public function setCollection(string $collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Devuelve el nombre del archivo JSON de la colección.
     */
    private function getFilePath(): string
    {
        return "{$this->dbPath}/{$this->collection}.json";
    }

    /**
     * Lee los datos de la colección.
     */
    private function readCollection(): array
    {
        $filePath = $this->getFilePath();

        if (!Storage::disk('local')->exists($filePath)) {
            return [];
        }

        $data = Storage::disk('local')->get($filePath);
        return json_decode($data, true) ?? [];
    }

    /**
     * Escribe datos en la colección.
     */
    private function writeCollection(array $data): bool
    {
        $filePath = $this->getFilePath();
        return Storage::disk('local')->put($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Inserta un nuevo documento en la colección.
     */
    public function insert(array $document): bool
    {
        $data = $this->readCollection();
        $document['_id'] = $document['_id'] ?? uniqid();
        $data[] = $document;

        Log::info("Documento insertado", ['document' => $document]);

        return $this->writeCollection($data);
    }

    /**
     * Encuentra un documento por ID.
     */
    public function findById(string $id): ?array
    {
        $data = $this->readCollection();
        foreach ($data as $document) {
            if ($document['_id'] == $id) {
                return $document;
            }
        }

        Log::warning("Documento no encontrado", ['id' => $id]);
        return null;
    }

    /**
     * Actualiza un documento en la colección.
     */
    public function update(string $id, array $updates): bool
    {
        $data = $this->readCollection();
        $updated = false;

        foreach ($data as &$document) {
            if ($document['_id'] == $id) {
                $document = array_merge($document, $updates);
                $updated = true;
                Log::info("Documento actualizado", ['id' => $id, 'updates' => $updates]);
                break;
            }
        }

        return $updated ? $this->writeCollection($data) : false;
    }

    /**
     * Elimina un documento por ID.
     */
    public function delete(string $id): bool
    {
        $data = $this->readCollection();
        $originalCount = count($data);

        $data = array_filter($data, fn($doc) => $doc['_id'] !== $id);

        if (count($data) === $originalCount) {
            Log::warning("Documento no encontrado para eliminar", ['id' => $id]);
            return false;
        }

        Log::info("Documento eliminado", ['id' => $id]);
        return $this->writeCollection(array_values($data));
    }

    /**
     * Encuentra documentos que coinciden con los filtros proporcionados.
     */
    public function find(array $filters = []): array
    {
        $data = $this->readCollection();

        return array_filter($data, function ($document) use ($filters) {
            foreach ($filters as $key => $value) {
                if (!isset($document[$key]) || $document[$key] != $value) {
                    return false;
                }
            }
            return true;
        });
    }
}
