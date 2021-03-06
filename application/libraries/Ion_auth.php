<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth
*
* Author: Ben Edmunds
*		  ben.edmunds@gmail.com
*         @benedmunds
*
* Added Awesomeness: Phil Sturgeon
*
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  10.01.2009
*
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
* Original Author name has been kept but that does not mean that the method has not been modified.
*
* Requirements: PHP5 or above
*
*/

class Ion_auth
{
	/**
	 * account status ('not_activated', etc ...)
	 *
	 * @var string
	 **/
	protected $status;

	/**
	 * extra where
	 *
	 * @var array
	 **/
	public $_extra_where = array();

	/**
	 * extra set
	 *
	 * @var array
	 **/
	public $_extra_set = array();

	/**
	 * caching of users and their groups
	 *
	 * @var array
	 **/
	public $_cache_user_in_group;

	/**
	 * __construct
	 *
	 * @return void
	 * @author Ben
	 **/
	public function __construct()
	{
		$this->load->config('ion_auth', TRUE);
		$this->load->library('email');
		$this->lang->load('ion_auth');
		$this->load->helper('cookie');

		//Load the session, CI2 as a library, CI3 uses it as a driver
		if (substr(CI_VERSION, 0, 1) == '2')
		{
			$this->load->library('session');
		}
		else
		{
			$this->load->driver('session');
		}

		// Load IonAuth MongoDB model if it's set to use MongoDB,
		// We assign the model object to "ion_auth_model" variable.
		$this->config->item('use_mongodb', 'ion_auth') ?
			$this->load->model('ion_auth_mongodb_model', 'ion_auth_model') :
			$this->load->model('ion_auth_model');

		$this->_cache_user_in_group =& $this->ion_auth_model->_cache_user_in_group;

		//auto-login the user if they are remembered
		if (!$this->logged_in() && get_cookie('identity') && get_cookie('remember_code'))
		{
			$this->ion_auth_model->login_remembered_user();
		}

		$email_config = $this->config->item('email_config', 'ion_auth');

		if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config))
		{
			$this->email->initialize($email_config);
		}

		$this->ion_auth_model->trigger_events('library_constructor');
	}

	/**
	 * __call
	 *
	 * Acts as a simple way to call model methods without loads of stupid alias'
	 *
	 **/
	public function __call($method, $arguments)
	{
		if (!method_exists( $this->ion_auth_model, $method) )
		{
			throw new Exception('Undefined method Ion_auth::' . $method . '() called');
		}

		return call_user_func_array( array($this->ion_auth_model, $method), $arguments);
	}

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 * I can't remember where I first saw this, so thank you if you are the original author. -Militis
	 *
	 * @access	public
	 * @param	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}


	/**
	 * forgotten password feature
	 *
	 * @return mixed  boolian / array
	 * @author Mathew
	 **/
	public function forgotten_password($identity)    //changed $email to $identity
	{
		if ( $this->ion_auth_model->forgotten_password($identity) )   //changed
		{
			// Get user information
			$user = $this->where($this->config->item('identity', 'ion_auth'), $identity)->users()->row();  //changed to get_user_by_identity from email

			if ($user)
			{
				$data = array(
					'identity'		=> $user->{$this->config->item('identity', 'ion_auth')},
					'forgotten_password_code' => $user->forgotten_password_code
				);

				if(!$this->config->item('use_ci_email', 'ion_auth'))
				{
					$this->set_message('forgot_password_successful');
					return $data;
				}
				else
				{
					$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_forgot_password', 'ion_auth'), $data, true);
					$this->email->clear();
					$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
					$this->email->to($user->email);
					$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_forgotten_password_subject'));
					$this->email->message($message);

					if ($this->email->send())
					{
						$this->set_message('forgot_password_successful');
						return TRUE;
					}
					else
					{
						$this->set_error('forgot_password_unsuccessful');
						return FALSE;
					}
				}
			}
			else
			{
				$this->set_error('forgot_password_unsuccessful');
				return FALSE;
			}
		}
		else
		{
			$this->set_error('forgot_password_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * forgotten_password_complete
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code)
	{
		$this->ion_auth_model->trigger_events('pre_password_change');

		$identity = $this->config->item('identity', 'ion_auth');
		$profile  = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile

		if (!$profile)
		{
			$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		$new_password = $this->ion_auth_model->forgotten_password_complete($code, $profile->salt);

		if ($new_password)
		{
			$data = array(
				'identity'     => $profile->{$identity},
				'new_password' => $new_password
			);
			if(!$this->config->item('use_ci_email', 'ion_auth'))
			{
				$this->set_message('password_change_successful');
				$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_successful'));
					return $data;
			}
			else
			{
				$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_forgot_password_complete', 'ion_auth'), $data, true);

				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
				$this->email->to($profile->email);
				$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_new_password_subject'));
				$this->email->message($message);

				if ($this->email->send())
				{
					$this->set_message('password_change_successful');
					$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_successful'));
					return TRUE;
				}
				else
				{
					$this->set_error('password_change_unsuccessful');
					$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
					return FALSE;
				}

			}
		}

		$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
		return FALSE;
	}

	/**
	 * forgotten_password_check
	 *
	 * @return void
	 * @author Michael
	 **/
	public function forgotten_password_check($code)
	{
		$profile = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile

		if (!is_object($profile))
		{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}
		else
		{
			if ($this->config->item('forgot_password_expiration', 'ion_auth') > 0) {
				//Make sure it isn't expired
				$expiration = $this->config->item('forgot_password_expiration', 'ion_auth');
				if (time() - $profile->forgotten_password_time > $expiration) {
					//it has expired
					$this->clear_forgotten_password_code($code);
					$this->set_error('password_change_unsuccessful');
					return FALSE;
				}
			}
			return $profile;
		}
	}

	/**
	 * invite
	 *
	 * @return void
	 * @author Andrew Welters <awelters@hugmehugyou.org> 
	 **/
	public function invite($username, $email, $additional_data = array(), $group_ids = array()) //need to test email activation
	{
		$this->ion_auth_model->trigger_events('pre_account_creation');

		$id = $this->ion_auth_model->invite($username, $email, $additional_data, $group_ids);

		if (!$id)
		{
			$this->set_error('account_creation_unsuccessful');
			return FALSE;
		}

		$activate = $this->ion_auth_model->activate($id, false, true);

		if (!$activate)
		{
			$this->set_error('activate_unsuccessful');
			$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful'));
			return FALSE;
		}

		$activation_code = $this->ion_auth_model->activation_code;
		$identity        = $this->config->item('identity', 'ion_auth');
		$user            = $this->ion_auth_model->user($id)->row();

		$data = array(
			'identity'   => $user->{$identity},
			'id'         => $user->id,
			'email'      => $email,
			'activation' => $activation_code,
		);
		if(!$this->config->item('use_ci_email', 'ion_auth'))
		{
			$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
			$this->set_message('activation_email_successful');
				return $data;
		}
		else
		{
			$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);

			$this->email->clear();
			$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
			$this->email->to($email);
			$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
			$this->email->message($message);

			if ($this->email->send() == TRUE)
			{
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
				$this->set_message('activation_email_successful');
				return $id;
			}
		}

		$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful', 'activation_email_unsuccessful'));
		$this->set_error('activation_email_unsuccessful');
		return FALSE;
	}

	/**
	 * logout
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function logout()
	{
		$this->ion_auth_model->trigger_events('logout');

		$identity = $this->config->item('identity', 'ion_auth');
		$this->session->unset_userdata($identity);
		$this->session->unset_userdata('id');
		$this->session->unset_userdata('user_id');

		//delete the remember me cookies if they exist
		if (get_cookie('identity'))
		{
			delete_cookie('identity');
		}
		if (get_cookie('remember_code'))
		{
			delete_cookie('remember_code');
		}

		//Destroy the session
		$this->session->sess_destroy();

		//Recreate the session
		if (substr(CI_VERSION, 0, 1) == '2')
		{
			$this->session->sess_create();
		}

		$this->set_message('logout_successful');
		return TRUE;
	}

	/**
	 * logged_in
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function logged_in()
	{
		$this->ion_auth_model->trigger_events('logged_in');

		$identity = $this->config->item('identity', 'ion_auth');

		return (bool) $this->session->userdata($identity);
	}

	/**
	 * logged_in
	 *
	 * @return integer
	 * @author jrmadsen67
	 **/
	public function get_user_id()
	{
		$user_id = $this->session->userdata('user_id');
		if (!empty($user_id))
		{
			return $user_id;
		}
		return null;
	}


	/**
	 * is_admin
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function is_admin($id=false)
	{
		$this->ion_auth_model->trigger_events('is_admin');

		$admin_group = $this->config->item('admin_group', 'ion_auth');

		return $this->in_group($admin_group, $id);
	}
	
	/**
	 * is_group_editor
	 *
	 * @return bool
	 * @author Andrew Welters
	 **/
	public function is_group_editor($id=false)
	{
		$this->ion_auth_model->trigger_events('is_group_editor');

		$group_editor_group = $this->config->item('group_editor_group', 'ion_auth');

		return $this->in_group($group_editor_group, $id);
	}

	/**
	 * in_group
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function in_group($check_group, $id=false)
	{
		$this->ion_auth_model->trigger_events('in_group');

		$id || $id = $this->session->userdata('user_id');

		if (!is_array($check_group))
		{
			$check_group = array($check_group);
		}

		if (isset($this->_cache_user_in_group[$id]))
		{
			$groups_array = $this->_cache_user_in_group[$id];
		}
		else
		{
			$users_groups = $this->ion_auth_model->get_users_groups($id)->result();
			$groups_array = array();
			foreach ($users_groups as $group)
			{
				$groups_array[$group->id] = $group->name;
			}
			$this->_cache_user_in_group[$id] = $groups_array;
		}
		foreach ($check_group as $key => $value)
		{
			$groups = (is_string($value)) ? $groups_array : array_keys($groups_array);

			if (in_array($value, $groups))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * is_group_editable
	 *
	 * @return bool
	 * @author Andrew Welters
	 **/
	public function is_group_editable($check_group)
	{
		$this->ion_auth_model->trigger_events('is_group_editable');

		$admin_group = $this->config->item('admin_group', 'ion_auth');
		$group_editor_group = $this->config->item('group_editor_group', 'ion_auth');

		if($check_group == $admin_group || $check_group == $group_editor_group)
			return FALSE;

		return TRUE;
	}
	
	public function getUsersAndSuperUsers($id = NULL, $notInGroup = false) {
		$usAll = null;
		
		if($id)
		{
			//list the users in the group
			$us = $this->users($id)->result();
			
			//should set an error if no users by that id no????
			
			//list all
			if($notInGroup)
				$usAll = $this->users()->result();
		}
		else
		{
			//list all users
			$us = $this->users()->result();
		}
		
		$users = array();
		$superUsers = array();
		
		//if $usAll is empty null then don't add users if they are $usAll
		if($notInGroup)
		{
			if($us && $usAll)
			{
				foreach ($usAll as $user)
				{
					if( $user->active )
					{
						$inGroup = false;
						foreach ($us as $u)
						{
							if($user->id == $u->id)
							{
								$inGroup = true;
								break;
							}
						}
						if(!$inGroup)
						{
							if($this->is_admin($user->id))
								array_push($superUsers,$user);
							else if($this->is_group_editor($user->id))
								array_push($superUsers,$user);
							else
								array_push($users,$user);
						}
					}
				}
			}
		}
		else if($us) //if $us is not empty
		{
			foreach ($us as $user)
			{
				if( $user->active )
				{
					if($this->is_admin($user->id))
						array_push($superUsers,$user);
					else if($this->is_group_editor($user->id))
						array_push($superUsers,$user);
					else
						array_push($users,$user);
				}
			}
		}
		return array($superUsers, $users);
	}

	/**
	 * emergency_alert
	 *
	 * @return void
	 * @author Andrew Welters <awelters@hugmehugyou.org> 
	 **/
	public function emergency_alert($companionName, $groupId)
	{
		$this->ion_auth_model->trigger_events('emergency_alert');

		if(!$groupId)
		{
			$this->set_error('emergency_alert_no_group_found');
			return FALSE;
		}

		$group = $this->group($groupId)->row();
		
		if(count($group) == 0)
		{
			$this->set_error('emergency_alert_no_group_found');
			return FALSE;
		}

		$us = $this->ion_auth->getUsersAndSuperUsers($groupId);
		
		$groupLeaders = $us[0];
		$groupMembers = $us[1];
		
		if(count($groupLeaders) > 0 || count($groupMembers) > 0)
		{
			$this->load->library('twilio');
			
			$data = array(
				'companion_name' => $companionName,
				'group_name' => $group->name
			);
			
			$smsMessage = $this->load->view($this->config->item('sms_templates', 'ion_auth').$this->config->item('sms_emergency_alert', 'ion_auth'), $data, true);
			$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_emergency_alert', 'ion_auth'), $data, true);
			
			$smsSendError = FALSE;
			$emailSendError = FALSE;
		
			foreach ($groupLeaders as $leader)
			{
				if($leader->mobile_alerts)
				{
					$response = $this->twilio->sms($this->twilio->getPhone(), $leader->phone, $smsMessage);
					if($response->IsError) {
						$smsSendError = TRUE;
						$this->set_error($response->ErrorMessage);
					}
				}
			
				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
				$this->email->to($leader->email);
				$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_emergency_alert_subject'));
				$this->email->message($message);
				
				if (!$this->email->send())
				{
					$emailSendError = TRUE;
				}
			}
			
			foreach ($groupMembers as $member)
			{
				if($member->mobile_alerts)
				{
					$response = $this->twilio->sms($this->twilio->getPhone(), $member->phone, $smsMessage);
					if($response->IsError) {
						$smsSendError = TRUE;
						$this->set_error($response->ErrorMessage);
					}
				}
				
				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
				$this->email->to($member->email);
				$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_emergency_alert_subject'));
				$this->email->message($message);
				
				if (!$this->email->send())
				{
					$emailSendError = TRUE;
				}
			}
			
			if ($emailSendError)
			{
				$this->set_error('emergency_alert_email_sent_error');
				return FALSE;
			}
			else if($smsSendError)
				return FALSE;
		}
		else
		{
			$this->set_error('emergency_alert_no_group_members');
			return FALSE;
		}
		
		return TRUE;
	}
}
