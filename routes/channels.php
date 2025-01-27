<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('Interface.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('Public.Interface', function () {});
Broadcast::channel('Public.Map', function () {});
