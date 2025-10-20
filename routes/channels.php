<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Usuario;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Default Laravel example uses App.Models.User; adjust to our Usuario model
Broadcast::channel('App.Models.Usuario.{id}', function (Usuario $user, $id) {
    return (int) $user->id_usuario_pk === (int) $id;
});

// Back-compat channel name in case frontend expects `users.{id}`
Broadcast::channel('users.{id}', function (Usuario $user, $id) {
    return (int) $user->id_usuario_pk === (int) $id;
});
