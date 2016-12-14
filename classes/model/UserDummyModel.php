<?php
/**
 * @package login_dummy
 * @author Roland Lang
 */

class UserDummyModel
{
    public $id;
    public $email;
    public $firstname;
    public $lastname;
    public $password;
    public $token = NULL;
	public $logins = 0;

    /**
     * @param int $id
     * @return null|UserDummyModel
     */
    public static function get_by_id($id)
    {
        $user_dummy_model = new UserDummyModel();

        $data = $user_dummy_model->read_file();

        if ( ! $data
            OR ! is_array($data)
            OR ! array_key_exists($id, $data))
        {
            return NULL;
        }

        $user_dummy_model->id = $data[$id]['id'];
        $user_dummy_model->email = $data[$id]['email'];
        $user_dummy_model->firstname = $data[$id]['firstname'];
        $user_dummy_model->lastname = $data[$id]['lastname'];
        $user_dummy_model->password = $data[$id]['password'];
        $user_dummy_model->token = $data[$id]['token'];
        $user_dummy_model->logins = $data[$id]['logins'];

        return $user_dummy_model;
    }

    public static function get_by_username($username)
    {
        $user_dummy_model = new UserDummyModel();

        $data = $user_dummy_model->read_file();

        foreach($data AS $key => $userdata)
        {
            if ($userdata['email'] == $username)
            {
                return self::get_by_id($key);
            }
        }
    }

    public static function get_by_token($token)
    {
        $user_dummy_model = new UserDummyModel();

        $data = $user_dummy_model->read_file();

        foreach($data AS $key => $userdata)
        {
            if ($userdata['token'] == $token)
            {
                return self::get_by_id($key);
            }
        }
    }

    /**
     * save to data storage
     */
    public function save()
    {
        $data = $this->read_file();

        if (( ! $data))
        {
            $data = array();
        }

        $data[$this->id] = array(
            'id' => $this->id,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'password' => $this->password,
            'token' => $this->token,
            'logins' => $this->logins,
        );

        $this->write_file($data);
    }

    /**
     * read storage file
     * @return array|boolean
     */
    protected function read_file()
    {
        $handle = fopen(dirname(__FILE__).'/../../data/data.txt', 'r');
        $data = stream_get_contents($handle);
        fclose($handle);

		if (empty($data))
		{
			$data = serialize(array());
		}

        return unserialize($data);
    }

    /**
     * @param array $data
     */
    protected function write_file(Array $data)
    {
        $handle = fopen(dirname(__FILE__).'/../../data/data.txt', 'w');
        fwrite($handle, serialize($data));
        fclose($handle);
    }

}