<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends MY_Model{
	public static $definitation= array(
		'table'=>'customer',
		'primary'=>'id',
		'fields'=>array(
			'fname'=>array('require'=>true, 'type'=>'string'),
			'lname'=>array('require'=>false, 'type'=>'string'),
			'email'=>array('require'=>true, 'type'=>'string'),			
			'password'=>array('require'=>true, 'type'=>'string'),
			'status'=>array('type'=>'integer'),
			'date'=>array('require'=>true, 'type'=>'string'),
			)
		);

	public function __construct(){
		parent::__construct();
	}

	public function add(){
		parent::add();
	}

	public function get(){
		return parent::select();
	}
}