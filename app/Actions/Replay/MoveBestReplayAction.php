<?php

namespace App\Actions\Replay;

use App\Actions\BaseAction;
use App\Models\Replay;
use Illuminate\Support\Facades\Storage;

class MoveBestReplayAction extends BaseAction
{
    public function __construct(
        private string $fileName,
        private int $gameID
    ) {}

    public function execute(): self
    {
        try {
            $exists = Storage::disk('replays')->exists('good_'.$this->gameID.'.rep');

            if ($exists) {
                Storage::disk('replays')->delete('good_'.$this->gameID.'.rep');
            }

            $mover = Storage::disk('replays')->move($this->fileName, 'good_'.$this->gameID.'.rep');

            if ($mover === false) {
                return $this->setFailed('Failed to move replay to disk.');
            }

            if (! $exists) {
                Replay::create([
                    'file_name' => 'good_'.$this->gameID.'.rep',
                    'game_id' => $this->gameID,
                ]);
            }
        } catch (\Throwable $th) {
            return $this->setFailed('Failed to move replay error: '.$th->getMessage());
        }

        return $this->setSuccessful();
    }
}
