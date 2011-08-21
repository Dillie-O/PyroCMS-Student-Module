<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Student Helpers
 *
 * @package		Student Module
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Sean Patterson
 */
// ----------------------------------------------------------------------------

/** Return an array of Ids and Names for all schedule approved sports in the
 *  the alternate database.
 *
 *  @return array
 */
function get_sports()
{
   $results = array();

   $otherdb = ci()->load->database('otherdbsource', TRUE);      
   $sql = "  SELECT Sport.Id, CONCAT(Sport.Name, '  [', SportType.Name, ']') AS SportName
               FROM Sport
                    INNER JOIN SportType on SportType.Id = Sport.SportTypeID
              WHERE SportType.Name IN ('Freshman', 'Junior Varsity', 'JV 2', 'Varsity', 'VAR 2')
           ORDER BY SportName ASC          
          ";                        
   $query = $otherdb->query($sql);
   
   foreach ($query->result() as $row)
   {
       $results[$row->Id] = $row->SportName;
   }
   
   return $results;
}

/** Return the student group Id.
 *
 *  @return array
 */
function get_student_group_id()
{
   $query = ci()->db->query('SELECT Id FROM groups WHERE name = \'student\' LIMIT 1');
   $row = $query->row();
   
   return $row->Id;
}

/** Tests if user has student account or not.
 *
 * @param int
 * @return bool
 */
function is_student($user_id)
{
   $results = FALSE;

   $query = ci()->db->query('SELECT Count(Id) AS CountCheck FROM student WHERE user_id = ' . $user_id);
   $row = $query->row();
   
   $test = (int)$row->CountCheck;
   
   if($test == 1)
   {
      $results = TRUE;
   }
   else
   {
      $results = FALSE;
   }
   
   return $results;
}

/* End of file students/helpers/student_helper.php */