<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Usuario;




Broadcast::channel('App.Models.Usuario.{id}', function (Usuario $user, $id) {
    return (int) $user->id_usuario_pk === (int) $id;
});


Broadcast::channel('users.{id}', function (Usuario $user, $id) {
    return (int) $user->id_usuario_pk === (int) $id;
});
