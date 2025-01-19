<?php

namespace App\Actions\Replay;

use App\Actions\BaseAction;

class ProcessReplayAction extends BaseAction
{
    protected ReplayParserAction $parsedReplay;

    public function getParsedReplay(): ReplayParserAction
    {
        return $this->parsedReplay;
    }

    public function __construct(private string $fileName) {}

    public function execute(): self
    {
        $processor = new ReplayProcessorAction($this->fileName);
        $processor->handle();
        if ($processor->failed()) {
            return $this->setFailed('Failed to process replay: '.$processor->getErrorMessage());
        }

        $decoded = new ReplayDecoderAction($processor->getProcessOutput());
        $decoded->handle();
        if ($decoded->failed()) {
            return $this->setFailed('Failed to decode replay: '.$decoded->getErrorMessage());
        }

        $parsed = new ReplayParserAction($decoded->getData());
        $parsed->handle();
        if ($parsed->failed()) {
            return $this->setFailed('Failed to parse replay: '.$parsed->getErrorMessage());
        }

        $validated = new ReplayValidatorAction($parsed);
        $validated->handle();
        if ($validated->failed()) {
            return $this->setFailed('Replay failed validation: '.$validated->getErrorMessage());
        }

        $this->parsedReplay = $parsed;

        return $this->setSuccessful();
    }
}
