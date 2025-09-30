<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
	public function up(): void
	{
		$param = 'Formato de DNI';
		$valor = '^[0-9]{4}-[0-9]{4}-[0-9]{5}$';
		$now = now();

		$exists = DB::table('tbl_parametros')->where('parametro', $param)->exists();
		if ($exists) {
			DB::table('tbl_parametros')->where('parametro', $param)->update([
				'valor' => $valor,
				'modificado_por' => 'system',
				'fecha_modificacion' => $now,
			]);
		} else {
			DB::table('tbl_parametros')->insert([
				'parametro' => $param,
				'valor' => $valor,
				'id_usuario_fk' => 1,
				'creado_por' => 'system',
				'fecha_creacion' => $now,
				'modificado_por' => 'system',
				'fecha_modificacion' => $now,
			]);
		}
	}

	public function down(): void
	{
		DB::table('tbl_parametros')->where('parametro', 'Formato de DNI')->delete();
	}
};

