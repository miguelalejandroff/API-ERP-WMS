<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Storage;

class jsonDB
{
    public function __call($name, $arguments)
    {
        // Si el nombre del método comienza con "set", se asume que se está 
        // tratando de definir una propiedad
        if (strpos($name, 'set') === 0) {
            $property = lcfirst(substr($name, 3));
            //$this->data[$property] = $arguments[0];
            return $this;
        }

        if (Storage::disk('local')->exists("json/_id{$name}.json")) {
            return "si hay";
        }
        throw new Exception("El método $name no existe en la clase.");
    }
    public function find()
    {
        return $this;
    }
    /*
    function __construct(
        private $filename = null,
        private $idField = '_id',
        private $dbPath = null,
        private $partitionSize = 1000
    ) {

        $this->dbPath = storage_path('data');
        $this->filename = $filename;
        $this->idField = $idField;
        $this->partitionSize = $partitionSize;
    }
    
    private function getPartitionFilename($id)
    {
        $partitionId = (int) ($id / $this->partitionSize);
        return $this->filename . '.' . $partitionId;
    }

    private function readPartition($filename)
    {
        if (file_exists($filename)) {
            $data = file_get_contents($filename);
            return json_decode($data, true);
        } else {
            return [];
        }
    }

    private function writePartition($filename, $data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($filename, $json);
    }

    private function writeToFile($data)
    {
        $partitionId = 0;
        foreach ($data as $doc) {
            $id = $doc[$this->idField];
            $partitionFilename = $this->getPartitionFilename($id);
            if ($partitionFilename != $currentPartitionFilename) {
                $currentPartitionFilename = $partitionFilename;
                $partitionData = $this->readPartition($currentPartitionFilename);
            }
            $partitionData[] = $doc;
            if (count($partitionData) >= $this->partitionSize) {
                $this->writePartition($currentPartitionFilename, $partitionData);
                $partitionId += 1;
                $currentPartitionFilename = $this->filename . '.' . $partitionId;
                $partitionData = [];
            }
        }
        if (!empty($partitionData)) {
            $this->writePartition($currentPartitionFilename, $partitionData);
        }
    }

    private function searchInPartition($data, $id)
    {
        foreach ($data as $doc) {
            if ($doc[$this->idField] == $id) {
                return $doc;
            }
        }
        return null;
    }

    private function searchInFile($id)
    {
        $partitionFilename = $this->getPartitionFilename($id);
        $partitionData = $this->readPartition($partitionFilename);
        return $this->searchInPartition($partitionData, $id);
    }

    public function findById($id)
    {
        return $this->searchInFile($id);
    }

    public function insert($doc)
    {
        $data = $this->readAll();
        $data[] = $doc;
        $this->writeToFile($data);
    }

    public function update($id, $update)
    {
        $data = $this->readAll();
        foreach ($data as &$doc) {
            if ($doc[$this->idField] == $id) {
                foreach ($update as $key => $value) {
                    $doc[$key] = $value;
                }
                break;
            }
        }
        $this->writeToFile($data);
    }

    public function delete($collection, $filter = [])
    {
        $collection_path = $this->db_path . '/' . $collection . '.json';

        if (!file_exists($collection_path)) {
            return false;
        }

        $data = $this->read($collection);

        $updated_data = [];

        foreach ($data as $document) {
            if (!$this->compareFilter($document, $filter)) {
                $updated_data[] = $document;
            }
        }

        return $this->write($collection, $updated_data);
    }
    private function readAll()
    {
        $data = [];

        if (file_exists($this->db_path)) {
            foreach (scandir($this->db_path) as $collection_file) {
                if (strpos($collection_file, '.json') !== false) {
                    $collection = str_replace('.json', '', $collection_file);
                    $data[$collection] = $this->read($collection);
                }
            }
        }

        return $data;
    }

    private function read($collection)
    {
        $collection_path = $this->db_path . '/' . $collection . '.json';

        if (!file_exists($collection_path)) {
            return [];
        }

        $data = file_get_contents($collection_path);

        return json_decode($data, true);
    }

    private function compareFilter($document, $filter)
    {
        foreach ($filter as $field => $value) {
            if (isset($document[$field]) && $document[$field] == $value) {
                return true;
            }
        }

        return false;
    }

    private function write($collection, $data)
    {
        $collection_path = $this->db_path . '/' . $collection . '.json';

        if (!file_exists($collection_path)) {
            touch($collection_path);
        }

        $json_data = json_encode($data, JSON_PRETTY_PRINT);

        return file_put_contents($collection_path, $json_data);
    }*/
}
