<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UsuarioModel;
use App\Models\ClienteModel;
use CodeIgniter\API\ResponseTrait;
use Config\Services;
use Firebase\JWT\JWT;

class Home extends ResourceController{

	use ResponseTrait;

    public function __construct() {
        $this->model = $this->setModel(new ClienteModel());
    }

	public function index()
	{
		$clientes = $this->model->findAll();
		return $this->respond($clientes);
	}

	public function JWT($user){

		$user=$user['0'];
		
		$key = Services::getSecretKey();
		$time = time();
		$payload=[
			'aud'=>base_url(),
			'iat'=>$time,
			'exp'=>$time+(60*600),
			'data'=>[
				'nombre'=>$user['nombre'],
				'username'=>$user['username'],
				'rol'=>$user['rol_id']
			]
		];
		$token = JWT::encode($payload, $key,'HS256');
        $response = [
            'message' => 'Login Succesful',
            'token' => $token
        ];
		return $response;
	}

	public function log(){
        try {
			$token='';
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

			if ($password== '' or $username== '') {
				$response = [
					'message' => 'credenciales vacias',
					'token' => 'credenciales vacias'
				];
				return $this->respond($response, 200);
            }


            $UserModel = new UsuarioModel();
            $where = ['username' => $username, 'password' => $password];
            $valUser = $UserModel->where($where)->find();

            if ($valUser== null) {
				$response = [
					'message' => 'invalid username or password',
					'token' => 'credenciales'
				];
				return $this->respond($response, 200);
            }

			$jwt=$this->JWT($valUser);
            // return $this->respond($jwt);
        	return $this->respond($jwt, 200);

		} catch (\Exception $e) {
			return $this->failServerError('Error en el servidor');
		}
	}
}
