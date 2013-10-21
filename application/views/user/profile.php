<h1>Profile</h1>
<p>Please update the information below.</p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open(uri_string());?>

      <p>
            First Name: <br />
            <?php echo form_input($first_name);?>
      </p>

      <p>
            Last Name: <br />
            <?php echo form_input($last_name);?>
      </p>

      <p>
            Phone: <br />
            <?php echo form_input($phone1);?>-<?php echo form_input($phone2);?>-<?php echo form_input($phone3);?>
      </p>

      <p>
            Password: (if changing password)<br />
            <?php echo form_input($password);?>
      </p>

      <p>
            Confirm Password: (if changing password)<br />
            <?php echo form_input($password_confirm);?>
      </p>
      
	<?php if( count($currentGroups) != 0 ) { ?>
	
		 <h3>Member of groups</h3>
		<?php foreach ($groups as $group):?>
		<label class="checkbox">
		<?php
			$gID=$group['id'];
			$checked = null;
			$item = null;
			foreach($currentGroups as $grp) {
				if ($gID == $grp->id) {
					$checked= ' checked="checked"';
				break;
				}
			}
		?>
	
		<input type="checkbox" name="groups[]" value="<?php echo $group['id'];?>"<?php echo $checked;?>>
		<?php echo $group['name'];?>
		</label>
		<?php endforeach?>
		
	<?php } ?>
	
      <?php echo form_hidden('id', $user->id);?>
      <?php echo form_hidden($csrf); ?>

      <p><?php echo form_submit('submit', 'Update Profile');?></p>

<?php echo form_close();?>