<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use \App\Models\User;

use JWTAuth;

class AuthController extends Controller
{
    /**
     * Criando uma nova instancia para AuthController
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('jwt.verify', ['except'=>['login', 'register']]);
        $this->middleware('jwt.xauth', ['except'=>['login', 'register', 'refresh']]);
        $this->middleware('jwt.xrefresh', ['only'=>['refresh']]);
    }

    /**
     * Validando credenciais do usuário
     *
     * @return token|\Illuminate\Http\JsonResponse
     */
    public function login(){
        /**
         * armazenando as credenciais
         */
        $credentials = request()->only(['email', 'password']);

        /**
         * Verificando se usuário existe
         */
        if(!$access_token = auth()->claims(['xtype'=>'auth'])->attempt($credentials)){
            return response()->json(['error'=>"Usuário não autorizado"], 401);
        }

        return $this->respondWithToken($access_token);
    }

    /**
     * Retornando dados do usuário logado
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(){
        return response()->json(auth()->user());
    }

    /**
     * Deslogando Usuário
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        auth()->logout();

        return response()->json(['message'=>'Deslogado com sucesso!']);
    }

    /**
     * Atualizar o token comparando com o token anterior para evitar  "identity spoofing"
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(){
        $access_token = auth()->claims(['xtype'=>'auth'])->refresh(true, true);
        auth()->setToken($access_token);

        return $this->respondWithToken($access_token);
    }

    /**
     * Pegando o array da estrutura do token
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($access_token){
        return response()->json([
            'access_token'=>$access_token,
            'token_type'=>'bearer',
            'access_expires_in'=>auth()->factory()->getTTL() * 60,
            'expire' => now()->timestamp + (auth()->factory()->getTTL() * 60),
            'refresh_token'=>auth()
                        ->claims([
                            'xtype'=>'refresh',
                            'xpair'=>auth()->payload()->get('jti')
                        ])
                        ->setTTL(auth()->factory()->getTTL() * 3)
                        ->tokenById(auth()->user()->id),
                    'refresh_expires_in'=>auth()->factory()->getTTL() * 60
        ]);
    }


    public function register(){

        $messages = [
            'required' => 'O :attribute é obrigatório.',
            'max' => 'O :attribute Não pode ultrapassar :max.',
            'min' => "O :attribute tem que ser maior que :min.",
            'confirmed'=> "A confirmação da senha deve ser igual a senha!",
            'unique' => "Já existe este :attribute cadastrado no sistema!"
        ];


        $validator = Validator::make(request()->all(), [
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|max:255|unique:users',
            'password'=>'required|string|min:6|confirmed',
        ], $messages);

        if($validator->fails()){
            return response()->json([
                'status'=>'error',
                'success'=>false,
                'error'=>$validator->errors()->toArray()
            ], 400);
        }

        $user = User::create([
            'name'=>request()->input('name'),
            'email'=>request()->input('email'),
            'password'=>Hash::make(request()->input('password'))
        ]);

        return response()->json([
            'message'=>'Usuário criado!',
            'user'=>$user
        ]);
    }
}
