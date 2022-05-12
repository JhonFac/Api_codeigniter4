<?php

namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use CodeIgniter\API\ResponseTrait;


class LogFilter implements FilterInterface{

	use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null){
        try {

            $key = Services::getSecretKey();
            $authheader =$request->getServer('HTTP_AUTHORIZATION');
            echo $authheader;

            if ($authheader==null)
                return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'El JWT no es valido');
            
            $arr = explode(' ', $authheader);
            $jwt = $arr[1];

            JWT::decode($jwt,$key, 'HS256 ');

        } catch (\ExpiredException $e) {
        	// return $this->respond($jwt, 200);
            return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'Su token expiro');
        } catch (\Exception $e) {
            return Services::response()->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Ocurrio un error en el servidor');
        }

    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){


    }
}