<?php

namespace App\Actions\Claim;

use App\Actions\BaseAction;
use App\Events\PrivateClaimingEvent;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;

use function Illuminate\Support\defer;

class ClaimFoundClaimGameAction extends BaseAction
{
    public function __construct(private Game $game) {}

    public function execute(): self
    {
        if (! Auth::check()) {
            return $this->setFailed('User not logged in.');
        }
        $user = Auth::user();
        $claim = $user->claim;

        if (! $user->canClaimMoreComputers()) {
            return $this->setFailed('User cannot claim more computers.');
        }

        $players = collect($this->game->data['players']);

        $found = false;

        foreach ($players as $player) {
            if ($player['name'] == $claim->name) {
                dump('Player found');
                $found = true;
                break;
            }
        }

        if (! $found) {
            return $this->setFailed('Player not found.');
        }

        $users = $this->game->users;

        foreach ($users as $user) {
            if ($user->pivot->player_name == $claim->name) {
                $gentool = $user->pivot->gentool;
                dump('Gentool found');
                dump($gentool);
                break;
            }
        }

        if (is_null($gentool)) {
            return $this->setFailed('Gentool not found.');
        }

        if ($gentool->user_id == $claim->user_id) {
            return $this->setFailed('Gentool already belongs to user.');
        }

        if ($gentool->private) {
            return $this->setFailed('Gentool is private. The user should delete the gentool from their account.');
        }

        $gentool->user_id = $claim->user_id;
        $gentool->private = $claim->private;
        $gentool->save();

        $claim->delete();

        // FIXME: When a claim is done, and the state resets the modal is in a stuck placeholder state
        defer(fn () => broadcast(new PrivateClaimingEvent));

        return $this->setSuccessful();
    }
}
