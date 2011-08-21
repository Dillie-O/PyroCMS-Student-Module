<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Students extends Module 
{
	public $version = '1.0';
	
	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Students'
			),
			'description' => array(
				'en' => 'Let students register and log in to the site, and manage them via the control panel.'
			),
			'frontend' => FALSE,
			'backend'  => TRUE,
			'menu'	  => TRUE
		);
	}
	
	public function install()
	{
		// Create student tables and add student category to user groups.
		$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->db->dbprefix('student') . '
              (
                 id int NOT NULL AUTO_INCREMENT,
                 user_id int NOT NULL,
                 age int NOT NULL,
                 grade_level int NOT NULL,
                 gender char NOT NULL,
                 esl char NOT NULL,
                 sport_id int NOT NULL,
                 sport_level_id int NOT NULL,
                 created datetime NOT NULL,
                 modified datetime NOT NULL,
                 PRIMARY KEY (id)
              )
		       ';		
		$this->db->query($sql);
		
		$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->db->dbprefix('student_secondary_sport') . '
              (
                 id int NOT NULL AUTO_INCREMENT,
                 student_id int NOT NULL,
                 sport_id int NOT NULL,
                 created datetime NOT NULL,
                 modified datetime NOT NULL,
                 PRIMARY KEY (id)
              )
		       ';
		$this->db->query($sql);
		
		$sql = 'INSERT INTO ' . $this->db->dbprefix('groups') . ' (name, description) 
		             VALUES (\'student\', \'Students\')';
		$this->db->query($sql);
		
		return TRUE;
	}

	public function uninstall()
	{
		// Remove student related data and module settings					
		$sql = 'DELETE FROM '. $this->db->dbprefix('groups') . ' WHERE name = \'student\'';
		$this->db->query($sql);
		
		$sql = 'DROP TABLE '. $this->db->dbprefix('student_secondary_sport');
		$this->db->query($sql);
		
		$sql = 'DROP TABLE '. $this->db->dbprefix('student');
		$this->db->query($sql);
		
		$sql = 'DELETE FROM '. $this->db->dbprefix('modules') . ' WHERE name = \'student\'';
		$this->db->query($sql);	
				
		return TRUE;
	}

	public function upgrade($old_version)
	{
		// Your Upgrade Logic
		return TRUE;
	}
	
	public function help()
	{
		// Return a string containing help info
		// You could include a file and return it here.
		return '<h4>Overview</h4>
		<p>The Students module works together with Users, Groups, and Permissions to give PyroCMS access control.</p>
		<h4>Add a Student</h4><hr>
		<p>Fill out the students\'s details (including a password) and save. If you have activation emails enabled in Settings
		an email will be sent to the new student with an activation link.</p>
		<h4>Activating New Students</h4><hr>
		<p>If activation emails are disabled in Settings users that register on the website front-end will appear under the Inactive Students
		menu item until you either approve or delete their account. If activation emails are enabled users may register silently, without an admin\'s help.</p>';
	}
}
/* End of file details.php */
