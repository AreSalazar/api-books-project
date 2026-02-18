<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; //Librería para manejar fechas y horas (tokens y expiraciones)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    //Register
    public function register(Request $request)
    {
        try {
            //Se validan los datos de la petición
            $request->validate([
                'name' => 'required|string|min:3|max:255|unique:users|alpha',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|confirmed|min:8'
            ]); //Laravel devuelve automáticamente error 422 (Datos inválidos)

            //Se crea el registro de un nuevo usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password) //En la BD el password debe aparecer con caracteres aleatorios como $2y$12$dU0KCtwWcGgs, esto significa que está HASHEADO
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'status' => 'success'
            ], 201); //CREATED

        } catch (QueryException $exc) {
            return response()->json([
                'message' => 'Database error registering user',
                'error' => $exc->getMessage()
            ], 409); //CONFLICTO

        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Failed to register user',
                'error' => $th->getMessage()
            ], 500); //ERROR EN EL SERVIDOR
        }
    }

    //Login
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string|min:8'
            ]);

            //Se extrae (only) solo los datos que vamos a usar del body de la petición
            $credentials = $request->only('email', 'password'); //$credentials = $request->all());

            //Autenticar al usuario con las credenciales
            if (Auth::attempt($credentials)) { //Auth::attempt devuelve true o flase, dependiendo si las credenciales son correctas

                //Credenciales correctas, se obtiene el usuario
                $user = $request->user(); //$user = Auth::user();

                //Tiempo de expiración del token 
                $expiration = Carbon::now()->addMinutes(30);
                //Generación de token (Sanctum)
                $token = $user->createToken('auth_token', ['server:update'], $expiration)->plainTextToken; //sirve para roles/admin
                //$token=$user->createToken('authentication_token',[], $expiration)->plainTextToken; //sirve para usuarios normales

                return response()->json([
                    'message' => 'User logged successfully',
                    'user' => $user,
                    'type_token' => 'Bearer', //Para postman
                    'token' => $token,
                ], 200); //OK

            } else {
                return response()->json([
                    'message' => 'unauthorized',
                ], 401); //No autorizado
            }

        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Failed to register user',
                'error' => $th->getMessage()
            ], 500); //ERROR EN EL SERVIDOR
        }
    }

    //Logout
    public function logout(Request $request)
    {
        try {
            //Se obtiene el usuario logueado
            $user = $request->user();

            //Revocar token actual, usuario debe generar otro
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'User logged out successfully',
            ], 200); //OK

        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Failed to logged out',
                'error' => $th->getMessage()
            ], 500); //ERROR DEL SERVIDOR
        }
    }
}
