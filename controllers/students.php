<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Student controller for the students module (frontend)
 *
 * @author 		Sean Patterson - Fresh Consulting
 * @package 	PyroCMS
 * @subpackage Students module
 * @category	Modules
 */
class Students extends Public_Controller {

	/**
	 * Constructor method
	 *
	 * @access public
	 * @return void
	 */
	function __construct()
	{
		// Call the parent's constructor method
		parent::__construct();

		// Load the required classes
		$this->load->model('users/users_m');
		$this->load->model('students_m');
		$this->load->model('groups/group_m');
		
		$this->load->helper('users/user');
		$this->load->helper('student');
		
		$this->lang->load('users/user');
		$this->lang->load('student');
		
		$this->load->library('form_validation');
	}

	/**
	 * Method to register a new student
	 * @access public
	 * @return void
	 */
	public function register()
	{
		// Validation rules
		$validation = array(
			array(
			'field' => 'first_name',
			'label' => 'lang:user_first_name_label',
			'rules' => 'required|utf8'
         ),
         array(
            'field' => 'last_name',
            'label' => 'lang:user_last_name_label',
            'rules' => 'required|utf8'
         ),
         array(
            'field' => 'email',
            'label' => 'lang:user_email_label',
            'rules' => 'required|valid_email'
         ),
         array(
            'field' => 'password',
            'label' => 'lang:user_password_label',
            'rules' => 'min_length[6]|max_length[20]'
         ),
         array(
            'field' => 'confirm_password',
            'label' => 'lang:user_password_confirm_label',
            'rules' => 'matches[password]'
         ),
         array(
            'field' => 'username',
            'label' => 'lang:user_username',
            'rules' => 'required|alphanumeric|min_length[3]|max_length[20]'
         ),
         array(
            'field' => 'display_name',
            'label' => 'lang:user_display_name',
            'rules' => 'alphanumeric|min_length[3]|max_length[50]'
         ),		
         array(
            'field' => 'active',
            'label' => 'lang:user_active_label',
            'rules' => ''
         ),
         array(
            'field' => 'age',
            'label' => 'lang:student_age',
            'rules' => 'required|integer'
         ),
         array(
            'field' => 'grade_level',
            'label' => 'lang:student_grade_level',
            'rules' => 'required|integer'
         ),
         array(
            'field' => 'gender',
            'label' => 'lang:student_gender',
            'rules' => 'required|alpha'
         ),
         array(
            'field' => 'esl',
            'label' => 'lang:student_esl',
            'rules' => 'required|alpha'
         ),
         array(
            'field' => 'sport_id',
            'label' => 'lang:student_sport',
            'rules' => 'required|numeric'
         )
		);

		// Set the validation rules
		$this->form_validation->set_rules($validation);

		$email				= $this->input->post('email');
		$password			= $this->input->post('password');
		$username			= $this->input->post('username');
		
		$group_id         = (int)$this->input->post('group_id');
		$age              = (int)$this->input->post('age');
		$grade_level      = (int)$this->input->post('grade_level');
		$gender           = $this->input->post('gender');
		$esl              = $this->input->post('esl');
		$sport_id         = (int)$this->input->post('sport_id');
		$sport_level_id   = (int)$this->input->post('sport_level_id');
		
		$secondary_sport_ids = array();
		$sports = get_sports();
      $sportids = array_keys($sports);
      
      foreach ($sportids as $id)
      {                               
        $secondaryid = 'secondary_sport_' . $id;

        if($this->input->post($secondaryid))
        {
           $secondary_sport_ids[] = $id;
        }
      } 
				
		$user_data_array = array(
			'first_name'      => $this->input->post('first_name'),
			'last_name'       => $this->input->post('last_name'),
			'display_name'    => $this->input->post('display_name'),
			'group_id'        => (int)$this->input->post('group_id'),
         'age'             => (int)$this->input->post('age'),
         'grade_level'     => (int)$this->input->post('grade_level'),
         'gender'          => $this->input->post('gender'),
         'esl'             => $this->input->post('esl'),
         'sport_id'        => (int)$this->input->post('sport_id'),
         'sport_level_id'  => (int)$this->input->post('sport_level_id'),
         'secondary_sport_ids' => $secondary_sport_ids
		);

		// Convert the array to an object
		$user_data						= new stdClass();
		$user_data->first_name 		= $user_data_array['first_name'];
		$user_data->last_name		= $user_data_array['last_name'];
		$user_data->display_name	= $user_data_array['display_name'];
		$user_data->username			= $username;
		$user_data->email				= $email;
		$user_data->password 		= $password;
		$user_data->confirm_email 	= $this->input->post('confirm_email');
      $user_data->group_id       = (int)$this->input->post('group_id');
      $user_data->age            = (int)$this->input->post('age');
      $user_data->grade_level    = (int)$this->input->post('grade_level');
      $user_data->gender         = $this->input->post('gender');
      $user_data->esl            = $this->input->post('esl');
      $user_data->sport_id       = (int)$this->input->post('sport_id');
      $user_data->sport_level_id = (int)$this->input->post('sport_level_id');
      $user_data->secondary_sport_ids = $secondary_sport_ids;
		
		if ($this->form_validation->run())
		{
         $group = $this->group_m->get($this->input->post('group_id'));

			// Try to create the user
			if ($id = $this->ion_auth->register($username, $password, $email, $user_data_array, $group->name))
			{
				
				// Insert the student information to the proper table and add it to the user data object.
				$sql = 'INSERT INTO student (user_id, age, grade_level, gender, esl, sport_id, sport_level_id, created, modified)
                                 VALUES (' . $this->db->escape($id) . ', ' 
                                           . $this->db->escape($age) . ', '
                                           . $this->db->escape($grade_level) . ', '
                                           . $this->db->escape($gender) . ', '
                                           . $this->db->escape($esl) . ', '
                                           . $this->db->escape($sport_id) . ', '
                                           . $this->db->escape($sport_level_id) . ', 
                                           NOW(), 
                                           NOW()
                                        )';
                                       
            $this->db->query($sql);
            $student_id = $this->db->insert_id();
            
            $user_data->student_id = $student_id;     
            
            // Insert any secondary sports for the student.
            if (isset($secondary_sport_ids) && count($secondary_sport_ids > 0))
            {
               foreach ($secondary_sport_ids as $sportid)
               {                               
                 $sql = 'INSERT INTO student_secondary_sport (student_id, sport_id, created, modified)
                                       VALUES (' . $this->db->escape($student_id) . ', ' 
                                                 . $this->db->escape($sportid) . ', 
                                                 NOW(), 
                                                 NOW()
                                              )';
                                             
                  $this->db->query($sql);
               }
            }
                                 				
				$this->session->set_flashdata(array('notice' => $this->ion_auth->messages()));
				redirect('/users/activate');
			}

			// Can't create the user, show why
			else
			{
				$this->data->error_string = $this->ion_auth->errors();
			}
		}
		else
		{
			// Return the validation error
			$this->data->error_string = $this->form_validation->error_string();
		}

		$this->data->user_data =& $user_data;
		$this->template->title(lang('student_register_title'));
		$this->template->build('register', $this->data);
	}
}