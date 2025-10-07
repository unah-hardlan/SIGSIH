<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ConfigurarPerfilClienteRequest;
use App\Models\Persona;
use App\Models\Genero;
use App\Models\Cliente;
use App\Models\EmpresaCliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SpaHelper;

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
    public function perfil()
    {
        $user = auth()->user();
        $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->with('genero')->first();
        $generos = Genero::all();
        
        // Verificar si también tiene datos de empresa
        $cliente = Cliente::where('id_cliente_pk', $user->id_usuario_pk)->first();
        $empresa = null;
        
        if ($cliente && $cliente->tipo_cliente === 'empresa') {
            $empresa = EmpresaCliente::where('id_cliente_fk', $cliente->id_cliente_pk)->first();
        }
        
        return SpaHelper::clienteView('cliente.perfil', compact('persona', 'empresa', 'generos'));
    }

    /**
     * Muestra las órdenes de servicio del cliente.
     */
    public function ordenes()
    {
        return SpaHelper::clienteView('cliente.ordenes');
    }

    /**
     * Muestra las facturas del cliente.
     */
    public function facturas()
    {
        return SpaHelper::clienteView('cliente.facturas');
    }

    /**
     * Muestra las cotizaciones del cliente.
     */
    public function cotizaciones()
    {
        return SpaHelper::clienteView('cliente.cotizaciones');
    }

    /**
     * Muestra las solicitudes de soporte del cliente.
     */
    public function solicitudes()
    {
        return SpaHelper::clienteView('cliente.solicitudes');
    }

    /**
     * Muestra la vista de configuración de empresa.
     */
    public function configurarEmpresa(): View
    {
        return view('cliente.configurar-empresa');
    }

    /**
     * Guarda los datos de la empresa.
     */
    public function configurarEmpresaStore(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre_comercial' => 'required|string|max:150',
            'razon_social' => 'nullable|string|max:150',
            'rtn' => 'nullable|string|max:30',
            'descripcion_empresa' => 'nullable|string|max:255',
            'horario_atencion' => [
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidHorarioFormat($value)) {
                        $fail('El formato del horario no es válido. Ejemplos: "L-V 8:00-17:00", "L-S 9:00-18:00", "24 horas"');
                    }
                }
            ],
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            
            // Crear o actualizar cliente
            $cliente = Cliente::firstOrCreate(
                ['id_cliente_pk' => $user->id_usuario_pk],
                [
                    'tipo_cliente' => 'empresa',
                    'estado_cliente' => 'activo',
                    'fecha_registro' => now()
                ]
            );

            // Para empresas, crear un registro de persona mínimo para satisfacer el middleware
            // Usar el nombre comercial como nombre de la persona representante
            $persona = \App\Models\Persona::firstOrCreate(
                ['id_usuario_fk' => $user->id_usuario_pk],
                [
                    'primer_nombre' => $request->nombre_comercial,
                    'primer_apellido' => 'Empresa',
                    'dni' => $request->rtn ?: 'EMPRESA-' . $user->id_usuario_pk,
                    'id_genero_fk' => 1, // Valor por defecto, puede ajustarse
                ]
            );

            // Subir avatar si existe
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars/empresas', 'public');
            }

            // Crear o actualizar datos de empresa
            $empresaData = [
                'id_cliente_fk' => $cliente->id_cliente_pk,
                'nombre_comercial' => $request->nombre_comercial,
                'razon_social' => $request->razon_social,
                'rtn' => $request->rtn,
                'descripcion_empresa' => $request->descripcion_empresa,
                'horario_atencion' => $request->horario_atencion,
            ];

            // Solo agregar avatar si se subió uno
            if ($avatarPath) {
                $empresaData['avatar'] = $avatarPath;
            }

            EmpresaCliente::updateOrCreate(
                ['id_cliente_fk' => $cliente->id_cliente_pk],
                $empresaData
            );

            DB::commit();

            return redirect()->route('cliente.perfil')
                ->with('success', 'Datos de empresa guardados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al guardar los datos de empresa: ' . $e->getMessage());
        }
    }

    /**
     * Valida el formato del horario de atención.
     */
    private function isValidHorarioFormat($horario): bool
    {
        $horario = trim($horario);
        
        // Si está vacío, es válido (campo opcional)
        if (empty($horario)) {
            return true;
        }
        
        // Patrones de validación para horarios
        $patterns = [
            // L-V 8:00-17:00 (rango de días con horario)
            '/^[LMXJVSD]-[LMXJVSD]\s+\d{1,2}:\d{2}-\d{1,2}:\d{2}$/',
            // L-V 8:00-12:00, 14:00-18:00 (con pausa de almuerzo)
            '/^[LMXJVSD]-[LMXJVSD]\s+\d{1,2}:\d{2}-\d{1,2}:\d{2},\s*\d{1,2}:\d{2}-\d{1,2}:\d{2}$/',
            // L 8:00-17:00 (día individual)
            '/^[LMXJVSD]\s+\d{1,2}:\d{2}-\d{1,2}:\d{2}$/',
            // L-V 8:00-12:00, S 9:00-13:00 (días diferentes)
            '/^[LMXJVSD]-[LMXJVSD]\s+\d{1,2}:\d{2}-\d{1,2}:\d{2},\s*[LMXJVSD]\s+\d{1,2}:\d{2}-\d{1,2}:\d{2}$/',
            // 24 horas
            '/^24\s*horas?$/i',
            // Cerrado
            '/^cerrado$/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $horario)) {
                // Validación adicional de horas (0-23) y minutos (0-59)
                if ($this->validateTimeValues($horario)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Actualiza el perfil personal del cliente.
     */
    public function perfilUpdate(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Validar datos básicos
            $request->validate([
                'primer_nombre' => 'required|string|max:50',
                'segundo_nombre' => 'nullable|string|max:50',
                'primer_apellido' => 'required|string|max:50',
                'segundo_apellido' => 'nullable|string|max:50',
                'dni' => 'required|string|max:15',
                'id_genero_fk' => 'nullable|exists:tbl_genero,id_genero_pk',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            DB::beginTransaction();

            // Buscar la persona del usuario
            $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            
            if (!$persona) {
                return response()->json(['success' => false, 'message' => 'Perfil no encontrado'], 404);
            }

            $data = $request->only([
                'primer_nombre',
                'segundo_nombre', 
                'primer_apellido',
                'segundo_apellido',
                'dni',
                'id_genero_fk'
            ]);

            // Manejar la subida del avatar si existe
            if ($request->hasFile('avatar')) {
                // Eliminar avatar anterior si existe
                if ($persona->avatar_path && Storage::disk('public')->exists($persona->avatar_path)) {
                    Storage::disk('public')->delete($persona->avatar_path);
                }

                $avatar = $request->file('avatar');
                $avatarName = 'avatar_' . $user->id_usuario_pk . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');
                $data['avatar_path'] = $avatarPath;
            }

            // Actualizar la persona
            $persona->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza los datos de la empresa del cliente.
     */
    public function empresaUpdate(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'nombre_comercial' => 'required|string|max:150',
                'razon_social' => 'nullable|string|max:150',
                'rtn' => 'nullable|string|max:30',
                'descripcion_empresa' => 'nullable|string|max:255',
                'horario_atencion' => [
                    'nullable','string','max:100',
                    function ($attribute,$value,$fail){
                        if ($value && !$this->isValidHorarioFormat($value)) {
                            $fail('El formato del horario no es válido.');
                        }
                    }
                ],
                'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048'
            ]);

            DB::beginTransaction();

            $cliente = Cliente::where('id_cliente_pk',$user->id_usuario_pk)->first();
            if (!$cliente) {
                return response()->json(['success'=>false,'message'=>'Cliente no encontrado'],404);
            }

            $empresa = EmpresaCliente::where('id_cliente_fk',$cliente->id_cliente_pk)->first();
            if (!$empresa) {
                return response()->json(['success'=>false,'message'=>'Empresa no configurada'],404);
            }

            $data = $request->only(['nombre_comercial','razon_social','rtn','descripcion_empresa','horario_atencion']);

            // Avatar
            if ($request->hasFile('avatar')) {
                if ($empresa->avatar && Storage::disk('public')->exists($empresa->avatar)) {
                    Storage::disk('public')->delete($empresa->avatar);
                }
                $file = $request->file('avatar');
                $name = 'empresa_'.$cliente->id_cliente_pk.'_'.time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('avatars/empresas',$name,'public');
                $data['avatar'] = $path;
            }

            $empresa->update($data);

            DB::commit();
            return response()->json(['success'=>true,'message'=>'Empresa actualizada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>'Error al actualizar la empresa: '.$e->getMessage()],500);
        }
    }

    /**
     * Valida que las horas y minutos estén en rangos correctos.
     */
    private function validateTimeValues($horario): bool
    {
        // Extraer todas las horas del formato HH:MM
        preg_match_all('/\d{1,2}:\d{2}/', $horario, $matches);
        
        foreach ($matches[0] as $time) {
            [$hour, $minute] = explode(':', $time);
            $hour = (int) $hour;
            $minute = (int) $minute;
            
            // Validar rango de horas (0-23) y minutos (0-59)
            if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
                return false;
            }
        }
        
        return true;
    }
}
