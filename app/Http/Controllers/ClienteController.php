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
use App\Models\Contacto;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Models\Direccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SpaHelper;

class ClienteController extends Controller
{
    /**
     * Viewer HTML de cotización para el portal de cliente con endpoints propios.
     * Sustituye a Cliente\CotizacionViewerController.
     */
    public function cotizacionViewer(\Illuminate\Http\Request $request)
    {
        $base = [
            'cot' => url('/cliente/cotizaciones/{id}/data'),
            'items' => url('/cliente/cotizaciones/{id}/items'),
        ];
        return view('admin.detalle-cotizacion', [
            'COTI_ENDPOINTS' => $base,
        ]);
    }

    public function configurarPerfil(): View
    {
        $generos = Genero::all();
        return view('cliente.configurar-perfil', compact('generos'));
    }


    public function configurarPerfilStore(ConfigurarPerfilClienteRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();

            $data = $request->validated();

            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = 'avatar_' . $user->id_usuario_pk . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');
                $data['avatar_path'] = $avatarPath;
            }

            $data['id_usuario_fk'] = $user->id_usuario_pk;

            if ($persona) {
                if ($request->hasFile('avatar') && $persona->avatar_path) {
                    Storage::disk('public')->delete($persona->avatar_path);
                }
                $persona->update($data);
            } else {
                $persona = Persona::create($data);
            }

            try {
                $clientePersona = DB::table('tbl_cliente_persona')
                    ->where('id_persona_fk', $persona->id_persona_pk)
                    ->first();

                if ($clientePersona) {
                    $cliente = Cliente::find($clientePersona->id_cliente_fk);
                } else {
                    $cliente = Cliente::create([
                        'tipo_cliente' => 'persona',
                        'estado_cliente' => 'activo',
                        'fecha_registro' => now(),
                    ]);

                    DB::table('tbl_cliente_persona')->insert([
                        'id_cliente_fk' => $cliente->id_cliente_pk,
                        'id_persona_fk' => $persona->id_persona_pk,
                    ]);
                }

                $emailContacto = $request->input('email_contacto');
                if ($emailContacto) {
                    Contacto::updateOrCreate(
                        [
                            'id_cliente_fk' => $cliente->id_cliente_pk,
                            'tipo_contacto' => 'email',
                        ],
                        [
                            'valor_contacto' => $emailContacto,
                        ]
                    );
                }
            } catch (\Throwable $e) {
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


    public function perfil()
    {
        $user = auth()->user();
        $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->with('genero')->first();
        $generos = Genero::all();

        $correoContacto = null;
        $cliente = null;

        if ($persona) {
            $clientePersona = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->first();

            if ($clientePersona) {
                $correoContacto = Contacto::where('id_cliente_fk', $clientePersona->id_cliente_fk)
                    ->where('tipo_contacto', 'email')
                    ->value('valor_contacto');

                $cliente = Cliente::find($clientePersona->id_cliente_fk);
            }
        }

        $empresa = null;
        $empresaDireccion = null;
        if ($cliente && $cliente->tipo_cliente === 'empresa') {
            $empresa = EmpresaCliente::where('id_cliente_fk', $cliente->id_cliente_pk)->first();


            $agencia = $cliente->agencias()
                ->with(['direccion.ciudad.departamento.pais'])
                ->first();

            if ($agencia && $agencia->direccion) {
                $dir = $agencia->direccion;
                $ciudad = $dir->ciudad;
                $departamento = $ciudad?->departamento;
                $pais = $departamento?->pais;

                $empresaDireccion = [
                    'calle' => $dir->calle,
                    'numero' => $dir->numero,
                    'colonia' => $dir->colonia,
                    'codigo_postal' => $dir->codigo_postal,
                    'referencia' => $dir->referencia,
                    'ciudad' => $ciudad?->nombre_ciudad,
                    'departamento' => $departamento?->nombre_departamento,
                    'pais' => $pais?->nombre_pais,
                    'formateada' => trim(($dir->calle . ' ' . $dir->numero . ', ' . $dir->colonia . ', ' . ($ciudad?->nombre_ciudad ?? '') . ', ' . ($departamento?->nombre_departamento ?? '') . ', ' . ($pais?->nombre_pais ?? '') . ' CP ' . $dir->codigo_postal))
                ];
            }
        }


        $personaData = $persona ? [
            'primer_nombre' => $persona->primer_nombre ?? '',
            'segundo_nombre' => $persona->segundo_nombre ?? '',
            'primer_apellido' => $persona->primer_apellido ?? '',
            'segundo_apellido' => $persona->segundo_apellido ?? '',
            'dni' => $persona->dni ?? '',
            'id_genero_fk' => $persona->id_genero_fk ?? '',
            'correo_contacto' => $correoContacto ?? ''
        ] : [
            'primer_nombre' => '',
            'segundo_nombre' => '',
            'primer_apellido' => '',
            'segundo_apellido' => '',
            'dni' => '',
            'id_genero_fk' => '',
            'correo_contacto' => $correoContacto ?? ''
        ];

        return SpaHelper::clienteView('cliente.perfil', compact('persona', 'empresa', 'generos', 'correoContacto', 'personaData', 'empresaDireccion'));
    }


    public function ordenes()
    {
        return SpaHelper::clienteView('cliente.ordenes');
    }


    public function facturas()
    {
        return SpaHelper::clienteView('cliente.facturas');
    }


    public function cotizaciones()
    {
        return SpaHelper::clienteView('cliente.cotizaciones');
    }



    public function tickets()
    {
        return SpaHelper::clienteView('cliente.tickets');
    }


    public function solicitudes()
    {

        $correoContacto = null;
        $user = auth()->user();
        $persona = $user?->persona;

        if ($persona) {
            $clientePersona = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->first();

            if ($clientePersona) {
                $correoContacto = Contacto::where('id_cliente_fk', $clientePersona->id_cliente_fk)
                    ->where('tipo_contacto', 'email')
                    ->value('valor_contacto');
            }
        }

        return SpaHelper::clienteView('cliente.solicitudes', compact('correoContacto'));
    }


    public function configurarEmpresa(): View
    {
        $paises = Pais::orderBy('nombre_pais')->get();
        $departamentos = Departamento::with('pais')->orderBy('nombre_departamento')->get();
        $ciudades = Ciudad::with('departamento.pais')->orderBy('nombre_ciudad')->get();

        return view('cliente.configurar-empresa', compact('paises', 'departamentos', 'ciudades'));
    }


    public function configurarEmpresaStore(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre_comercial' => 'required|string|max:150',
            'razon_social' => 'nullable|string|max:150',
            'rtn' => 'required|string|max:30|unique:tbl_cliente_empresa,rtn',
            'descripcion_empresa' => 'nullable|string|max:500',

            'id_pais_fk' => 'required|exists:tbl_pais,id_pais_pk',
            'id_departamento_fk' => 'required|exists:tbl_departamento,id_departamento_pk',
            'id_ciudad_fk' => 'required|exists:tbl_ciudad,id_ciudad_pk',

            'calle' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'colonia' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:10',
            'referencia' => 'required|string',
            'horario_atencion' => [
                'nullable',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidHorarioFormat($value)) {
                        $fail('El formato del horario no es válido. Ejemplos: "L-V 8:00 AM-5:00 PM", "L-S 9:00 AM-6:00 PM", "24 horas"');
                    }
                }
            ],
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'email_contacto' => 'required|email|max:255',
        ], [
            'rtn.required' => 'El RTN es obligatorio.',
            'rtn.unique' => 'Ya existe una empresa registrada con este RTN.',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();



            $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona) {
                $persona = \App\Models\Persona::create([
                    'id_usuario_fk' => $user->id_usuario_pk,
                    'primer_nombre' => $request->nombre_comercial,
                    'primer_apellido' => 'Empresa',

                    'dni' => 'EMPRESA-' . $user->id_usuario_pk,
                    'id_genero_fk' => 1,
                ]);
            }

            $clientePersona = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->first();

            if ($clientePersona) {
                $cliente = Cliente::find($clientePersona->id_cliente_fk);
                $cliente->update(['tipo_cliente' => 'empresa']);
            } else {
                $cliente = Cliente::create([
                    'tipo_cliente' => 'empresa',
                    'estado_cliente' => 'activo',
                    'fecha_registro' => now()
                ]);

                DB::table('tbl_cliente_persona')->insert([
                    'id_cliente_fk' => $cliente->id_cliente_pk,
                    'id_persona_fk' => $persona->id_persona_pk,
                ]);
            }

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars/empresas', 'public');
            }

            $empresaData = [
                'id_cliente_fk' => $cliente->id_cliente_pk,
                'nombre_comercial' => $request->nombre_comercial,
                'razon_social' => $request->razon_social,
                'rtn' => $request->rtn,
                'descripcion_empresa' => $request->descripcion_empresa,
                'horario_atencion' => $request->horario_atencion,
            ];

            if ($avatarPath) {
                $empresaData['avatar'] = $avatarPath;
            }

            EmpresaCliente::updateOrCreate(
                ['id_cliente_fk' => $cliente->id_cliente_pk],
                $empresaData
            );


            $direccion = Direccion::create([
                'id_ciudad_fk' => $request->id_ciudad_fk,
                'calle' => $request->calle,
                'numero' => $request->numero,
                'colonia' => $request->colonia,
                'codigo_postal' => $request->codigo_postal,
                'referencia' => $request->referencia,
            ]);


            $agencia = $cliente->agencias()->first();
            $agenciaData = [
                'nombre_agencia' => $request->nombre_comercial,
                'horario_agencia' => $request->horario_atencion,
                'id_direccion_fk' => $direccion->id_direccion_pk,
            ];

            if ($agencia) {
                $agencia->update($agenciaData);
            } else {
                $agencia = \App\Models\Agencia::create($agenciaData);

                $cliente->agencias()->attach($agencia->id_agencias_pk);
            }

            $emailContacto = $request->input('email_contacto');
            if ($emailContacto) {
                Contacto::updateOrCreate(
                    [
                        'id_cliente_fk' => $cliente->id_cliente_pk,
                        'tipo_contacto' => 'email',
                    ],
                    [
                        'valor_contacto' => $emailContacto,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('cliente.perfil')
                ->with('success', 'Datos de empresa guardados correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                return back()
                    ->withInput()
                    ->withErrors(['rtn' => 'Ya existe una empresa registrada con este RTN.']);
            }

            return back()
                ->withInput()
                ->with('error', 'Error al guardar los datos de empresa.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al guardar los datos de empresa.');
        }
    }


    private function isValidHorarioFormat($horario): bool
    {
        $horario = trim($horario);

        if ($horario === '') {
            return true;
        }

        if (preg_match('/^24\s*horas?$/i', $horario) || preg_match('/^cerrado$/i', $horario)) {
            return true;
        }

        $timePattern = '\\d{1,2}:\\d{2}(?:\\s*(?:[AaPp]\\.?[Mm]\\.?))?';
        $daysPattern = '[LMXJVSD](?:\\s*-\\s*[LMXJVSD])?(?:\\s*,\\s*[LMXJVSD](?:\\s*-\\s*[LMXJVSD])?)*';

        if (!preg_match('/^(' . $daysPattern . ')\\s+' . $timePattern . '\\s*-\\s*' . $timePattern . '$/i', $horario, $matches)) {
            return false;
        }

        $daysString = $matches[1] ?? null;
        if (!$daysString) {
            return false;
        }

        $dayOrder = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];

        foreach (array_map('trim', explode(',', $daysString)) as $segment) {
            if ($segment === '') {
                return false;
            }

            if (strpos($segment, '-') !== false) {
                [$start, $end] = array_map('trim', explode('-', $segment));
                if (!in_array($start, $dayOrder, true) || !in_array($end, $dayOrder, true)) {
                    return false;
                }

                if (array_search($start, $dayOrder, true) > array_search($end, $dayOrder, true)) {
                    return false;
                }
            } else {
                if (!in_array($segment, $dayOrder, true)) {
                    return false;
                }
            }
        }

        if (!$this->validateTimeValues($horario)) {
            return false;
        }

        return true;
    }


    public function perfilUpdate(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'primer_nombre' => 'required|string|max:50',
                'segundo_nombre' => 'nullable|string|max:50',
                'primer_apellido' => 'required|string|max:50',
                'segundo_apellido' => 'nullable|string|max:50',
                'dni' => 'required|string|max:20',
                'id_genero_fk' => 'nullable|exists:tbl_genero,id_genero_pk',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            DB::beginTransaction();

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

            if ($request->hasFile('avatar')) {
                if ($persona->avatar_path && Storage::disk('public')->exists($persona->avatar_path)) {
                    Storage::disk('public')->delete($persona->avatar_path);
                }

                $avatar = $request->file('avatar');
                $avatarName = 'avatar_' . $user->id_usuario_pk . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');
                $data['avatar_path'] = $avatarPath;
            }

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


    public function empresaUpdate(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'nombre_comercial' => 'required|string|max:150',
                'razon_social' => 'nullable|string|max:150',
                'rtn' => 'nullable|string|max:30',
                'descripcion_empresa' => 'nullable|string|max:500',
                'horario_atencion' => [
                    'nullable',
                    'string',
                    'max:500',
                    function ($attribute, $value, $fail) {
                        if ($value && !$this->isValidHorarioFormat($value)) {
                            $fail('El formato del horario no es válido. Ejemplos: "L-V 8:00 AM-5:00 PM", "L-D 9:00 AM-6:00 PM", "24 horas"');
                        }
                    }
                ],
                'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'email_contacto' => 'sometimes|email|max:255',

                'calle' => 'required|string|max:100',
                'numero' => 'required|string|max:20',
                'colonia' => 'required|string|max:100',
                'codigo_postal' => 'required|string|max:10',
                'referencia' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona) {
                return response()->json(['success' => false, 'message' => 'Perfil no encontrado'], 404);
            }

            $clientePersona = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->first();

            if (!$clientePersona) {
                return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            }

            $cliente = Cliente::find($clientePersona->id_cliente_fk);
            if (!$cliente) {
                return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            }

            $empresa = EmpresaCliente::where('id_cliente_fk', $cliente->id_cliente_pk)->first();
            if (!$empresa) {
                return response()->json(['success' => false, 'message' => 'Empresa no configurada'], 404);
            }

            $data = $request->only(['nombre_comercial', 'razon_social', 'rtn', 'descripcion_empresa', 'horario_atencion']);


            if ($request->hasFile('avatar')) {
                if ($empresa->avatar && Storage::disk('public')->exists($empresa->avatar)) {
                    Storage::disk('public')->delete($empresa->avatar);
                }
                $file = $request->file('avatar');
                $name = 'empresa_' . $cliente->id_cliente_pk . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('avatars/empresas', $name, 'public');
                $data['avatar'] = $path;
            }

            $empresa->update($data);

            if ($request->filled('email_contacto')) {
                $email = $request->input('email_contacto');
                Contacto::updateOrCreate(
                    [
                        'id_cliente_fk' => $cliente->id_cliente_pk,
                        'tipo_contacto' => 'email',
                    ],
                    [
                        'valor_contacto' => $email,
                    ]
                );
            }


            if ($request->filled(['calle', 'numero', 'colonia', 'codigo_postal', 'referencia'])) {

                $agencia = $cliente->agencias()->first();

                if ($agencia && $agencia->id_direccion_fk) {

                    $direccion = \App\Models\Direccion::find($agencia->id_direccion_fk);
                    if ($direccion) {
                        $direccion->update([
                            'calle' => $request->input('calle'),
                            'numero' => $request->input('numero'),
                            'colonia' => $request->input('colonia'),
                            'codigo_postal' => $request->input('codigo_postal'),
                            'referencia' => $request->input('referencia'),
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Empresa actualizada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al actualizar la empresa: ' . $e->getMessage()], 500);
        }
    }


    private function validateTimeValues($horario): bool
    {
        preg_match_all('/\d{1,2}:\d{2}/', $horario, $matches);

        foreach ($matches[0] as $time) {
            [$hour, $minute] = explode(':', $time);
            $hour = (int) $hour;
            $minute = (int) $minute;

            if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
                return false;
            }
        }

        return true;
    }


    public function getDepartamentosByPais($paisId)
    {
        try {

            $departamentos = \App\Models\Departamento::where('id_pais_pk', $paisId)
                ->orderBy('nombre_departamento')
                ->get(['id_departamento_pk', 'nombre_departamento']);

            return response()->json($departamentos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar departamentos'], 500);
        }
    }


    public function getCiudadesByDepartamento($departamentoId)
    {
        try {
            $ciudades = \App\Models\Ciudad::where('id_departamento_fk', $departamentoId)
                ->orderBy('nombre_ciudad')
                ->get(['id_ciudad_pk', 'nombre_ciudad']);

            return response()->json($ciudades);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar ciudades'], 500);
        }
    }


    public function validarDni(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|min:6|max:20'
        ]);

        $dni = $request->input('dni');


        $exists = Persona::where('dni', $dni)->exists();

        return response()->json([
            'disponible' => !$exists,
            'mensaje' => $exists ? 'Este DNI ya está registrado' : 'DNI disponible'
        ]);
    }
}
