<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin controller for the students module
 *
 * @author 		Sean Patterson - Fresh Consulting
 * @package 	PyroCMS
 * @subpackage Students module
 * @category	Modules
 */
class Admin extends Admin_Controller {

	/**
	 * Validation array
	 * @access private
	 * @var array
	 */
	private $validation_rules = array(
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

	/**
	 * Constructor method
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// Call the parent's constructor method
		parent::Admin_Controller();

		// Load the required classes
		$this->load->model('users/users_m');
		$this->load->model('students_m');
		$this->load->model('groups/group_m');
		
		$this->load->helper('users/user');
		$this->load->helper('student');
		
		$this->load->library('form_validation');
		
		$this->lang->load('users/user');
		$this->lang->load('student');

		$this->data->groups = $this->group_m->get_all();
		$this->data->groups_select = array_for_select($this->data->groups, 'id', 'description');

		$this->template->set_partial('shortcuts', 'admin/partials/shortcuts');
	}

	/**
	 * List all students
	 * @access public
	 * @return void
	 */
	public function index()
	{
		//base where clause
		$base_where = array('active' => 0);

		//determine active param
		$base_where['users.active'] = $this->input->post('f_module') ? (int) $this->input->post('f_active') : $base_where['active'];

		//keyphrase param
		$base_where = $this->input->post('f_keywords') ? $base_where + array('name' => $this->input->post('f_keywords')) : $base_where;

		// Create pagination links
		$pagination = create_pagination('admin/students/index', $this->students_m->count_by($base_where));

		// Using this data, get the relevant results
		$users = $this->students_m
						  ->order_by('users.active', 'desc')
						  ->limit($pagination['limit'])
						  ->get_many_by($base_where);

		//unset the layout if we have an ajax request
		$this->is_ajax() ? $this->template->set_layout(FALSE) : '';

		// Render the view
		$this->template
				->set('pagination', $pagination)
				->set('users', $users)
				->set_partial('filters', 'admin/partials/filters')
				->append_metadata(js('admin/filter.js'))
				->title($this->module_details['name'])
				->build('admin/index', $this->data);
	}

	/**
	 * Method for handling different form actions
	 * @access public
	 * @return void
	 */
	public function action()
	{
		// Determine the type of action
		switch ($this->input->post('btnAction'))
		{
			case 'activate':
				$this->activate();
				break;
			case 'delete':
				$this->delete();
				break;
			default:
				redirect('admin/students');
				break;
		}
	}

	/**
	 * Create a new student
	 *
	 * @access public
	 * @return void
	 */
	public function create()
	{
		// We need a password don't you think?
		$this->validation_rules[2]['rules'] .= '|callback__email_check';
		$this->validation_rules[3]['rules'] .= '|required';
		$this->validation_rules[5]['rules'] .= '|callback__username_check';

		// Set the validation rules
		$this->form_validation->set_rules($this->validation_rules);

		$email            = $this->input->post('email');
		$password         = $this->input->post('password');
		$username         = $this->input->post('username');
		
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

		$user_data = array(
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

		if ($this->form_validation->run() !== FALSE)
		{
			// Hack to activate immediately
			if ($this->input->post('active'))
			{
				$this->config->config['ion_auth']['email_activation'] = FALSE;
			}

			$group = $this->group_m->get($this->input->post('group_id'));

			// Try to register the user
			if ($user_id = $this->ion_auth->register($username, $password, $email, $user_data, $group->name))
			{
				// Insert the student information to the proper table and add it to the user data object.
				$sql = 'INSERT INTO student (user_id, age, grade_level, gender, esl, sport_id, sport_level_id, created, modified)
                                 VALUES (' . $this->db->escape($user_id) . ', ' 
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
				
				// Set the flashdata message and redirect
				$this->session->set_flashdata('success', $this->ion_auth->messages());
				redirect('admin/students');
			}
			// Error
			else
			{
				$this->data->error_string = $this->ion_auth->errors();
			}
		}
		else
		{
			// Dirty hack that fixes the issue of having to re-add all data upon an error
			if ($_POST)
			{
				$member = (object) $_POST;
			}
		}
		// Loop through each validation rule
		foreach ($this->validation_rules as $rule)
		{
			$member->{$rule['field']} = set_value($rule['field']);
		}

		// Render the view
		$this->data->member = & $member;
		$this->template
				->title($this->module_details['name'], lang('user_add_title'))
				->build('admin/form', $this->data);
	}

	/**
	 * Edit an existing student
	 *
	 * @access public
	 * @param int $id The ID of the student to edit
	 * @return void
	 */
	public function edit($id = 0)
	{
		// confirm_password is required in case the user enters a new password
		if ($this->input->post('password') && $this->input->post('password') != '')
		{
			$this->validation_rules[3]['rules'] .= '|required';
			$this->validation_rules[3]['rules'] .= '|matches[password]';
		}

		// Get the student's data
		$member = $this->ion_auth->get_user($id);
		
		// Got student?
		if (!$member)
		{
			$this->session->set_flashdata('error', $this->lang->line('user_edit_user_not_found_error'));
			redirect('admin/students');
		}
		
		// Get student related data and add it to member.
		$this->db->where('user_id', $id);
    	$student_data = $this->db->get('student')->row();
    	
    	$member->student_id     = $student_data->id;
    	$member->age            = $student_data->age;
		$member->grade_level    = $student_data->grade_level;
		$member->gender         = $student_data->gender;
		$member->esl            = $student_data->esl;
		$member->sport_id       = $student_data->sport_id;
		$member->sport_level_id = $student_data->sport_level_id;	
		
		$secondary_sport_ids = array();
		$this->db->where('student_id', $member->student_id);		
		$query = $this->db->get('student_secondary_sport');

      foreach ($query->result() as $row)
      {
          $secondary_sport_ids[] = $row->sport_id;
      }
		
		$member->secondary_sport_ids = $secondary_sport_ids;

		// Check to see if we are changing usernames
		if ($member->username != $this->input->post('username'))
		{
			$this->validation_rules[6]['rules'] .= '|callback__username_check';
		}

		// Check to see if we are changing emails
		if ($member->email != $this->input->post('email'))
		{
			$this->validation_rules[5]['rules'] .= '|callback__email_check';
		}

		// Run the validation
		$this->form_validation->set_rules($this->validation_rules);
		if ($this->form_validation->run() === TRUE)
		{
			// Get the POST data
			$update_data['first_name'] = $this->input->post('first_name');
			$update_data['last_name'] = $this->input->post('last_name');
			$update_data['email'] = $this->input->post('email');
			$update_data['active'] = $this->input->post('active');
			$update_data['username'] = $this->input->post('username');
			$update_data['display_name'] = $this->input->post('display_name');
			$update_data['group_id'] = $this->input->post('group_id');						

			// Password provided, hash it for storage
			if ($this->input->post('password') && $this->input->post('confirm_password'))
			{
				$update_data['password'] = $this->input->post('password');
			}

			if ($this->ion_auth->update_user($id, $update_data))
			{
				// Append the student data to the update data after the user module does it's update.
            $update_data['student_id'] = $member->student_id;
				$update_data['age'] = (int)$this->input->post('age');
            $update_data['grade_level'] = (int)$this->input->post('grade_level');
            $update_data['gender'] = $this->input->post('gender');
            $update_data['esl'] = $this->input->post('esl');
            $update_data['sport_id'] = (int)$this->input->post('sport_id');
            $update_data['sport_level_id'] = (int)$this->input->post('sport_level_id');
            $update_data['secondary_sport_ids'] = $secondary_sport_ids;
				
				// Update student related data.
				$sql = 'UPDATE student 
				           SET
				               age = ' . $this->db->escape($update_data['age']) . ', 
				               grade_level = ' . $this->db->escape($update_data['grade_level']) . ', 
				               gender = ' . $this->db->escape($update_data['gender']) . ', 
				               esl = ' . $this->db->escape($update_data['esl']) . ', 
				               sport_id = ' . $this->db->escape($update_data['sport_id']) . ', 
				               sport_level_id = ' . $this->db->escape($update_data['sport_level_id']) . ', 
				               modified = NOW()
				         WHERE user_id = ' . $id;
				         $this->db->query($sql);	
				         
				// Remove any existing and insert any secondary sports for the student.
				$sql = 'DELETE FROM student_secondary_sport WHERE student_id = ' . $this->db->escape($update_data['student_id']);
				$this->db->query($sql);
				
				// Reprocess secondary sport Ids to get ones checked from the form and not the user's
				// account.
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
				
            if (isset($secondary_sport_ids) && count($secondary_sport_ids > 0))
            {
               foreach ($secondary_sport_ids as $sportid)
               {                               
                 $sql = 'INSERT INTO student_secondary_sport (student_id, sport_id, created, modified)
                                       VALUES (' . $this->db->escape($update_data['student_id']) . ', ' 
                                                 . $this->db->escape($sportid) . ', 
                                                 NOW(), 
                                                 NOW()
                                              )';
                                             
                  $this->db->query($sql);
               }
            }
				
				$this->session->set_flashdata('success', $this->ion_auth->messages());
			}
			else
			{
				$this->session->set_flashdata('error', $this->ion_auth->errors());
			}

			// Redirect the user
			redirect('admin/students');
		}
		else
		{
			// Dirty hack that fixes the issue of having to re-add all data upon an error
			if ($_POST)
			{
				$member = (object) $_POST;
				$member->full_name = $member->first_name . ' ' . $member->last_name;
			}
		}
		// Loop through each validation rule
		foreach ($this->validation_rules as $rule)
		{
			if ($this->input->post($rule['field']) !== FALSE)
			{
				$member->{$rule['field']} = set_value($rule['field']);
			}
		}

		// Render the view
		$this->data->member = & $member;
		$this->template
				->title($this->module_details['name'], sprintf(lang('student_edit_title'), $member->full_name))
				->build('admin/form', $this->data);
	}

	/**
	 * Show a user preview
	 * @access	public
	 * @param	int $id The ID of the user
	 * @return	void
	 */
	public function preview($id = 0)
	{
		$data->user = $this->ion_auth->get_user($id);

		$this->template
			->set_layout('modal', 'admin')
			->build('admin/preview', $data);
	}

	/**
	 * Activate a user
	 * @access public
	 * @param int $id The ID of the user to activate
	 * @return void
	 */
	public function activate($id = 0)
	{
		$ids = ($id > 0) ? array($id) : $this->input->post('action_to');

		// Activate multiple
		if (!empty($ids))
		{
			$activated = 0;
			$to_activate = 0;
			foreach ($ids as $id)
			{
				if ($this->ion_auth->activate($id))
				{
					$activated++;
				}
				$to_activate++;
			}
			$this->session->set_flashdata('success', sprintf($this->lang->line('user_activate_success'), $activated, $to_activate));
		}
		else
		{
			$this->session->set_flashdata('error', $this->lang->line('user_activate_error'));
		}

		// Redirect the user
		redirect('admin/students');
	}

	/**
	 * Delete an existing student
	 *
	 * @access public
	 * @param int $id The ID of the student to delete
	 * @return void
	 */
	public function delete($id = 0)
	{
		$ids = ($id > 0) ? array($id) : $this->input->post('action_to');

		if (!empty($ids))
		{
			$deleted = 0;
			$to_delete = 0;
			foreach ($ids as $id)
			{
				// Make sure the admin is not trying to delete themself
				if ($this->ion_auth->get_user()->id == $id)
				{
					$this->session->set_flashdata('notice', $this->lang->line('user_delete_self_error'));
					continue;
				}

				if ($this->ion_auth->delete_user($id))
				{
					// Flush student information as well.
					$query = $this->db->query('SELECT id FROM student WHERE user_id = ' . $id . ' LIMIT 1');
               $row = $query->row();
               $student_id = $row->id;
				   
				   $sql = 'DELETE FROM student_secondary_sport WHERE student_id = ' . $student_id;
		         $this->db->query($sql);

				   $sql = 'DELETE FROM student WHERE student_id = ' . $student_id;
		         $this->db->query($sql);		         
					
					$deleted++;
				}
				$to_delete++;
			}

			if ($to_delete > 0)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('user_mass_delete_success'), $deleted, $to_delete));												

				$this->session->set_flashdata('success', $this->ion_auth->messages());				
			}
		}
		// The array of id's to delete is empty
		else
			$this->session->set_flashdata('error', $this->lang->line('user_mass_delete_error'));

		// Redirect
		redirect('admin/students');
	}

	/**
	 * Username check
	 *
	 * @return bool
	 * @author Ben Edmunds
	 * */
	public function _username_check($username)
	{
		if ($this->ion_auth->username_check($username))
		{
			$this->form_validation->set_message('_username_check', $this->lang->line('user_error_username'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Email check
	 *
	 * @return bool
	 * @author Ben Edmunds
	 * */
	public function _email_check($email)
	{
		if ($this->ion_auth->email_check($email))
		{
			$this->form_validation->set_message('_email_check', $this->lang->line('user_error_email'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Check that a proper group has been selected
	 *
	 * @return bool
	 * @author Stephen Cozart
	 */
	public function _group_check($group)
	{
		if ( ! $this->group_m->get($group))
		{
			$this->form_validation->set_message('_group_check', $this->lang->line('regex_match'));
			return FALSE;
		}
		return TRUE;
	}

}