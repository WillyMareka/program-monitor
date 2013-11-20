<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
require ('gantti.php');
class Gantt extends MY_Controller {
	var $activities, $assignedType, $indicators, $objectives, $partners, $targets, $users;
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> createTables();
		$this -> setProperties();
		date_default_timezone_set('UTC');
		setlocale(LC_ALL, 'en_US');
		//$query = "SELECT * FROM activities";
		$values = $this -> db -> get('activities') -> result_array();
		//var_dump($values);die;
		$data = array();
		foreach ($values as $val) {
			//$this->activities
			$data[] = array('label' => $val['activity_name'], 'start' => $val['activity_start'], 'end' => $val['activity_end']);
		}
		/*	$data[] = array('label' => 'Project 1', 'start' => '2012-04-20', 'end' => '2012-05-12');
		 $data[] = array('label' => 'Project 2', 'start' => '2012-04-22', 'end' => '2012-05-22', 'class' => 'important', );
		 $data[] = array('label' => 'Project 3', 'start' => '2012-05-25', 'end' => '2013-06-20', 'class' => 'urgent', );
		 $data[] = array('label' => 'Project 3', 'start' => '2012-05-25', 'end' => '2013-06-20', 'class' => 'urgent', );
		 $data[] = array('label' => 'Project 3', 'start' => '2012-05-25', 'end' => '2013-06-20', 'class' => 'urgent', );
		 $data[] = array('label' => 'Project 3', 'start' => '2012-05-25', 'end' => '2013-06-20', 'class' => 'urgent', );
		 */
		$gantti = new Gantti($data, array('title' => 'Demo', 'cellwidth' => 25, 'cellheight' => 35));

		$datas['gantt'] = $gantti;
		//$gantti = new Gantti();
		$datas['contentView'] = "gantt/gantt_v";
		$datas['title'] = "Dashboard | System Backup";
		$this -> load -> view('template_v', $datas);
	}

	public function createTables() {
		$this -> activities = R::dispense('activities');
		$this -> assignedType = R::dispense('assignedtype');
		$this -> indicators = R::dispense('indicators');
		$this -> objectives = R::dispense('objectives');
		$this -> partners = R::dispense('partners');
		$this -> targets = R::dispense('targets');
		$this -> users = R::dispense('users');
	}

	public function setProperties() {
		$this -> activities ->  setAttr('activity_name','test') -> setAttr('activity_start','2013-07-20') -> setAttr('activity_end','2013-09-01') -> setAttr('activity_created','test') -> setAttr('activity_partners','test') -> setAttr('activity_creator','test') -> setAttr('activity_type',1) -> setAttr('activity_responsible',1) -> setAttr('activity_indicator',1) -> setAttr('activity_objective',1);
		R::store($this -> activities);

		$this -> assignedType ->  setAttr('assigned_name','test') ;
		R::store($this -> assignedType);

		$this -> indicators -> setAttr('indicator_name', 'Test');
		R::store($this -> indicators);

		$this -> objectives -> setAttr('objective_name', 'Test') -> setAttr('objective_created', time()) -> setAttr('objective_creator', '1');
		R::store($this -> objectives);
		
		$this -> partners -> setAttr('partner_name', 'Test') -> setAttr('partner_type', 1);
		R::store($this -> partners);
		
		$this -> targets -> setAttr('target_value', 'Test') -> setAttr('target_created', time())-> setAttr('target_creator', 1)-> setAttr('target_indicator', 1);
		R::store($this -> targets);
		
		$this -> users -> setAttr('user_name', 'Test') -> setAttr('user_email', 'user@example.com')-> setAttr('user_phone', '0000000000')-> setAttr('user_password', 'secret')-> setAttr('user_rights', 1)-> setAttr('user_active', 1)-> setAttr('user_created', time());
		R::store($this -> users);
	}

}
