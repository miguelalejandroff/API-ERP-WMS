<?php

namespace App\Models;

trait HasCompositePrimaryKey
{
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * @return false
     */
    public function getIncrementing()
    {
        return false;
    }
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }
        return $this->getAttribute($keyName);
    }

    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string|array  $column
     * @return string|array
     */
    public function qualifyColumn($column)
    {
        if (!is_array($column)) {
            return parent::qualifyColumn($column);
        }

        $qualified = [];

        foreach ($column as $col) {
            $qualified[] = parent::qualifyColumn($col);
        }
        return implode(', ', $qualified);
    }

    /**
     * Qualify a single column segment.
     *
     * @param  string  $column
     * @return string
     */
    protected function qualifyColumnSegment($column)
    {
        $segments = explode('.', $column);

        foreach ($segments as $key => $segment) {
            if ($key == 0 && count($segments) > 1) {
                $segments[$key] = $this->getTable() . '.' . $segment;
            } else {
                $segments[$key] = $this->wrap($segment);
            }
        }

        return implode('.', $segments);
    }


    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        $keys = $this->getKeyName();

        if (!is_array($keys)) {
            return $this->getAttribute($keys);
        }

        $values = [];

        foreach ($keys as $keyName) {
            $values[$keyName] = $this->getAttribute($keyName);
        }
        return $values;
    }
    public function newQueryForRestoration($ids)
    {
        if (!is_array($ids) || count($ids) === 0 || !is_array($ids[0])) {

            return parent::newQueryForRestoration($ids);
        }

        $query = $this->newQueryWithoutScopes();
        $columns = [];

        foreach ($ids as $keyValues) {
            foreach ($keyValues as $column => $value) {
                if (!isset($columns[$column])) {
                    $columns[$column] = [];
                }

                if (!in_array($value, $columns[$column])) {
                    $columns[$column][] = $value;
                }
            }
        }
        foreach ($columns as $column => $value) {
            if (count($value) > 1) {
                $query->whereIn($column, $value);
                continue;
            }
            $query->where($column, $value);
        }
        return $query;
    }
}
