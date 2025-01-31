<?php

namespace App\Actions\Claim;

use App\Actions\BaseAction;
use App\Events\PrivateClaimingEvent;
use App\Models\Gentool;
use Illuminate\Support\Facades\Auth;

use function Illuminate\Support\defer;

class RemoveClaimGentoolAction extends BaseAction
{
    public function __construct(private Gentool $gentool) {}

    public function execute(): self
    {
        if (! Auth::check()) {
            return $this->setFailed('User not logged in.');
        }

        $this->gentool->delete();

        defer(fn () => broadcast(new PrivateClaimingEvent));

        return $this->setSuccessful();
    }
}
