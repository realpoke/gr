<?php

namespace App\Livewire\Game;

use App\Models\Game;
use App\Traits\WithSortable;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('title.games')]
class IndexGamePage extends Component
{
    use WithPagination, WithSortable;

    #[Url(except: '')]
    public $search = '';

    protected $sortableColumns = [
        'hash',
        'created_at',
        'updated_at',
    ];

    protected $defaultSortBy = 'created_at';

    protected $defaultSortDirection = 'desc';

    public function resetFilters()
    {
        Flux::toast(__('toast.filters-reset'));
    }

    public function applyFilters()
    {
        Flux::toast(__('toast.filters-applied'));
    }

    public function updated()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function games()
    {
        return Game::orderBy($this->sortBy, $this->sortDirection)
            ->when($this->search != '', fn ($q) => $q->search($this->search))
            ->paginate();
    }

    #[Computed()]
    public function hasFiltersApplied(): bool
    {
        return false;
    }
}
