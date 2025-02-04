<?php

namespace App\Livewire\Map;

use App\Enums\Game\GameModeEnum;
use App\Models\Map;
use App\Traits\WithSortable;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy()]
#[Title('title.maps')]
class IndexMapPage extends Component
{
    use WithPagination, WithSortable;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: false)]
    public $verifiedOnly = false;

    #[Url(except: 'all')]
    public $mode = 'all';

    #[Url(except: '15')]
    public $amount = '15';

    protected $sortableColumns = [
        'name',
        'plays',
        'plays_monthly',
        'plays_weekly',
        'updated_at',
        'created_at',
        'verified_at',
    ];

    protected $defaultSortBy = 'plays';

    protected $defaultSortDirection = 'desc';

    public function mount()
    {
        $amount = $this->amount;
        if (! ($amount == '15' || $amount == '25' || $amount == '50')) {
            $this->reset('amount');
        }

        $mode = $this->mode;
        if ($mode != 'all' && ! GameModeEnum::tryFrom($mode)) {
            $this->reset('mode');
        }

        $verifiedOnly = $this->verifiedOnly;
        if (! is_bool($verifiedOnly)) {
            $this->reset('verifiedOnly');
        }
    }

    public function placeholder()
    {
        return view('livewire.map.index-map-placeholder');
    }

    public function resetFilters()
    {
        $this->reset('verifiedOnly');
        $this->reset('mode');
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
    public function maps()
    {
        return Map::orderBy($this->sortBy, $this->sortDirection)
            ->when($this->search != '', fn ($q) => $q->search($this->search))
            ->when($this->verifiedOnly, fn ($q) => $q->where('verified_at', '!=', null))
            ->when($this->mode != 'all', fn ($q) => $q->whereJsonContains('modes', $this->mode))
            ->paginate($this->amount);
    }

    #[Computed()]
    public function hasFiltersApplied(): bool
    {
        return $this->verifiedOnly || $this->mode != 'all';
    }
}
