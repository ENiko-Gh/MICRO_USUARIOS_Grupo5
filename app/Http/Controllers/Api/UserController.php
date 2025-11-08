<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException; // Importar para capturar errores SQL

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        // Validación de campos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            // CRÍTICO: Asegúrate que el campo exista en tu tabla. Si es 'historial_medico' úsalo.
            'fecha_nacimiento' => 'required|date', 
            'sexo' => 'required|in:Masculino,Femenino,Otro',
            'numero_seguro' => 'required|string|max:50',
            'historial_medico' => 'nullable|string', // Cambié a 'required' a 'nullable' según tu JSON anterior.
            'contacto_emergencia' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Se usa Hash::make() aquí, lo cual es correcto si no usas $casts = ['password' => 'hashed']
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'sexo' => $request->sexo,
                'numero_seguro' => $request->numero_seguro,
                'historial_medico' => $request->historial_medico,
                'contacto_emergencia' => $request->contacto_emergencia,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
            
        } catch (QueryException $e) {
             // 💥 DIAGNÓSTICO FORZADO: Muestra el error SQL real.
             // Esto nos dirá si es un campo faltante o un error de columna/tabla.
             return response()->json([
                'status' => 'error',
                'message' => 'Database error during creation. Check migration file.',
                'sql_error' => $e->getMessage() // <-- ESTE ES EL MENSAJE VITAL
            ], 500);
        } catch (\Exception $e) {
            // Captura cualquier otro error de aplicación (ej. "Clase no encontrada")
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error.',
                'detailed_error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$id,
            'password' => 'sometimes|string|min:8',
            'fecha_nacimiento' => 'sometimes|date',
            'sexo' => 'sometimes|in:Masculino,Femenino,Otro',
            'numero_seguro' => 'sometimes|string|max:50',
            'contacto_emergencia' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update(array_filter([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : null,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'numero_seguro' => $request->numero_seguro,
            'historial_medico' => $request->historial_medico,
            'contacto_emergencia' => $request->contacto_emergencia,
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }
}