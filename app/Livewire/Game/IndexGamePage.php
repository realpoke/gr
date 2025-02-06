<?php

namespace App\Livewire\Game;

use App\Enums\Game\GameStatusEnum;
use App\Enums\Game\GameTypeEnum;
use App\Enums\Rank\RankBracketEnum;
use App\Models\Game;
use App\Models\Map;
use App\Traits\WithSortable;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy()]
#[Title('title.games')]
class IndexGamePage extends Component
{
    use WithPagination, WithSortable;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $cursor = '';

    #[Url(except: true)]
    public $live = true;

    #[Url(except: '15')]
    public $amount = '15';

    #[Url(except: '')]
    public $map = '';

    #[Url(except: 'all')]
    public $type = 'all';

    #[Url(except: ['awaiting', 'processing', 'ranked', 'unranked'])]
    public $statuses = ['awaiting', 'processing', 'ranked', 'unranked'];

    #[Url(except: 'all')]
    public $bracket = 'all';

    private array $with = [
        'map:id,name,hash',
        'users:id,username',
    ];

    protected $sortableColumns = [
        'elo_average',
        'type',
        'status',
        'length',
        'created_at',
    ];

    protected $defaultSortBy = 'created_at';

    protected $defaultSortDirection = 'desc';

    public function placeholder()
    {
        return view('livewire.game.index-game-placeholder');
    }

    // TODO: Check if this game is in the filter/search and add it to the games list
    #[On('echo:Public.Game,PublicGameCreatedEvent')]
    public function refreshTable()
    {
        if (! $this->live) {
            $this->skipRender();
        }
    }

    public function mount()
    {
        $live = $this->live;
        if (! is_bool($live)) {
            $this->reset('live');
        }

        $amount = $this->amount;
        if (! ($amount == '15' || $amount == '25' || $amount == '50')) {
            $this->reset('amount');
        }

        $map = $this->map;
        if ($map != '' && ! Map::find($map)->exists()) {
            $this->reset('map');
        }

        $type = $this->type;
        if ($type != 'all' && ! GameTypeEnum::tryFrom($type)) {
            $this->reset('type');
        }

        $statuses = $this->statuses;
        if (is_array($statuses)) {
            $this->statuses = array_filter($statuses, function ($value) {
                return GameStatusEnum::tryFrom($value) !== null;
            });
        }

        $bracket = $this->bracket;
        if ($bracket != 'all' && ! RankBracketEnum::tryFrom($bracket)) {
            $this->reset('bracket');
        }
    }

    public function resetFilters()
    {
        $this->reset('map');
        $this->reset('type');
        $this->reset('statuses');
        $this->reset('bracket');
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

    public function latestGame()
    {
        return Game::with($this->with)
            ->latest()
            ->first();
    }

    #[Computed()]
    public function games()
    {
        return Game::with($this->with)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->when($this->search != '', fn ($query) => $query->search($this->search))
            ->when($this->map, fn ($query) => $query->where('map_id', $this->map))
            ->when($this->type != 'all', fn ($query) => $query->where('type', $this->type))
            ->when($this->statuses, fn ($query) => $query->whereIn('status', $this->statuses))
            ->when(! in_array($this->bracket, ['all', 'unranked']), fn ($query) => $query->whereBetween('elo_average', RankBracketEnum::tryFrom($this->bracket)->eloRange()))
            ->when($this->bracket == 'unranked', fn ($query) => $query->whereNull('elo_average'))
            ->cursorPaginate($this->amount);
    }

    #[Computed()]
    public function hasFiltersApplied(): bool
    {
        return $this->map != '' ||
            $this->type != 'all' ||
            $this->statuses != ['awaiting', 'processing', 'ranked', 'unranked'] ||
            $this->bracket != 'all';
    }
}
