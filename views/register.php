<h2 class="page-title" id="page_title"><?php echo lang('user_register_header') ?></h2>

<p>
	<span id="active_step"><?php echo lang('user_register_step1') ?></span> -&gt; 
	<span><?php echo lang('user_register_step2') ?></span>
</p>

<p><?php echo lang('student_register_reasons') ?></p>

<?php if(!empty($error_string)):?>
<!-- Woops... -->
<div class="error_box">
	<?php echo $error_string;?>
</div>
<?php endif;?>  

<?php 
   $hidden = array('group_id' => get_student_group_id());
   echo form_open('students/register', array('id'=>'register'), $hidden);    
?>

<fieldset>
   <legend><?php echo lang('user_details_section') ?></legend>
   <table>
      <tr>
         <td style="text-align:right">
            <label for="first_name"><?php echo lang('user_first_name') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <input type="text" name="first_name" maxlength="40" value="<?php echo $user_data->first_name; ?>" />
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="last_name"><?php echo lang('user_last_name') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <input type="text" name="last_name" maxlength="40" value="<?php echo $user_data->last_name; ?>" />
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="username"><?php echo lang('user_username') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <input type="text" name="username" maxlength="100" value="<?php echo $user_data->username; ?>" />
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="display_name"><?php echo lang('user_display_name') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <input type="text"name="display_name" maxlength="100" value="<?php echo $user_data->display_name; ?>" />
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="email"><?php echo lang('user_email') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <input type="text" name="email" maxlength="100" value="<?php echo $user_data->email; ?>" />
            <em>used to login</em>
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="confirm_email"><?php echo lang('user_confirm_email') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <input type="text" name="confirm_email" maxlength="100" value="<?php echo $user_data->confirm_email; ?>" />
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="password"><?php echo lang('user_password') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <input type="password" name="password" maxlength="100" />
         </td>
      </tr>
      
      <tr>
         <td style="text-align:right">
            <label for="confirm_password"><?php echo lang('user_confirm_password') ?></label>
         </td>
            
         <td style="text-align:left">
            &nbsp;&nbsp;
            <input type="password" name="confirm_password" maxlength="100" />
         </td>
      </tr>
   </table>
</fieldset>

<br/>

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
      
               echo form_dropdown('age', $options, isset($user_data->age) ? $user_data->age : 0);                                
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
      
               echo form_dropdown('grade_level', $options, isset($user_data->grade_level) ? $user_data->grade_level : 0);
               
               echo('<span class="required-icon tooltip">' . lang('required_label') . '</span>');
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
               echo form_radio('gender', 'M', isset($user_data->gender) && $user_data->gender == 'M' ? TRUE : FALSE) . ' Male';
               echo form_radio('gender', 'F', isset($user_data->gender) && $user_data->gender == 'F' ? TRUE : FALSE) . ' Female';                    
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
               echo form_radio('esl', 'N', (isset($user_data->esl) && $user_data->esl == 'N') ? TRUE : FALSE) . ' Yes';
               echo form_radio('esl', 'Y', (isset($user_data->esl) && $user_data->esl == 'Y') ? TRUE : FALSE) . ' No';                       
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
      
               echo form_dropdown('sport_id', $options, isset($user_data->sport_id) ? $user_data->sport_id : 0);
               
               echo('<span class="required-icon tooltip">' . lang('required_label') . '</span>');
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
      
               echo form_dropdown('sport_level_id', $options, isset($user_data->sport_level_id) ? $user_data->sport_level_id : 0);
               
               echo('<span class="required-icon tooltip">' . lang('required_label') . '</span>');
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
                            'checked'     => isset($user_data->secondary_sport_ids) && in_array($id, $user_data->secondary_sport_ids) ? TRUE : FALSE,
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

<?php echo form_submit('btnSubmit', lang('user_register_btn')) ?>

<?php echo form_close(); ?>