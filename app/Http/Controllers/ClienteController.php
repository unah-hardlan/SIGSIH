<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    /**
     * Muestra la vista de perfil del cliente.
     */
    public function perfil(): View
    {
        return view('cliente.perfil');
    }

    /**
     * Muestra las órdenes de servicio del cliente.
     */
    public function ordenes(): View
    {
        return view('cliente.ordenes');
    }

    /**
     * Muestra las facturas del cliente.
     */
    public function facturas(): View
    {
        return view('cliente.facturas');
    }

    /**
     * Muestra las cotizaciones del cliente.
     */
    public function cotizaciones(): View
    {
        return view('cliente.cotizaciones');
    }
}
