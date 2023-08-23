<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;


class AuthController extends Controller
{
    public function createUser(CreateUserRequest $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]
            );

            if($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $AccessToken = Str::random(80);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'AccessToken' => $AccessToken,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $AccessToken,
                'data' => $user,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

    public function loginUser(LoginRequest $request)
    {
         try {
            $validateUser = Validator::make(
                $request->all(),
                [
                'email' => 'required|email',
                'password' => 'required'
            ]
            );

            if($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'AccessToken' => $user -> AccessToken,
                //'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Create or Login User based on JSON data
     * @param Request $request
     * @return User
     */
    public function googleUser(Request $request)
    {
        try {
            // Obtener los datos del JSON recibido
            $jsonData = $request->json()->all();
            $email = $jsonData['email'];

            // Verificar si el usuario ya existe en la base de datos
            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                // Si el usuario ya existe, iniciar sesión
                $loginUser = $this->loginUser($request);

                return response()->json([
                    'status' => true,
                    'message' => 'User Logged In Successfully',
                    'token' => $loginUser['token']
                ], 200);
            } else {
                // Si el usuario no existe, crearlo
                $request->merge([
                    'name' => $jsonData['name'],
                    'password' => $jsonData['password']
                ]);

                $user = $this->createUser($request);

                return response()->json([
                    'status' => true,
                    'message' => 'User Created Successfully',
                    'token' => $user['token']
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }



    public function logout(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        try {
            $user = Auth::user();
            $user->tokens()->where('tokenable_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAuthUser(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $user = Auth::user();

        return response()->json(['user' => $user]);
    }

}
