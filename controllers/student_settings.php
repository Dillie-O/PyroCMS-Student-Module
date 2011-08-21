<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Student settings controller for the students module
 *
 * @author 		Sean Patterson - Fresh Consulting
 * @package 	PyroCMS
 * @subpackage Students module
 * @category	Modules
 */
class Student_settings extends Public_Controller
{

	/**
	 * The ID of the user
	 * @access private
	 * @var int
	 */
	private $user_id = 0;

	/**
	 * Array containing the validation rules
	 * @access private
	 * @var array
	 */
	private $validation_rules 	= array();

	/**
	 * Constructor method
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// Call the parent's constructor method
		parent::__construct();

		// Get the user ID, if it exists
		if($user = $this->ion_auth->get_user())
		{
			$this->user_id = $user->id;
		}

		// Load the required classes
		$this->load->model('users/users_m');
		$this->load->model('students_m');
		
		$this->load->helper('users/user');
		$this->load->helper('student');
		
		$this->lang->load('users/user');
		$this->lang->load('student');				
		
		$this->load->library('form_validation');

		// Validation rules
		$this->validation_rules = array(
			array(
				'field' => 'settings_first_name',
				'label' => lang('user_first_name'),
				'rules' => 'required'
			),
			array(
				'field' => 'settings_last_name',
				'label' => lang('user_last_name'),
				'rules' => ($this->settings->require_lastname ? 'required' : '')
			),
			array(
				'field' => 'settings_password',
				'label' => lang('user_password'),
				'rules' => 'min_length[6]|max_length[20]'
			),
			array(
				'field' => 'settings_confirm_password',
				'label' => lang('user_confirm_password'),
				'rules' => ($this->input->post('settings_password') ? 'required|' : '').'matches[settings_password]'
			),
			array(
				'field' => 'settings_email',
				'label' => lang('user_email'),
				'rules' => 'valid_email'
			),
			array(
				'field' => 'settings_confirm_email',
				'label' => lang('user_confirm_email'),
				'rules' => 'valid_email|matches[settings_email]'
			),
			array(
				'field' => 'settings_lang',
				'label' => lang('user_lang'),
				'rules' => 'alpha|max_length[2]'
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
		$this->form_validation->set_rules($this->validation_rules);
	}

	/**
   	 * Show the current settings
	 *
	 * @access public
	 * @return void
   	 */
	public function index()
	{
		$this->edit();
	}

	/**
	 * Edit the current user's settings
	 *
	 * @access public
	 * @return void
	 */
	public function edit()
	{
		// Got login?
		if(!$this->ion_auth->logged_in())
		{
			redirect('users/login');
		}
		
	    // Get settings for this user
	   $user_settings = $this->ion_auth->get_user();
	   
	   // Get student related data and add it to member.
		$this->db->where('user_id', $this->user_id);
    	$student_settings = $this->db->get('student')->row();    	
    	
    	$user_settings->student_id     = $student_settings->id;
    	$user_settings->age            = $student_settings->age;
		$user_settings->grade_level    = $student_settings->grade_level;
		$user_settings->gender         = $student_settings->gender;
		$user_settings->esl            = $student_settings->esl;
		$user_settings->sport_id       = $student_settings->sport_id;
		$user_settings->sport_level_id = $student_settings->sport_level_id;
		
		$secondary_sport_ids = array();
		$this->db->where('student_id', $user_settings->student_id);		
		$query = $this->db->get('student_secondary_sport');

      foreach ($query->result() as $row)
      {
          $secondary_sport_ids[] = $row->sport_id;
      }
		
		$user_settings->secondary_sport_ids = $secondary_sport_ids;
		
		// Settings valid?
	    if ($this->form_validation->run())
	    {
			// Set the data to insert
	    	$set['first_name'] 	= $this->input->post('settings_first_name', TRUE);
	    	$set['last_name'] 	= $this->input->post('settings_last_name', TRUE);

	    	// Set the language for this user
			$this->ion_auth->set_lang( $this->input->post('settings_lang', TRUE) );
			$set['lang'] = $this->input->post('settings_lang', TRUE);

			if ($set['lang'])
			{
				$_SESSION['lang_code'] = $set['lang'];
			}

	    	// If password is being changed (and matches)
	    	if($this->input->post('settings_password'))
	    	{
				$set['password'] = $this->input->post('settings_password');
	    	}	    	

			if ($this->ion_auth->update_user($this->user_id, $set))
			{
            // Append the student data to the update data after the user module does it's update.
				$set['age'] = (int)$this->input->post('age', TRUE);
            $set['grade_level'] = (int)$this->input->post('grade_level', TRUE);
            $set['gender'] = $this->input->post('gender', TRUE);
            $set['esl'] = $this->input->post('esl', TRUE);
            $set['sport_id'] = (int)$this->input->post('sport_id', TRUE);
            $set['sport_level_id'] = (int)$this->input->post('sport_level_id', TRUE);
            $set['secondary_sport_ids'] = $secondary_sport_ids;
				
				// Update student related data.
				$sql = 'UPDATE student 
				           SET age = ' . $this->db->escape($set['age']) . ', 
				               grade_level = ' . $this->db->escape($set['grade_level']) . ', 
				               gender = ' . $this->db->escape($set['gender']) . ', 
				               esl = ' . $this->db->escape($set['esl']) . ', 
				               sport_id = ' . $this->db->escape($set['sport_id']) . ', 
				               sport_level_id = ' . $this->db->escape($set['sport_level_id']) . ', 
				               modified = NOW()
				         WHERE user_id = ' . $this->user_id;
				$this->db->query($sql);			
				
				// Remove any existing and insert any secondary sports for the student.
				$sql = 'DELETE FROM student_secondary_sport WHERE student_id = ' . $this->db->escape($user_settings->student_id);
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
                                       VALUES (' . $this->db->escape($user_settings->student_id) . ', ' 
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

			// Redirect
	    	redirect('students/student_settings/edit');
	    }
		else
		{
			// Loop through each validation rule
			foreach ($this->validation_rules as $rule)
			{
				if ($this->input->post($rule['field']) !== FALSE)
				{
					// Get rid of the settings_ prefix
					$fieldname = str_replace('settings_','',$rule['field']);
					$user_settings->{$fieldname} = set_value($rule['field']);
				}
			}
		}

	    // Format languages for the dropdown box
	    $this->data->languages = array();
	    foreach($this->config->item('supported_languages') as $lang_code => $lang)
	    {
	    	$this->data->languages[$lang_code] = $lang['name'];
	    }

		$this->data->user_settings =& $user_settings;
		$this->template->build('settings/edit', $this->data);
	}
}