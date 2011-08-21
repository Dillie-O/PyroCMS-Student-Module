<style type="text/css">
fieldset dl dd label {
	width:8em;
	display:inline-block;
}
fieldset dl dd input, fieldset dl dd textarea {
	width:95%;
}
</style>
    <h2><?php echo lang('profile_settings') ?></h2>
    
	<?php echo form_open('students/student_settings/edit', array('id'=>'student_edit_settings'));?>
	
	<fieldset>
   <legend><?php echo lang('user_details_section') ?></legend>
   <table>
      <tr>
         <td style="text-align:right">
            <label for="first_name"><?php echo lang('user_first_name') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php echo form_input('settings_first_name', $user_settings->first_name); ?>
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="last_name"><?php echo lang('user_last_name') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php echo form_input('settings_last_name', $user_settings->last_name); ?>
         </td>
      </tr>
   </table>
   </fieldset>
   
   <br/>
   
   <fieldset>
   <legend>Password</legend>
   <table>      
      <tr>
         <td style="text-align:right">
            <label for="password"><?php echo lang('user_password') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php echo form_password('settings_password'); ?>
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="confirm_password"><?php echo lang('user_confirm_password') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php echo form_password('settings_password'); ?>
         </td>
      </tr>
   </table>
</fieldset>

<br/>

<div style="display:none">	
<fieldset>
<legend><?php echo lang('user_other_settings_section') ?></legend>
   
   <p>
      <label for="settings_lang"><?php echo lang('user_lang') ?></label><br/>
      <?php echo form_dropdown('settings_lang', $languages, $user_settings->lang); ?>
   </p>
   
</fieldset>
</div>

<fieldset>
   <legend><?php echo lang('student_details_section') ?></legend>
   <table>
      <tr>
         <td style="text-align:right">
            <label for="age"><?php echo lang('student_age_label') ?></label> 
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php
               $options = array(
                           '0' => 'Select an age ...',
                           '13'  => '13 or younger',
                           '14'  => '14',
                           '15'  => '15',
                           '16'  => '16',
                           '17'  => '17',
                           '18'  => '18',
                           '19'  => '19 or older'
                      );                
      
               echo form_dropdown('age', $options, $user_settings->age);                             
            ?>
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="grade_level"><?php echo lang('student_grade_level_label') ?></label>         
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php
               $options = array(
                           '0'   => 'Select a grade level ...',
                           '9'   => '9th',
                           '10'  => '10th',
                           '11'  => '11th',
                           '12'  => '12th'
                      );                
      
               echo form_dropdown('grade_level', $options, $user_settings->grade_level);
            ?>
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="gender"><?php echo lang('student_gender_label') ?></label>         
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php
               echo form_radio('gender', 'M', isset($user_settings->gender) && $user_settings->gender == 'M' ? TRUE : FALSE) . ' Male';
               echo form_radio('gender', 'F', isset($user_settings->gender) && $user_settings->gender == 'F' ? TRUE : FALSE) . ' Female';                    
               echo('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="required-icon tooltip">' . lang('required_label') . '</span>');
            ?>
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="esl"><?php echo lang('student_esl_label') ?></label>          
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php
               echo form_radio('esl', 'N', isset($user_settings->esl) && $user_settings->esl == 'N' ? TRUE : FALSE) . ' Yes';
               echo form_radio('esl', 'Y', isset($user_settings->esl) && $user_settings->esl == 'Y' ? TRUE : FALSE) . ' No';                                           
               echo('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="required-icon tooltip">' . lang('required_label') . '</span>');
            ?>
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="sport_id"><?php echo lang('student_sport_label') ?></label>         
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php                     
               $options = array('0' => 'Select a sport ...');                                          
               
               $sports = get_sports();
               $sportids = array_keys($sports);
               
               foreach ($sportids as $id)
               {
                   $options[$id] = $sports[$id];
               }                                                               
      
               echo form_dropdown('sport_id', $options, $user_settings->sport_id);
            ?>
         </td>
      </tr>
      
      <tr style="display:none">
         <td style="text-align:right">            
            <label for="sport_level_id"><?php echo lang('student_sport_level_label') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <?php
               $options = array(
                           '0'  => 'Select a sport level ...',
                           '1'  => 'Freshman',
                           '2'  => 'JV',
                           '3'  => 'Varsity'
                      );
      
               echo form_dropdown('sport_level_id', $options, $user_settings->sport_level_id);
            ?>
         </td>
      </tr>
      
      <tr>
         <td colspan="2">
            <label for="secondary_sport_id"><?php echo lang('student_secondary_sport_label') ?></label> 
            <br/><br/>
            <table style="width:100%">
            <tr>
            <td style="width:300">
            <?php                     
               $options = array();
               
               $sports = get_sports();
               $sportids = array_keys($sports);
               $count = 1;
                           
               foreach ($sportids as $id)
               {                
                   $data = array(
                            'name'        => 'secondary_sport_' . $id,
                            'value'       => $id,
                            'checked'     => isset($user_settings->secondary_sport_ids) && in_array($id, $user_settings->secondary_sport_ids) ? TRUE : FALSE,
                            );
                   
                   echo form_checkbox($data) . $sports[$id];
                   
                   if($count != 0)
                   {
                      if($count % 2 == 0)
                      {
                         echo('</td><tr><td style:width="300">');
                      }
                      else
                      {
                         echo('</td><td>');
                      }
                   }
                   else
                   {
                      echo('</td><td>');
                   }
                   
                   $count++;
               }                                                                  
            ?>
            </tr>
            </table>
         </td>
      </tr>           
   </table>
</fieldset>

<?php echo form_submit('', lang('user_settings_btn')); ?>
	
 <?php echo form_close(); ?>
