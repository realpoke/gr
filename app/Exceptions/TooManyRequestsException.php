<?php

namespace App\Exceptions;

use Exception;

class TooManyRequestsException extends Exception
{
    public $minutesUntilAvailable;

    public function __construct(
        public $key,
        public $secondsUntilAvailable,
    ) {
        $this->minutesUntilAvailable = ceil($this->secondsUntilAvailable / 60);

        parent::__construct(__('exception.too-many-requests', [
            'key' => $this->key,
            'minutes' => $this->minutesUntilAvailable,
        ]));
    }
}
