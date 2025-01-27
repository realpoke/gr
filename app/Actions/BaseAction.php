<?php

namespace App\Actions;

use App\Contracts\BaseActionContract;
use App\Exceptions\InvalidActionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseAction implements BaseActionContract
{
    private ?bool $success = null;

    private ?string $errorMessage = null;

    final protected function setSuccessful(): self
    {
        DB::commit();
        $this->success = true;

        return $this;
    }

    final protected function setFailed(string $errorMessage): self
    {
        Log::info('Action failed in '.get_class($this).': '.$errorMessage);

        DB::rollBack();

        $this->success = false;
        $this->errorMessage = $errorMessage;

        return $this;
    }

    // Override this method to perform the action
    // Should always run setSuccessful() or setFailed()
    // It could also throw an exception, which will be caught and setFailed()
    protected function execute(): self
    {
        return $this;
    }

    final public function handle(): self
    {
        DB::beginTransaction();

        try {
            $this->execute();
        } catch (\Throwable $e) {
            $this->setFailed($e->getMessage());
            throw $e;
        }

        if (is_null($this->success)) {
            DB::rollBack();
            throw new InvalidActionException('Action did not fail or succeed, this should never happen.');
        }

        return $this;
    }

    final public function successful(): bool
    {
        return $this->success ?? false;
    }

    final public function failed(): bool
    {
        return ! $this->successful();
    }

    final public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
