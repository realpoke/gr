<?php

namespace App\Contracts;

interface BaseActionContract
{
    public function handle(): self;

    public function successful(): bool;

    public function failed(): bool;

    public function getErrorMessage(): ?string;
}
