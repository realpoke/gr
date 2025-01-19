<?php

namespace App\Actions\Replay;

use App\Actions\BaseAction;
use Illuminate\Support\Collection;

class ReplayDecoderAction extends BaseAction
{
    private ?Collection $data = null;

    public function getData(): Collection
    {
        return $this->data;
    }

    public function __construct(private string $replayProcessOutput) {}

    public function execute(): self
    {
        try {
            $this->data = collect(json_decode($this->replayProcessOutput, true, 512, JSON_BIGINT_AS_STRING));
        } catch (\Throwable $exception) {
            return $this->setFailed('Failed json decoding: '.$exception->getMessage());
        }

        return $this->setSuccessful();
    }
}
