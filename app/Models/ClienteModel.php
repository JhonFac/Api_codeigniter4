<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model{

    protected $table        = 'usuario';
    protected $primaryKey   = 'id';

    protected $returnType    = 'array';
    protected $allowedFields =['nombre', 'username', 'password','rol_id'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';    

    protected $validationRules  =[];
    protected $validationMessages  =[];
    protected $skipValidation     = false;


}
class ClienteModel extends Model{

    protected $table        = 'cliente';
    protected $primaryKey   = 'id';

    protected $returnType    = 'array';
    protected $allowedFields =['nombre', 'apellido', 'telefono','correo'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules  =[ 
        'nombre'             => 'required|alpha_space|min_length[3]|max_length[15]',
        'apellido'           => 'required|alpha_space|min_length[3]|max_length[15]',
        'telefono'           => 'required|alpha_numeric_space|min_length[10]|max_length[10]',
        'correo'             => 'required|permit_empty|valid_email|max_length[90]',
    ]; 

    protected $validationMessages  =[
        'correo'           =>[ 
            'valid_email' => 'el correo no es valido'
        ]
    ]; 

    protected $skipValidation     = false;

}

?>