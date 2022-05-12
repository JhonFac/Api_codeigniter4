<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
// use App\Filters\LogFilter;
use App\Filters\Cors;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array
     */
    public $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'authfilter'    => LogFilter::class,
        'cors'          => Cors::class,  
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array
     */
    public $globals = [
        'before' => [
            'cors'
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['csrf', 'throttle']
     *
     * @var array
     */
    public $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array
     */
    public $filters = [
        'authfilter'=> [ 'before' => [ 'api/*' ]]
        // 'cors' => ['before' => ['api/*','/*']],
    ];
}


use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\Key;

class LogFilter implements FilterInterface{

	use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null){
        try {

            $key = Services::getSecretKey();
            $authheader =$request->getServer('HTTP_AUTHORIZATION');

            if ($authheader==null)
                return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'El JWT no es valido');
            
            $arr = explode(' ', $authheader);
            $jwt = $arr[1];
            
            JWT::decode($jwt, new Key($key, 'HS256'));

        } catch (ExpiredException $e) {
            $response = [
                'message' => 'El token a expirad',
                'token' => 'ExpiredToken'
            ];
            return $this->respond($response, 200);
            
            // return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'Token Expiro');
        } catch ( SignatureInvalidException $e) {
            return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'error interno');
        } catch ( BeforeValidException $e) {
            return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'BeforeValidException');
        } catch (\Exception $e) {
            return Services::response()->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Ocurrio un error en el servidor');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){

    }
}
