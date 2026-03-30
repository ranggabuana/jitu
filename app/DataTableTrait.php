<?php

namespace App;

trait DataTableTrait
{
    /**
     * Prepare data for data table component
     * 
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @return array
     */
    protected function prepareTableData($collection)
    {
        return $collection->toArray();
    }

    /**
     * Define columns for data table
     * 
     * @param array $columns
     * @return array
     */
    protected function defineColumns(array $columns)
    {
        $defaultColumns = [];
        
        foreach ($columns as $column) {
            $defaultColumns[] = array_merge([
                'label' => $column['label'] ?? '',
                'field' => $column['field'] ?? '',
                'sortable' => $column['sortable'] ?? true,
                'type' => $column['type'] ?? 'text',
            ], $column);
        }
        
        return $defaultColumns;
    }

    /**
     * Get searchable columns
     * 
     * @param array $columns
     * @return array
     */
    protected function getSearchableFields(array $columns)
    {
        return array_filter($columns, fn($col) => $col['searchable'] ?? true);
    }
}
