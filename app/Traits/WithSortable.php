<?php

namespace App\Traits;

use Livewire\Attributes\Url;

trait WithSortable
{
    #[Url]
    public $sortDirection;

    #[Url]
    public $sortBy;

    protected function queryString()
    {
        return [
            'sortDirection' => [
                'except' => $this->defaultSortDirection ?? 'asc',
            ],
            'sortBy' => [
                'except' => $this->defaultSortBy ?? $this->sortableColumns[0],
            ],
        ];
    }

    public function initializeWithSortable()
    {
        if (! property_exists($this, 'sortableColumns')) {
            throw new \Exception('Component using WithSortable trait must define protected $sortableColumns property');
        }
    }

    public function mountWithSortable()
    {
        if (! property_exists($this, 'defaultSortBy')) {
            $this->defaultSortBy = $this->sortableColumns[0];
        }

        if (! property_exists($this, 'defaultSortDirection')) {
            $this->defaultSortDirection = 'asc';
        }

        if (! $this->sortBy) {
            $this->sortBy = $this->defaultSortBy;
        }

        if (! $this->sortDirection) {
            $this->sortDirection = $this->defaultSortDirection;
        }

        if (! in_array($this->sortDirection, ['desc', 'asc'])) {
            $this->sortDirection = $this->defaultSortDirection;
        }

        if (! in_array($this->sortBy, $this->sortableColumns)) {
            $this->sortBy = $this->defaultSortBy;
        }
    }

    public function sort($column)
    {
        if (! in_array($column, $this->sortableColumns)) {
            return;
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
    }
}
