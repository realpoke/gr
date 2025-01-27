<?php

namespace App\Actions\Clean;

use App\Actions\BaseAction;
use App\Models\Claim;

class CleanOldClaimsAction extends BaseAction
{
    public function execute(): self
    {
        Claim::where('expires_at', '<', now())->delete();

        return $this->setSuccessful();
    }
}
