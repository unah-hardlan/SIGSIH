<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ConfigurarPerfilClienteRequest;
use App\Models\Persona;
use App\Models\Genero;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    /**
     * Muestra la vista de configuración inicial del perfil para nuevos usuarios.
     */
    public function configurarPerfil(): View
    {
        $generos = Genero::all();
        return view('cliente.configurar-perfil', compact('generos'));
    }

    /**
     * Guarda la configuración inicial del perfil del cliente.
     */
    public function configurarPerfilStore(ConfigurarPerfilClienteRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            
            // Verificar si ya existe una persona para este usuario
            $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            
            $data = $request->validated();
            
            // Manejar la subida del avatar si existe
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = 'avatar_' . $user->id_usuario_pk . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');
                $data['avatar_path'] = $avatarPath;
            }
            
            $data['id_usuario_fk'] = $user->id_usuario_pk;
            
            if ($persona) {
                // Si hay un avatar anterior y se sube uno nuevo, eliminar el anterior
                if ($request->hasFile('avatar') && $persona->avatar_path) {
                    Storage::disk('public')->delete($persona->avatar_path);
                }
                // Actualizar persona existente
                $persona->update($data);
            } else {
                // Crear nueva persona
                Persona::create($data);
            }

            DB::commit();

            return redirect()->route('cliente.perfil')->with('success', 'Perfil configurado correctamente. ¡Bienvenido!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al guardar la información del perfil. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Muestra la vista de perfil del cliente.
     */
    public function perfil(): View
    {
        $user = auth()->user();
        $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->with('genero')->first();
        
        return view('cliente.perfil', compact('persona'));
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
