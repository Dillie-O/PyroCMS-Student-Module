<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author 		Sean Patterson - Fresh Consutling
 * @package 	PyroCMS
 * @subpackage Students Module
 * @since		v1.0
 *
 */
class Students_m extends MY_Model
{
   protected $_table = 'student';
      
	function get_all()
   {    	
    	$this->db->select('student.age, student.grade_level, student.esl, student.sport_id, student.sport_level_id, profiles.*, users.*, g.description as group_name, IF(profiles.last_name = "", profiles.first_name, CONCAT(profiles.first_name, " ", profiles.last_name)) as full_name')
    			   ->join('users', 'users.id = student.user_id', 'left')
    			   ->join('groups g', 'g.id = users.group_id', 'left')
    			   ->join('profiles', 'profiles.user_id = users.id', 'left');
    			   
      $this->db->where('g.name', 'student');
      
      $this->db->group_by('users.id');
        
    	return parent::get_all();
   }

	// Create a new student
	function add($input = array())
    {
		$this->load->helper('date');
		
		// Do initial insert and get object
		$user = parent::insert(array(
        	'email'				   => $input->email,
        	'password'	   		=> $input->password,
        	'salt'			   	=> $input->salt,
        	'first_name' 		   => ucwords(strtolower($input->first_name)),
        	'last_name'    		=> ucwords(strtolower($input->last_name)),
        	'role' 			   	=> empty($input->role) ? 'user' : $input->role,
        	'is_active' 		   => 0,
        	'lang'		   		=> $this->config->item('default_language'),
        	'activation_code' 	=> $input->activation_code,
        	'created_on' 	   	=> now(),
			'last_login'	   	=> now(),
        	'ip' 			      	=> $this->input->ip_address()
        ));
        
      $student = $user;
        
      // Add student information and insert it as well.
      $student->age = $input->age;
      $student->grade_level = $input->grade_level;
      $student->gender = $input->gender;
      $student->esl = $input->esl;
      $student->sport_id = $input->sport_id;
      $student->sport_level_id = $input->sport_level_id;
      
      $this->db->insert(array(
        	'user_id'         => $user->id,
        	'age'			   	=> $student->age,
        	'grade_level'		=> $student->grade_level,
        	'gender'	   		=> $student->gender,
        	'sport_id'  		=> $student->sport_id,
        	'sport_level_id'  => $student->sport_level_id,
        	'created'         => 'NOW()',
        	'modified'        => 'NOW()'
        ));
        
      $student->id = $this->db->insert_id();
      
      return $student;
	}
	
	public function count_by($params = array())
	{
		if(!empty($params['active']))
		{
			$params['active'] = $params['active'] === 2 ? 0 : $params['active'] ;
			$this->db->where('users.active', $params['active']);
		}

		if(!empty($params['group_id']))
		{
			$this->db->where('group_id', $params['group_id']);
		}

		if(!empty($params['name']))
		{
			$this->db->like('users.username', trim($params['name']))
						->or_like('users.email', trim($params['name']))
						->or_like('profiles.first_name', trim($params['name']))
						->or_like('profiles.last_name', trim($params['name']));
		}

		return $this->db->count_all_results();
	}

	public function get_many_by($params = array())
	{
		if(!empty($params['active']))
		{
			$params['active'] = $params['active'] === 2 ? 0 : $params['active'] ;
			$this->db->where('users.active', $params['active']);
		}

		if(!empty($params['group_id']))
		{
			$this->db->where('group_id', $params['group_id']);
		}

		if(!empty($params['name']))
		{
			$this->db->or_like('users.username', trim($params['name']))
						->or_like('users.email', trim($params['name']))
						->or_like('profiles.first_name', trim($params['name']))
						->or_like('profiles.last_name', trim($params['name']));
		}

		return $this->get_all();
	}
}