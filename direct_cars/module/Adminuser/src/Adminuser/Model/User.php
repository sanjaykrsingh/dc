<?php
namespace Adminuser\Model;

class User
{
    public $user_id;
    public $username;	
	public $email;
	public $display_name;	
	public $first_name;	
	public $last_name;
	public $password;	
	public $state;		
	public $is_admin;
	public $active;

    public function exchangeArray($data)
    {
		$this->user_id		= (isset($data['user_id'])) ? $data['user_id'] : null;
		$this->username	= (isset($data['username'])) ? $data['username'] : null;
		$this->email	= (isset($data['email'])) ? $data['email'] : null;
		$this->display_name		= (isset($data['display_name'])) ? $data['display_name'] : null;
		$this->first_name	= (isset($data['first_name'])) ? $data['first_name'] : null;
		$this->last_name		= (isset($data['last_name'])) ? $data['last_name'] : null;
		$this->password		= (isset($data['password'])) ? $data['password'] : null;
		$this->state		= (isset($data['state'])) ? $data['state'] : null;
		$this->is_admin		= (isset($data['is_admin'])) ? $data['is_admin'] : null;
		$this->active		= (isset($data['active'])) ? $data['active'] : null;
    }

}