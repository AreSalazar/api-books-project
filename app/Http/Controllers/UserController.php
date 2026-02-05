<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //Register
    public function register(Request $request)
    {
        try {

            //Se validan los datos de la petición
            $request->validate([
                'name' => 'required|string|max:255|unique:users|alpha|min:8|regex:/^[A-Za-z]+$/',
                'email' => 'requited|string|email|max:255|unique:users',
                'password' => 'required|confirmed|min:8'
            ]);

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

        } catch (ValidationException $val) {
            return response()->json([
                'message' => 'invalid data',
                'error' => $val->getMessage()
            ], 422); //DATOS NO VÁLIDOS

        } catch (QueryException $exc) {
            return response()->json([
                'message' => 'Database error registering user',
                'error' => $exc->getMessage()
            ], 409); //CONFLICTO

        } catch (Exception $error) {
            return response()->json([
                'message' => 'Failed to register user',
                'error' => $error->getMessage()
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
            $credentials = $request->only('email', 'password');

            //Autenticar al usuario con las credenciales
            if (Auth::attempt($credentials)) { //Auth::attempt devuelve true o flase, dependiendo si las credenciales son correctas

                //Credenciales correctas, se obtiene el usuario
                $user = $request->user(); //$user = Auth::user();

                //Tiempo de expiración del token
                $expiration = Carbon::now()->addMinutes(30);
                //Generación de token
                $token = $user->createToken('auth_token', ['server:update'], $expiration)->plainTextToken;

                return response()->json([
                    'message' => 'User logged successfully',
                    'user' => $user,
                    'type_token' => 'Bearer', //Para postman
                    'token' => $token,
                    'status' => 200
                ], 200); //OK

            } else {
                return response()->json([
                    'message' => 'unauthorized',
                    'status' => 401
                ], 401); //No autorizado
            }
        } catch (ValidationException $val) {
            return response()->json([
                'message' => 'invalid data',
                'error' => $val->getMessage()
            ], 422); //DATOS NO VÁLIDOS

        } catch (Exception $error) {
            return response()->json([
                'message' => 'Failed to register user',
                'error' => $error->getMessage()
            ], 500); //ERROR EN EL SERVIDOR
        }
    }

    //Logout
    public function logout(Request $request)
    {
        try {
            //Se obtiene el usuario logueado
            $user = $request->user();

            //Revocar token, usuario debe generar otro
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'User logged successfully',
                'status' => 200
            ], 200); //OK

        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Failed to logged out',
                'error' => $exc->getMessage()
            ], 500); //ERROR DEL SERVIDOR
        }
    }
}
