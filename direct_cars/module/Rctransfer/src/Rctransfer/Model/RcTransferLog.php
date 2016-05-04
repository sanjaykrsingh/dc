<?php
namespace Rctransfer\Model;

class RcTransferLog
{
	public $log_id;	
    public $rc_transfer_id;
	public $user_type;
   	public $email;
	public $mobile;	
	public $message;	
	public $created_on;
	
    public function exchangeArray($data)
    {
		$this->log_id			= (isset($data['log_id'])) ? $data['log_id'] : null;
		$this->rc_transfer_id	= (isset($data['rc_transfer_id'])) ? $data['rc_transfer_id'] : null;
		$this->user_type		= (isset($data['user_type'])) ? $data['user_type'] : null;
		$this->email			= (isset($data['email'])) ? $data['email'] : null;
		$this->mobile			= (isset($data['mobile'])) ? $data['mobile'] : null;
		$this->message			= (isset($data['message'])) ? $data['message'] : null;
		$this->created_on		= (isset($data['created_on'])) ? $data['created_on'] : null;
    }
}