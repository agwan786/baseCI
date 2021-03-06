<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->model('customer');
		$this->customer->where = array('email'=>'altaf.h@cisinlabs.com');
		$dataExist = $this->customer->get();		
		if(empty($dataExist)){
			$this->customer->fname = 'Altaf';
			$this->customer->lname = 'Husain';
			$this->customer->email = 'altaf.h@cisinlabs.com';
			$this->customer->password = '123456';
			$this->customer->status = '1';
			$this->customer->date = date('Y-m-d H:i:s');
			$this->customer->add();
		}
		$this->load->view('welcome_message');
	}
}
