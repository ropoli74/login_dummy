<?php
/**
 * @package login_dummy
 * @author Roland Lang
 */

require_once(dirname(__FILE__).'/Session.php');
require_once(dirname(__FILE__).'/User.php');

class Auth
{
    private static $instance = NULL;

    protected $name;
    protected $session;
    protected $cookie_key = 'cookie_key';
    protected $session_key = 'session_key';
    protected $lifetime = 604800;

    /**
     * create a singleton
     *
     * @return Auth
     */
    public static function instance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new Auth();
        }

        return self::$instance;
    }

    /**
     * @param string $name
     */
    private function __construct($name = 'auth_dummy')
    {
        $this->name = $name;
        $this->session = Session::instance();
        $this->session_key = $this->session_key.'_'.$this->name;
        $this->cookie_key = $this->cookie_key.'_'.$this->name;
    }

    /**
     * @return bool
     */
    public function is_logged_in()
    {
        return $this->get_user() instanceof User;
    }

    /**
     * @return bool|null|User
     */
    public function get_user()
    {
        $user = $this->session->get($this->session_key);

        if ($user instanceof User)
        {
            if ( ! is_null($user->get_id()))
            {
                return $user;
            }
            else
            {
                $this->logout(TRUE);
                return FALSE;
            }
        }

        if ($this->lifetime > 0)
        {
            $token = Cookie::get($this->cookie_key);

            if (is_null($token))
            {
                return FALSE;
            }

            $token = explode('.', $token);

            if (count($token) === 2
                AND is_string($token[0])
                AND ! empty($token[1]))
            {
                $user = User::factory_by_token($token[0]);
                if ( ! is_null($user->get_id()))
                {
                    return $this->complete_login($user, TRUE);
                }
            }
        }

        return FALSE;
    }

    /**
     * @param string $username
     * @param string $password
     * @param bool|FALSE $remember
     * @return bool|User
     */
    public function login($username, $password, $remember = FALSE)
    {
        if (empty($username)
            OR empty($password))
        {
            return FALSE;
        }

        $user = User::factory_by_username($username);

        if (password_verify($password, $user->get_password()))
        {
            return $this->complete_login($user, $remember);
        }
		else
		{
			// Here you can write some Logs with a LogWriter class
			// e.g. count the login failures of the current ip to block the login after
			// a defined count of tries...
		}

        return FALSE;

    }

    /**
     * @param User $user
     * @param bool|FALSE $remember
     * @return User
     */
    protected function complete_login(User $user, $remember = FALSE)
    {
        $this->session->regenerate();
        $this->session->set($this->session_key, $user);

		if ($this->lifetime > 0 AND $remember === TRUE)
		{
			$token = bin2hex(uniqid());
			$user->set_token($token);
			Cookie::set($this->cookie_key, $this->create_user_token($user, $token), $this->lifetime);
		}
		else
		{
			$this->reset_user_token($user);
		}

		$user->increase_logins();
		$user->save();

        return $user;
    }

    /**
     * @param bool|FALSE $destroy
     */
    public function logout($destroy = FALSE)
    {
        // the remember cookie
        if (Cookie::get($this->cookie_key))
        {
            Cookie::delete($this->cookie_key);
        }

        $this->session->delete($this->session_key);
        $this->session->regenerate();

        if ($destroy)
        {
            $this->session->destroy();
        }
    }

    /**
     * @param User $user
     * @param string $token
     * @return string
     */
    protected function create_user_token(User $user, $token)
    {
        return $token.'.'.$user->get_id();
    }

	/**
	 * @param User $user
	 */
	public function reset_user_token(User $user)
	{
		$user->set_token('');
	}
}