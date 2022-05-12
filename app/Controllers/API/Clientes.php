<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ClienteModel;

class Clientes extends ResourceController
{

    public function __construct() {
        $this->model = $this->setModel(new ClienteModel());
    }

	public function GetUser($id=null){

		$finduser = $this->model->find($id);
		if($finduser==null){
			return $this->failValidationError('No se econtro o no es valido el Id');
		}else{
			return $this->respond($finduser)->getJSON();
		}
	}

	public function cosult_all(){
		$clientes = $this->model->findAll();
		return $this->respond($clientes);
	}

	public function deleteuser($id=null){
		try {

			if ($id==null) {
				return $this->failValidationError('No se econtro o no es valido el Id');
			}

			$finduser = $this->model->find($id);
			if ($finduser==null) {
				return $this->failNotFound('El id:'.$id.' no existe');
			}

			if ($this->model->delete($id)) {
				return $this->respondDeleted($finduser);
			}
			else{
				return $this->failServerError('El registro no se borro correctamente');
			}

		} catch (\Exception $e) {
			return $this->failServerError('Error en el servidor');
		}



	}

	public function modify(){
		try {
			$user = $this->request->getJSON();
			$data = (array) $user;
			$ID=$data['id'];

			if ($ID==null) {
				return $this->failValidationError('No se econtro o no es valido el Id');
			}

			$finduser = $this->model->find($ID);

			if ($finduser==null) {
				return $this->failNotFound('El id:'.$ID.' no existe');
			}

			if ($this->model->update($ID,$user)) {
				return $this->respondUpdated($user);
			}
			else{
				return $this->failValidationError($this->model->validation->listErrors());
			}

			return $this->respond($user);

		} catch (\Exception $e) {
			return $this->failServerError('Error en el servidor');
		}
	}

	public function crear(){

		try {
			$cliente = $this->request->getJSON();
			$date = explode("-", $cliente->{'created_at'});
			if ($date[0]>'1992' and $date[0]<'2005'){
				if ($this->model->insert($cliente)):
					$cliente->id = $this->model->insertID();
					return $this->respondCreated($cliente);
				else:
					return $this->failValidationError($this->model->validation->listErrors());
				endif;
			}
			else{
				$msg = array(
					"status"=> 400,
					"statusstatus"=> 400,
					"messages"=> array(
						"error"=> "<div class=\"errors\" role=\"alert\">El campo Fecha debe esatar entre 1992 al 2005</div>"
					)
				);
				return $this->respondCreated($msg);
			}



		} catch (\Exception $e) {
			return $this->failServerError('Error en el servidor');
		}


	}

}
