<?php
namespace Mmv\Model;

class MakeMaster
{
    public $make_id;
    public $make_name;	

    public function exchangeArray($data)
    {
		$this->make_id		= (isset($data['make_id'])) ? $data['make_id'] : null;
		$this->make_name	= (isset($data['make_name'])) ? $data['make_name'] : null;
    }
}