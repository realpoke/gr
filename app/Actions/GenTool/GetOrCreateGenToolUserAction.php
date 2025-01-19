<?php

namespace App\Actions\GenTool;

use App\Actions\BaseAction;
use App\Models\Gentool;
use App\Models\User;

class GetOrCreateGenToolUserAction extends BaseAction
{
    protected ?User $user;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function __construct(private string $genToolId, private ?string $username = null) {}

    public function execute(): self
    {
        $existingGentool = Gentool::where('gentool_id', $this->genToolId)->with('user')->first();

        if ($existingGentool) {
            $this->user = $existingGentool->user;

            return $this->setSuccessful();
        }

        if (is_null($this->username)) {
            $this->setFailed('GenTool id not found and no username provided.');
        }

        $this->user = User::create([
            'username' => $this->username,
            'email' => $this->username.$this->genToolId.'@'.fake()->safeEmailDomain(),
            'password' => fake()->password(),
            'fake' => true,
        ]);

        if (! is_null($this->user)) {
            $gentool = $this->user->gentools()->create([
                'gentool_id' => $this->genToolId,
            ]);
        }

        return is_null($this->user) || is_null($gentool) ?
            $this->setFailed('Could not find or create GenTool user.') :
            $this->setSuccessful();
    }
}
