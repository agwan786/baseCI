<?php
include_once '/../models/validation.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	private $foo = array();
	private $errors;
	private $class;
	private $setData = array();
	private $data = array();
	public $getData = array();

	public function __construct($id=''){
		parent::__construct();
		$this->class = strtolower(get_class($this));
		$obj = new ReflectionClass($this->class);
		$this->data = $obj->getStaticPropertyValue('definitation');
		if($id!=''){
			$this->where = array($this->data['primary']=>$id);
			$this->order_by = array($this->data['primary']=>'desc');
			$this->return = 1;
			$this->getData = $this->select();
		}
	}

	public function __set($name, $value){
		$this->foo[$name] = $value;
	}

	public function __get($name){
		$siteLoad = array('db', 'load');
		if(!in_array($name, $siteLoad))
			return $this->foo[$name];
		else
			return parent::__get($name);
	}

	protected function select(){
		if(isset($this->foo['select'])){
			$this->db->select($this->foo['select']);
		}

		if(isset($this->foo['where']))
			$this->where_condition();

		if(isset($this->foo['order_by']) && $ordered = $this->foo['order_by']){
			foreach ($this->foo['order_by'] as $key => $value) {
				$this->db->order_by($key, $value);
			}
		}

		$query = $this->db->get($this->class);

		if(isset($this->foo['return']))
			$records = $query->row();
		else
			$records = $query->result();
		return $records;
	}

	protected function add(){
		$this->def($this->data);
		if(empty($this->errors)){
			try{
				$this->db->insert($this->class, $this->setData);
			}catch(Exception $e){
				die($e->getMessage());		
			}
		}else{
			$strErr = '';
			foreach($this->errors as $sqlErr)
				$strErr .= $sqlErr. "<br/>";
			throw new Exception($strErr);
		}
	}

	protected function update(){
		$this->def($this->data);
		$set = '';
		$where = '';
		$where_or = '';
		$where_in = '';
		$where_like = '';
		$this->sql = 'UPDATE {$this->class} SET ';
		foreach ($this->setData as $key => $value) {
			$set = "{$key}='{$value}', ";
		}
		$set = rtrim($set, ",");
		$this->sql .= $set ." ";
		$this->where_condition();
		$this->allQuery[] = $this->sql."<br/>";
		$return = $this->execute($this->sql);
		$this->unsetData();
		return $return;
	}

	private function where_condition(){
		$where = '';
		$where_or = '';
		$where_in = '';
		$where_like = '';
		// if(isset($this->data['primary']))
		// 	$where .= $this->data['primary']." = '".$this->getData[$this->data['primary']]."'";
		if(isset($this->foo['where']))
			$this->db->where($this->foo['where']);	
		
		if(isset($this->foo['where_or']))
			$this->db->or_where($this->foo['where_or']);

		if(isset($this->foo['where_in']))
			$this->db->where_in($this->foo['where_in']);

		if(isset($this->foo['where_like']))
			$this->db->like($this->foo['where_like']);
	}

	private function def($data = array()){
		$obj = new Validation();
		foreach($data['fields'] as $key=>$value){			
			if(isset($value['require']) && $value['require']){
				if(!isset($this->foo[$key]) || $this->foo[$key]=="" || $this->foo[$key]==null){
					if(!isset($this->getData[$key]))
						$this->errors[] = ucwords($key)." can't be null or blank";
					else
						$this->foo[$key] = $this->getData[$key];
				}
				if(!$isValid = $obj->validate($value['type'], $this->foo[$key]))
					$this->errors[] = ucwords($key)." isn't {$value['type']}";
				if(!$fieldData = $obj->xss_clean($this->foo[$key]))
					$this->errors[] = ucwords($key)." isn't passed xss clean security";
				if(empty($this->errors))
					$fieldValue = isset($this->foo[$key])?$this->foo[$key]:$this->getData[$key];
			}else{
				$fieldValue = isset($this->foo[$key])?$this->foo[$key]:"";
			}
			$this->setData[$key] = $fieldValue;
		}

	}

}