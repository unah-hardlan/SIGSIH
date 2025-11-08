<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;



Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test', function () {
    \Illuminate\Support\Facades\Mail::raw('Correo de prueba de Mailpit', function ($message) {
        $message->to('test@example.com')->subject('Mailpit funcionando');
    });

    $this->info('Correo de prueba enviado. Revisa Mailpit en http://127.0.0.1:8025.');
})->purpose('Envía un correo de prueba usando la configuración actual.');
