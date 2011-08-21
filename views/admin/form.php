<script type="text/javascript">
(function ($) {
	$(function(){

		// Stops Firefox from being an ass and remembering YOUR password in this box
		//this doesn't work... I just lost an hour to this firefox you douche!
		$('input[name="password"], input[name="confirm_password"]').val('');

	});
})(jQuery);
</script>


<?php if ($this->method == 'create'): ?>
	<h3><?php echo lang('student_add_title');?></h3>

<?php else: ?>
	<h3><?php echo sprintf(lang('student_edit_title'), $member->full_name);?></h3>
<?php endif; ?>

<?php 
   $hidden = array('group_id' => get_student_group_id(), 'secondary_sport_id_list' => '');
   echo form_open($this->uri->uri_string(), 'class="crud"', $hidden); 
?>

		<!-- Content tab -->
		<div id="user-details-tab">
			<fieldset>
			   <legend>User Details</legend>
				<ol>
					<li class="even">
						<label for="first_name"><?php echo lang('user_first_name_label');?></label>
						<?php echo form_input('first_name', $member->first_name); ?>
						<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
					</li>

					<li>
						<label for="last_name"><?php echo lang('user_last_name_label');?></label>
						<?php echo form_input('last_name', $member->last_name); ?>
						<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
					</li>

					<li class="even">
						<label for="email"><?php echo lang('user_email_label');?></label>
						<?php echo form_input('email', $member->email); ?>
						<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
					</li>

					<li>
						<label for="username"><?php echo lang('user_username');?></label>
						<?php echo form_input('username', $member->username); ?>
						<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
					</li>

					<li class="even">
						<label for="display_name"><?php echo lang('user_display_name');?></label>
						<?php echo form_input('display_name', $member->display_name); ?>
					</li>

					<li>
						<label for="group_id"><?php echo lang('user_group_label');?></label>
						Students
					</li>

					<li class="even">
						<label for="active"><?php echo lang('user_activate_label');?></label>
						<?php echo form_checkbox('active', 1, (isset($member->active) && $member->active == 1)); ?>
					</li>
				</ol>
			</fieldset>
		</div>

		<div id="user-password-tab">
			<fieldset>
			   <legend>Password</legend>
				<ol>
					<li class="even">
						<label for="password"><?php echo lang('user_password_label');?></label>
						<?php echo form_password('password'); ?>
						<?php if ($this->method == 'create'): ?>
						<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
						<?php endif; ?>
					</li>

					<li>
						<label for="confirm_password"><?php echo lang('user_password_confirm_label');?></label>
						<?php echo form_password('confirm_password'); ?>
						<?php if ($this->method == 'create'): ?>
						<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
						<?php endif; ?>
					</li>
				</ol>
			</fieldset>
		</div>
		
		<div id="student-demographics-tab">
			<fieldset>
			   <legend>Demographic Information</legend>
				<ol>
					<li class="even">
						<label for="age"><?php echo lang('student_age_label') ?></label><br/>
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
            
                     echo form_dropdown('age', $options, isset($member->age) ? $member->age : 0);
                     echo('<span class="required-icon tooltip">' . lang('required_label') . '</span>');
                  ?>
					</li>

					<li>
						<label for="grade_level"><?php echo lang('student_grade_level') ?></label>
                  <?php
                     $options = array(
                                 '0'   => 'Select a grade level ...',
                                 '9'   => '9th',
                                 '10'  => '10th',
                                 '11'  => '11th',
                                 '12'  => '12th'
                            );                
            
                     echo form_dropdown('grade_level', $options, isset($member->grade_level) ? $member->grade_level : 0);
                  ?>
					</li>
					
					<li class="even">
                  <label for="gender"><?php echo lang('student_gender_label') ?></label>
                  <?php
                     echo form_radio('gender', 'M', isset($member->gender) && $member->gender == 'M' ? TRUE : FALSE) . ' Male';
                     echo form_radio('gender', 'F', isset($member->gender) && $member->gender == 'F' ? TRUE : FALSE) . ' Female';                    
                     echo('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="required-icon tooltip">' . lang('required_label') . '</span>');
                  ?>
               </li>
               
               <li>
                  <label for="esl"><?php echo lang('student_esl_label') ?></label>
                  <?php
                     echo form_radio('esl', 'N', isset($member->esl) && $member->esl == 'N' ? TRUE : FALSE) . ' Yes';
                     echo form_radio('esl', 'Y', isset($member->esl) && $member->esl == 'Y' ? TRUE : FALSE) . ' No';                                           
                     echo('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="required-icon tooltip">' . lang('required_label') . '</span>');
                  ?>
               </li>
               
               <li class="even">
                  <label for="sport_id"><?php echo lang('student_sport_label') ?></label>
                  <?php                     
                     $options = array('0' => 'Select a sport ...');                                          
                     
                     $sports = get_sports();
                     $sportids = array_keys($sports);
                     
                     foreach ($sportids as $id)
                     {
                         $options[$id] = $sports[$id];
                     }                                                               
            
                     echo form_dropdown('sport_id', $options, isset($member->sport_id) ? $member->sport_id : 0);
                     
                     echo('<span class="required-icon tooltip">' . lang('required_label') . '</span>');
                  ?>
               </p>
               
               <li style="display:none">
                  <label for="sport_level_id"><?php echo lang('student_sport_level_label') ?></label>
                  <?php
                     $options = array(
                                 '0'  => 'Select a sport level ...',
                                 '1'  => 'Freshman',
                                 '2'  => 'JV',
                                 '3'  => 'Varsity'
                            );
            
                     echo form_dropdown('sport_level_id', $options, isset($member->sport_level_id) ? $member->sport_level_id : 0);                     
                     echo('<span class="required-icon tooltip">' . lang('required_label') . '</span>');
                  ?>
               </li>
               
               <li>
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
                                     'checked'     => isset($member->secondary_sport_ids) && in_array($id, $member->secondary_sport_ids) ? TRUE : FALSE,
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
               </li>
				</ol>
			</fieldset>
		</div>

	<div class="buttons float-right padding-top">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
	</div>

<?php echo form_close(); ?>