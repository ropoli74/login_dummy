<?php
/**
 * @package login_dummy
 * @author Roland Lang
 */

require_once(dirname(__FILE__).'/model/UserDummyModel.php');
require_once(dirname(__FILE__).'/Auth.php');

class User
{
	protected $id;
	protected $email;
	protected $firstname;
	protected $lastname;
	protected $password;
	protected $token = NULL;
	protected $logins = 0;

	/**
	 * @return int
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function set_id($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function get_email()
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function set_email($email)
	{
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function get_firstname()
	{
		return $this->firstname;
	}

	/**
	 * @param string $firstname
	 */
	public function set_firstname($firstname)
	{
		$this->firstname = $firstname;
	}

	/**
	 * @return string
	 */
	public function get_lastname()
	{
		return $this->lastname;
	}

	/**
	 * @param string $lastname
	 */
	public function set_lastname($lastname)
	{
		$this->lastname = $lastname;
	}

	/**
	 * @return string
	 */
	public function get_password()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function set_password($password)
	{
		$this->password = $password;
	}

	/**
	 * @return null|string
	 */
	public function get_token()
	{
		return $this->token;
	}

	/**
	 * @param string $token
	 */
	public function set_token($token)
	{
		$this->token = $token;
	}

	/**
	 * @return int
	 */
	public function get_logins()
	{
		return $this->logins;
	}

	/**
	 * @param int $logins
	 */
	public function set_logins($logins)
	{
		$this->logins = $logins;
	}

	/**
	 * add +1 to logins
	 */
	public function increase_logins()
	{
		$this->logins += 1;
	}

	/**
	 * @return User
	 */
	public static function factory_by_id($id)
	{
		$model = UserDummyModel::get_by_id($id);

		$user = new User();

		if (!is_null($model))
		{
			$user->restore_from_model($model);
		}

		return $user;
	}

	/**
	 * @return bool|User
	 */
	public static function factory_from_session()
	{
		$user = Auth::instance()->get_user();

		if ( ! $user instanceof User)
		{
			$user = new User();
		}

		return $user;
	}

	/**
	 * @return bool|User
	 */
	public static function factory_by_username($username)
	{
		$model = UserDummyModel::get_by_username($username);

		$user = new User();

		if (!is_null($model))
		{
			$user->restore_from_model($model);
		}

		return $user;
	}

	/**
	 * @param $token
	 * @return User
	 */
	public static function factory_by_token($token)
	{
		$model = UserDummyModel::get_by_token($token);

		$user = new User();

		if (!is_null($model))
		{
			$user->restore_from_model($model);
		}

		return $user;
	}

	/**
	 * @param UserDummyModel $model
	 */
	public function restore_from_model(UserDummyModel $model)
	{
		$this->set_id($model->id);
		$this->set_email($model->email);
		$this->set_firstname($model->firstname);
		$this->set_lastname($model->lastname);
		$this->set_password($model->password);
		$this->set_token($model->token);
		$this->set_logins($model->logins);
	}

	/**
	 * save to the model
	 */
	public function save()
	{
		$model = UserDummyModel::get_by_id($this->get_id());
		if (!$model instanceof UserDummyModel)
		{
			$model = new UserDummyModel();
		}

		$model->id = $this->get_id();
		$model->email = $this->get_email();
		$model->firstname = $this->get_firstname();
		$model->lastname = $this->get_lastname();
		$model->password = $this->get_password();
		$model->token = $this->get_token();
		$model->logins = $this->get_logins();

		$model->save();
	}

	/**
	 * @return bool
	 */
	public static function is_logged_in()
	{
		return Auth::instance()->is_logged_in();
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @param bool|FALSE $remember
	 * @return bool|User
	 */
	public function login($username, $password, $remember = FALSE)
	{
		return Auth::instance()->login($username, $password, $remember);
	}

	/**
	 * @param bool|FALSE $destroy
	 */
	public function logout($destroy = FALSE)
	{
		return Auth::instance()->logout($destroy);
	}
}