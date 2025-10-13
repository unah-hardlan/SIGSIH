<?php

namespace App\Http\Controllers;

use App\Models\EstadoTicket;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEstadoTicketRequest;
use App\Http\Requests\UpdateEstadoTicketRequest;
use App\Http\Resources\EstadoTicketResource;

class EstadoTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = EstadoTicket::query();
        
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre','like',"%$q%")
                    ->orWhere('codigo','like',"%$q%")
                    ->orWhere('descripcion','like',"%$q%");
            });
        }
        
        $sortable = [
            'nombre' => 'nombre',
            'codigo' => 'codigo',
            'orden' => 'orden',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'orden',$direction);

        if($request->boolean('all')){
            return EstadoTicketResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return EstadoTicketResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreEstadoTicketRequest $request)
    {
        $estadoTicket = EstadoTicket::create($request->validated());
        return (new EstadoTicketResource($estadoTicket))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $estadoTicket = EstadoTicket::find($id);
        if(!$estadoTicket) return response()->json(['error'=>'Estado de Ticket no encontrado'],404);
        return (new EstadoTicketResource($estadoTicket))->response();
    }

    public function update(UpdateEstadoTicketRequest $request, $id)
    {
        $estadoTicket = EstadoTicket::find($id);
        if(!$estadoTicket) return response()->json(['error'=>'Estado de Ticket no encontrado'],404);
        $estadoTicket->update($request->validated());
        return (new EstadoTicketResource($estadoTicket))->response();
    }

    public function destroy($id)
    {
        $estadoTicket = EstadoTicket::find($id);
        if(!$estadoTicket) return response()->json(['error'=>'Estado de Ticket no encontrado'],404);
        $estadoTicket->delete();
        return response()->json(['message'=>'Estado de Ticket eliminado']);
    }
}