<?php
/**
 * @package login_dummy
 * @author Roland Lang
 */

require_once(dirname(__FILE__).'/Cookie.php');

class Session
{
    private static $instance = NULL;

    protected $name = 'session';
    protected $lifetime = 0;
    protected $data = array();
    protected $destroyed = FALSE;

	/**
	 * create a singleton
	 *
	 * @param null $session_id
	 * @return Session
	 */
    public static function instance($session_id = NULL)
    {
        if (is_null(self::$instance))
        {
            self::$instance = $session = new Session($session_id);
            register_shutdown_function(array($session, 'write'));
        }

        return self::$instance;
    }

	/**
	 * @param null $session_id
	 */
    private function __construct($session_id = NULL)
    {
        $this->read($session_id);
    }

	/**
	 * @return string
	 */
    public function id()
    {
        return session_id();
    }

	/**
	 * @return string
	 */
    function __toString()
    {
        /**
         * @todo do better encryption here
         */
        $data = serialize($this->data);
        $data = base64_encode($data);
        return $data;
    }

	/**
	 * @return bool
	 */
    public function write()
    {
        if (headers_sent() OR $this->destroyed)
        {
            return FALSE;
        }

        session_write_close();
        return TRUE;
    }

	/**
	 * @param null $session_id
	 */
    public function read($session_id = NULL)
    {
		Cookie::$httponly = TRUE;

		if ($this->is_SSL())
		{
			Cookie::$secure = TRUE;
		}

        session_set_cookie_params(
            $this->lifetime,
            Cookie::$path,
            Cookie::$domain,
            Cookie::$secure,
            Cookie::$httponly
        );

        session_cache_limiter(FALSE);
        session_name($this->name);

        if ($session_id)
        {
            session_id($session_id);
        }

		ini_set('session.use_only_cookies', 1);
        session_start();

        $this->data =& $_SESSION;
    }

	/**
	 * @return string
	 */
    public function regenerate()
    {
        session_regenerate_id(TRUE);
        return session_id();
    }

	/**
	 * @param $key
	 */
    public function delete($key)
    {
        if (array_key_exists($key, $this->data))
        {
            unset($this->data[$key]);
        }
    }

	/**
	 * @param string $key
	 * @param mixed $value
	 */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return null
	 */
    public function get($key, $default = NULL)
    {
        if (array_key_exists($key, $this->data))
        {
            return $this->data[$key];
        }

        return $default;
    }

	/**
	 * @return bool
	 */
    public function destroy()
    {
		$status = FALSE;

		if ($this->destroyed === FALSE)
		{
			session_destroy();
			$status = ! session_id();

			if ($status)
			{
				Cookie::delete($this->name);
			}

			$this->destroyed = TRUE;
		}

        return $status;
    }

	/**
	 * @return bool
	 */
	protected function is_SSL()
	{
		if( ! empty( $_SERVER['HTTPS'] ))
		{
			return TRUE;
		}

		if( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] )
			AND $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
		{
			return TRUE;
		}

		return FALSE;
	}
}