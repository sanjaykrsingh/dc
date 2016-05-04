<?php
namespace Mmv\Model;

class ModelMaster
{
    public $model_id;
    public $model_name;	

    public function exchangeArray($data)
    {
		$this->model_id		= (isset($data['model_id'])) ? $data['model_id'] : null;
		$this->model_name	= (isset($data['model_name'])) ? $data['model_name'] : null;
    }
}