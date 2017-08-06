<?php

class Authentication {
    /**
     * Stores the instance to the database
     */
    protected $db;
    /**
     * Stores the generated CSRF toke
     */
    protected $csrf;
    /**
     * User instance variables
     */
    public
        $id,
        $username,
        $point,
        $email,
        $expired,
        $settings;

    /**
     * Store an instance of the core class
     */
    protected static $instance;

    /**
     * Class options
     */
    protected $options = array();

    /**
     * Global config
     */
    private  $config;

    /**
     * Protected no one can instantiate an object by calling new
     */
    protected function __construct() {  }

    /**
     * Connect to the database and return an instance of the
     * connection.
     */
    protected function connect() {
        global $db;
        /**
         * Try and connect to the database, this information can be
         * configured in the inc/config.php file do not change them here.
         */
        $this->db = $db;

        if ( !isset($_SESSION['CSRF_TOKEN']) )
            $this->csrf_token();
    }


    public static function init() {
        global $config;

        if ( !self::$instance ) {
            self::$instance = new authentication();
        }
        $core = self::$instance; // We have an instance of the core class.
        $core->config = $config;
        $core->connect(); // Connect to the database.
        $core->set_user_data(); // Check, we may have user data.

        // Ok we are done in here return the core.
        return $core;
    }

    public function set_user_data() {
        // Check to see if the user is logged in, also make some ivars
        // available just so they can be used rather than session variables.
        if ( isset($_SESSION['LOGGED_IN']) && $_SESSION['LOGGED_IN'] === true ) {
            # We have a logged in user..
            $this->id = $_SESSION['USER_ID'];
            $this->username = $_SESSION['USERNAME'];
            $this->point = $_SESSION['POINT'];
            $this->email = $_SESSION['EMAIL'];
            $this->lr = $_SESSION['LR'];
        }
    }

    public function set_options($options) {
        $this->options = $options;
    }

    public function is_login()
    {
        if ( !isset( $_SESSION['LOGGED_IN'] ) ) return false;
        else return true;
    }

    public function secure( $validate_users = null ) {

        if ( !empty( $this->options ) ) {
            $options = $this->options; // Set the options.
        }

        // Do we have a login session?
        if ( !isset( $_SESSION['LOGGED_IN'] ) ) {
            // No, no session has been found so redirect
            // the user to login
            return header('Location: ' . $this->config['SITE_URL'] .
                    (isset($options) ?
                        (array_key_exists('redirect', $options) ?
                            $options['redirect'] : 'account/login' ) : 'account/login')
            );
        }

    }

    /**
     * Used to protected forms from Cross Site Request Forgery.
     * Because our application accepts user input it's important
     * we protect it against such attacks. To find out what CSRF is
     * and why it's important check the link below.
     *
     * Link: http://en.wikipedia.org/wiki/Cross-site_request_forgery
     */
    public function csrf_token() {
        $this->csrf = md5(uniqid().rand());
        $_SESSION['CSRF_TOKEN'] = $this->csrf;
        return $this->csrf;
    }

    /**
     * Grabs the list of users.
     */
    public function fetch_users() {
        $sql = $this->db->sql_query("SELECT * FROM users ORDER BY id DESC");
        $row = $this->db->sql_fetchrow($sql);
        return $row;
    }

    /**
     * Adds users to the database.
     */
    public function add_user($username, $email, $password, $rpassword, $lr, $status, $new = false) {

        $_SESSION['L_USERNAME'] = $username;
        $_SESSION['L_EMAIL'] = $email;
        $_SESSION['L_LR'] = $lr;

        if ( strlen($password) == 0 ) {
            $password = $this->random_password();
        } else if (strlen($password) <= 5) {
            return json_encode(array(
                'error' => true,
                'message' => 'Password is too short! Please enter another or leave blank!'
            ));
        }
        if ($password != $rpassword) {
            return json_encode(array(
                'error' => true,
                'message' => 'Two password is not the same!'
            ));
        }
        if (!Helper::isValidAccountNumber($lr)) {
            return json_encode(array(
                'error' => true,
                'message' => 'LibertyReserve Account is not valid!'
            ));
        }
        // Validation time.
        if ( $this->in_use($username, 'username') ) {
            return json_encode(array(
                'error' => true,
                'message' => $username . ' is already in use. Please choose another!'
            ));
        }
        if ( $this->in_use($email, 'email') ) {
            return json_encode(array(
                'error' => true,
                'message' => $email . ' is already in use. Please choose another!'
            ));
        }
        // Ok validate email.
        if ( !$this->valid_email_address($email) ) {
            return json_encode(array(
                'error' => true,
                'message' => $email . ' is not a valid email address, please enter another one!'
            ));
        }

        // All the hashing, hashing away
        $pass_unhashed = $password;
        $password = sha1($password);

        // Some prep
        // $status = (int)$status;

        if ( $status == 3 ) {
            // We have an admin.
            $data[':is_admin'] = 1;
        } else {
            $data[':is_admin'] = 0;
        }

        // Is this users status awaiting activation?
        if ( $status == 0 ) {
            $data[':reset_token'] = md5(time().mt_rand());
            $validation_email = true;
        }

        // Gather the rest of the variables.
        $data[':username'] = strtolower($username);
        $data[':email'] = strtolower($email);
        $data[':status'] = $status;
        $data[':password'] = $password;
        $data[':lr'] = $lr;
        $data[':point']     = '10';

        $sql = $this->db->sql_query("INSERT INTO users (username,email,status,pass,point,lr) VALUES ('".$data[':username']."', '".$data[':email']."', '".$data[':status']."', '".$data[':password']."', '".$data[':point']."', '".$data[':lr']."')");

        // Check to see if it was successful
        if ( $this->db->sql_affectedrows() > 0 ) {
            /**
             * All is well with the world thus far.
             *
             * We need to send a welcome email to the user. However
             * there are two possible emails to send. A user that has to
             * verify his/her account and one that doesnt.
             */
            $e = new email;
            $e->to($email);
            $e->from('welcome@' . $this->config['EMAIL_EXT']);
            $e->subject('Welcome to ' . $this->config['SITE_NAME'] . ' ' . $username);

            if ( $new == true ) {
                // Send this email
                $e->message("Hello $username,

Welcome to ".$this->config['SITE_NAME']." this is just a quick email confirming your new account over at ".$this->config['SITE_NAME']." and
to drop off your account details. So to login simply head over to ".$this->config['SITE_URL']."account/login and enter the following info:

== YOUR LOGIN DETAILS
Username: $username
Password: $pass_unhashed

Cheers.");

            } else {
                // Send this email
                $e->message("Hello $username,

Welcome to ".$this->config['SITE_NAME']." this is just a quick email confirming your new account over at ".$this->config['SITE_NAME']." and
to drop off your account details. So to login simply head over to ".$this->config['SITE_URL']."account/login and enter the following info:

== YOUR LOGIN DETAILS
Username: $username
Password: $pass_unhashed

Cheers.");
            }

            // Send the email address to the new user.
            $e->send();

            return json_encode(
                array(
                    'error' => false,
                    'message' => 'Success, your account has been created. You can login now!'
                )
            );

        } else {
            // Something went wrong boss.
            return json_encode(array(
                'error' => true,
                'message' => 'System failure failed to insert user into the database. Please try again!'
            ));
        }

    }

    public function captcha() {
        $sum1 = mt_rand(1, 9);
        $sum2 = mt_rand(1, 9);
        $_SESSION['CAPTCHA'] = $sum1 + $sum2;
        return $sum1 . ' + ' . $sum2 . ' = ';
    }

    protected function in_use($item, $table) {
        $item = strip_tags(stripslashes(strtolower($item)));
        $sql = $this->db->sql_query("SELECT * FROM users WHERE ".$table."='".$item."'");
        $row = $this->db->sql_fetchrow($sql);
        if ( $this->db->sql_affectedrows() > 0 ) {
            // We have a match
            return true;
        } else
            return false;
    }

    /**
     * Grabs a single user from their ID.
     *
     * @var <int> $id - The users ID you wish to pull from the database
     */
    public function get_user_from_id($id) {
        global $db;
        $id = (int)$id; // Cast to int.
        $sql = $this->db->sql_query("SELECT * FROM users WHERE id='".$id."'");
        $row = $this->db->sql_fetchrow($sql);
        if ( $this->db->sql_affectedrows() > 0)
            return $row;
        return false;
    }

    /**
     * Grabs a single user from their email address
     */
    public function get_user_from_email($email) {
        $sql = $this->db->sql_query("SELECT * FROM users WHERE email='".$email."'");
        $row = $this->db->sql_fetchrow($sql);
        if ($this->db->sql_affectedrows() > 0)
            return $row;
        return false;
    }

    public function current_status($num, $status) {
        if ($num == $status)
            return 'selected';
    }

    /**
     * User friendly status
     */
    public function status($status) {
        switch ($status) {
            case 1:
                return 'Activated';
                break;
            case 2:
                return 'Admin';
                break;
            case 3:
                return 'Banned';
                break;
            default:
                return 'Unknown';
                break;
        }
    }

    /**
     * Checks to see if the user is logged in, if they are
     * then it will redirect them to their login location.
     */
    protected function is_logged_in() {
        if ( isset($_SESSION['LOCATION']) ) {
            header('Location: ' . $this->config['SITE_URL'] . $_SESSION['LOCATION']);
            return true;
        }
    }

    /**
     * refresh_data is called to update a members user data. ID is passed and
     * the users session data will be refreshed.
     */
    public function refresh($id) {
        $sql = $this->db->sql_query("SELECT * FROM users WHERE id='".$id."'");
        $row = $this->db->sql_fetchrow($sql);
        if ( $this->db->sql_affectedrows() > 0 ) {
            $_SESSION['LOGGED_IN'] = true;
            $_SESSION['USER_ID'] = $row['id'];
            $_SESSION['USERNAME'] = $row['username'];
            $_SESSION['POINT'] = $row['point'];
            $_SESSION['EMAIL'] = $row['email'];
            $_SESSION['LR'] = $row['lr'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks to see if the user has entered the correct login details.
     * If they have then it will redirect them to their success page.
     */
    public function validate_user($user, $pass) {

        // Clean the variables up.
        $user = strip_tags($user);
        $pass = strip_tags($pass);
        $pass_hash = sha1($pass);
        $_SESSION['L_USERNAME'] = $user;

        // Build the query
        $sql = $this->db->sql_query("SELECT * FROM users WHERE username='".$user."' AND pass='".$pass_hash."'");
        $row = $this->db->sql_fetchrow($sql);

        // Check to see if the user has failed to login too many times.
        if ( !isset($_SESSION['LOGIN_ATTEMPTS']) )
            $_SESSION['LOGIN_ATTEMPTS'] = 0;

        if ($_SESSION['LOGIN_ATTEMPTS'] >= 5) {
            return json_encode( array( 'error' => true, 'message' => 'Too many failed login attempts. Come back later!' ) );
        }

        if ($this->db->sql_affectedrows() >= 1) {
            // We have a user.
            // Set the session variables.

            // Check to see if the user is banned.
            if ( $row['status'] == 3 ) {
                return json_encode(
                    array(
                        'error' => true,
                        'message' => 'This account has been banned.'
                    ));
            }

            $_SESSION['LOGGED_IN'] = true;
            $_SESSION['USER_ID'] = $row['id'];
            $_SESSION['USERNAME'] = $row['username'];
            $_SESSION['POINT'] = $row['point'];
            $_SESSION['EMAIL'] = $row['email'];
            $_SESSION['LR'] = $row['lr'];
            return json_encode(
                array('error' => false,
                    'message' => 'Success, welcome ' . $row['username'] . ' you are now logged in.')
            );
        } else {
            // No user.
            $_SESSION['LOGIN_ATTEMPTS'] += 1;
            return json_encode(array('error' => true, 'message' => 'It seems you have entered an incorrect username and or password.'));
        }

    }

    /**
     * Validates email addresses
     */
    protected function valid_email_address($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Generates a random user password. I did this so administrators can't set user
     * passwords instead they allow the system to set one.
     */
    protected function random_password($len = 6) {
        $pass = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@£$%^&*";
        $maxlen = strlen($chars);
        if ( $len > $maxlen )
            $len = $maxlen;
        $i = 0;
        while ($i < $len) {
            $rdm_char = substr($chars, mt_rand(0, $maxlen-1), 1);
            if (!strstr($pass, $rdm_char))
                $pass .= $rdm_char;
            $i++;
        }
        return $pass;
    }

    /**
     * Logs the user out destroys all their session and then
     * redirects them back to the login (Default)
     *
     * @var <string> $location - Where to redirect the user after the logout is
     * successful? Default is login.php you can also pass URLS (Eg. http://john-crossley.me)
     */
    public static function logout($location = 'account/login') {
        global $config;

        if (isset($_SESSION['LOGGED_IN'])) {
            if ( session_destroy() )
                header('Location: ' . $config['SITE_URL'] . $location);
        } else header('Location: ' . $config['SITE_URL'] . $location);
    }

}