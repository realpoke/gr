<?php

namespace App\Livewire\Game;

use App\Enums\FactionEnum;
use App\Models\Game;
use App\Models\Map;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.games')]
class ShowGamePage extends Component
{
    public Game $game;

    public function render()
    {
        return view('livewire.game.type.game-type-'.$this->game->type->getGameMode()->value.'-component');
    }

    #[Computed()]
    public function teams(): Collection
    {
        return collect($this->game->data['players'])
            ->filter(fn ($player) => $player['isPlaying'])
            ->groupBy(fn ($player) => $player['team']);
    }

    #[Computed()]
    public function players(): Collection
    {
        $users = $this->game->users->keyBy(fn ($user) => $user->pivot->player_name);

        return collect($this->game->data['players'])
            ->filter(fn ($player) => $player['isPlaying'])
            ->map(function ($player) use ($users) {
                if (isset($users[$player['name']])) {
                    $user = $users[$player['name']];
                    $player['eliminated_position'] = $user->pivot->eliminated_position;
                    $player['elo_change'] = $user->pivot->elo_change;
                }

                return $player;
            });
    }

    #[Computed()]
    public function observers(): Collection
    {
        return collect($this->game->data['players'])
            ->filter(fn ($player) => FactionEnum::tryFrom($player['faction'])->isObserver());
    }

    #[Computed()]
    public function playedAt()
    {
        return Carbon::createFromTimeString($this->game->data['playedAt']);
    }

    #[Computed()]
    public function map(): Map
    {
        return $this->game->map;
    }

    #[Computed()]
    public function startingCredits(): int
    {
        return $this->game->data['metaData']['StartingCredits'];
    }

    #[Computed()]
    public function interval(): CarbonInterval
    {
        return CarbonInterval::seconds($this->game->data['metaData']['gameInterval']);
    }

    #[Computed()]
    public function playersPlaying(): int
    {
        return $this->game->data['metaData']['playersPlaying'];
    }
}
