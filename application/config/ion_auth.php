<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Database Type
| -------------------------------------------------------------------------
| If set to TRUE, Ion Auth will use MongoDB as its database backend.
|
| If you use MongoDB there are two external dependencies that have to be
| integrated with your project:
|   CodeIgniter MongoDB Active Record Library - http://github.com/alexbilbie/codeigniter-mongodb-library/tree/v2
|   CodeIgniter MongoDB Session Library - http://github.com/sepehr/ci-mongodb-session
*/
$config['use_mongodb'] = FALSE;

/*
| -------------------------------------------------------------------------
| MongoDB Collection.
| -------------------------------------------------------------------------
| Setup the mongodb docs using the following command:
| $ mongorestore sql/mongo
|
*/
$config['collections']['users']          = 'users';
$config['collections']['groups']         = 'groups';
$config['collections']['login_attempts'] = 'login_attempts';

/*
| -------------------------------------------------------------------------
| Tables.
| -------------------------------------------------------------------------
| Database table names.
*/
$config['tables']['users']           = 'users';
$config['tables']['groups']          = 'groups';
$config['tables']['users_groups']    = 'users_groups';
$config['tables']['login_attempts']  = 'login_attempts';

/*
 | Users table column and Group table column you want to join WITH.
 |
 | Joins from users.id
 | Joins from groups.id
 */
$config['join']['users']  = 'user_id';
$config['join']['groups'] = 'group_id';

/*
 | -------------------------------------------------------------------------
 | Hash Method (sha1 or bcrypt)
 | -------------------------------------------------------------------------
 | Bcrypt is available in PHP 5.3+
 |
 | IMPORTANT: Based on the recommendation by many professionals, it is highly recommended to use
 | bcrypt instead of sha1.
 |
 | NOTE: If you use bcrypt you will need to increase your password column character limit to (80)
 |
 | Below there is "default_rounds" setting.  This defines how strong the encryption will be,
 | but remember the more rounds you set the longer it will take to hash (CPU usage) So adjust
 | this based on your server hardware.
 |
 | If you are using Bcrypt the Admin password field also needs to be changed in order login as admin:
 | $2a$07$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36
 |
 | Becareful how high you set max_rounds, I would do your own testing on how long it takes
 | to encrypt with x rounds.
 */
$config['hash_method']    = 'bcrypt';	// IMPORTANT: Make sure this is set to either sha1 or bcrypt
$config['default_rounds'] = 8;		// This does not apply if random_rounds is set to true
$config['random_rounds']  = FALSE;
$config['min_rounds']     = 5;
$config['max_rounds']     = 9;

/*
 | -------------------------------------------------------------------------
 | Authentication options.
 | -------------------------------------------------------------------------
 | maximum_login_attempts: This maximum is not enforced by the library, but is
 | used by $this->ion_auth->is_max_login_attempts_exceeded().
 | The controller should check this function and act
 | appropriately. If this variable set to 0, there is no maximum.
 */
$config['site_title']                 = isset($_SERVER['CI_SITE_TITLE']) ? $_SERVER['CI_SITE_TITLE'] : "Example.com";       // Site Title, example.com
$config['admin_email']                = isset($_SERVER['CI_ADMIN_EMAIL']) ? $_SERVER['CI_ADMIN_EMAIL'] : "admin@example.com"; // Admin Email, admin@example.com
$config['default_group']              = 'testers';           // Default group, use name
$config['admin_group']                = 'admin';             // Default administrators group, use name
$config['group_editor_group']         = 'social worker';     // Default group editor group, use name
$config['identity']                   = 'email';             // A database column which is used to login with
$config['min_password_length']        = 8;                   // Minimum Required Length of Password
$config['max_password_length']        = 20;                  // Maximum Allowed Length of Password
$config['email_activation']           = TRUE;               // Email Activation for registration
$config['manual_activation']          = FALSE;               // Manual Activation for registration
$config['remember_users']             = FALSE;                // Allow users to be remembered and enable auto-login
$config['user_expire']                = 86400*365; //1 year   // How long to remember the user (seconds). Set to zero for no expiration
$config['user_extend_on_login']       = TRUE;               // Extend the users cookies everytime they auto-login
$config['track_login_attempts']       = TRUE;               // Track the number of failed login attempts for each user or ip.
$config['maximum_login_attempts']     = 3;                   // The maximum number of failed login attempts.
$config['lockout_time']               = 600;                 // The number of miliseconds to lockout an account due to exceeded attempts
$config['forgot_password_expiration'] = 0;                   // The number of miliseconds after which a forgot password request will expire. If set to 0, forgot password requests will not expire.


/*
 | -------------------------------------------------------------------------
 | Email options.
 | -------------------------------------------------------------------------
 | email_config:
 | 	  'file' = Use the default CI config or use from a config file
 | 	  array  = Manually set your email config settings
 */
$config['use_ci_email'] = TRUE; // Send Email using the builtin CI email class, if false it will return the code and the identity
$config['email_config'] = array(
	'useragent' => isset($_SERVER['CI_EMAIL_CONFIG_USER_AGENT']) ? $_SERVER['CI_EMAIL_CONFIG_USER_AGENT'] : 'CodeIgniter',
	'protocol' => isset($_SERVER['CI_EMAIL_CONFIG_PROTOCOL']) ? $_SERVER['CI_EMAIL_CONFIG_PROTOCOL'] : 'mail',
	'smtp_host' => isset($_SERVER['CI_EMAIL_CONFIG_SMTP_HOST']) ? $_SERVER['CI_EMAIL_CONFIG_SMTP_HOST'] : '',
	'smtp_user' => isset($_SERVER['CI_EMAIL_CONFIG_SMTP_USER']) ? $_SERVER['CI_EMAIL_CONFIG_SMTP_USER'] : '',
	'smtp_pass' => isset($_SERVER['CI_EMAIL_CONFIG_SMTP_PASS']) ? $_SERVER['CI_EMAIL_CONFIG_SMTP_PASS'] : '',
	'smtp_crypto' => isset($_SERVER['CI_EMAIL_CONFIG_SMTP_CRYPTO']) ? $_SERVER['CI_EMAIL_CONFIG_SMTP_CRYPTO'] : '',
	'smtp_port' => isset($_SERVER['CI_EMAIL_CONFIG_SMTP_PORT']) ? $_SERVER['CI_EMAIL_CONFIG_SMTP_PORT'] : '25',
	'smtp_timeout' => isset($_SERVER['CI_EMAIL_CONFIG_SMTP_TIMEOUT']) ? $_SERVER['CI_EMAIL_CONFIG_SMTP_TIMEOUT'] : '5',
	'email_crlf' => isset($_SERVER['CI_EMAIL_CONFIG_EMAIL_CRLF']) ? $_SERVER['CI_IEMAIL_CONFIG_EMAIL_CRLF'] : '\n',
	'email_newline' => isset($_SERVER['CI_EMAIL_CONFIG_EMAIL_NEWLINE']) ? $_SERVER['CI_EMAIL_CONFIG_EMAIL_NEWLINE'] : '\n',
	'mailtype' => 'html',
	'charset' => 'utf-8',
);

/*
 | -------------------------------------------------------------------------
 | Email templates.
 | -------------------------------------------------------------------------
 | Folder where email templates are stored.
 | Default: auth/
 */
$config['email_templates'] = 'user/email/';

/*
 | -------------------------------------------------------------------------
 | SMS templates.
 | -------------------------------------------------------------------------
 | Folder where sms templates are stored.
 | Default: auth/
 */
$config['sms_templates'] = 'user/sms/';

/*
 | -------------------------------------------------------------------------
 | Activate Account Email Template
 | -------------------------------------------------------------------------
 | Default: activate.tpl.php
 */
$config['email_activate'] = 'activate.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Forgot Password Email Template
 | -------------------------------------------------------------------------
 | Default: forgot_password.tpl.php
 */
$config['email_forgot_password'] = 'forgot_password.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Forgot Password Complete Email Template
 | -------------------------------------------------------------------------
 | Default: new_password.tpl.php
 */
$config['email_forgot_password_complete'] = 'new_password.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Emergency Alert Email Template
 | -------------------------------------------------------------------------
 | Default: emergency_alert.tpl.php
 */
$config['email_emergency_alert'] = 'emergency_alert.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Emergency Alert SMS Template
 | -------------------------------------------------------------------------
 | Default: emergency_alert.tpl.php
 */
$config['sms_emergency_alert'] = 'emergency_alert.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Salt options
 | -------------------------------------------------------------------------
 | salt_length Default: 10
 |
 | store_salt: Should the salt be stored in the database?
 | This will change your password encryption algorithm,
 | default password, 'password', changes to
 | fbaa5e216d163a02ae630ab1a43372635dd374c0 with default salt.
 */
$config['salt_length'] = 10;
$config['store_salt']  = FALSE;

/*
 | -------------------------------------------------------------------------
 | Message Delimiters.
 | -------------------------------------------------------------------------
 */
$config['message_start_delimiter'] = '<p>'; 	// Message start delimiter
$config['message_end_delimiter']   = '</p>'; 	// Message end delimiter
$config['error_start_delimiter']   = '<p>';		// Error mesage start delimiter
$config['error_end_delimiter']     = '</p>';	// Error mesage end delimiter

/* End of file ion_auth.php */
/* Location: ./application/config/ion_auth.php */
