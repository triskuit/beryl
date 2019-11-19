<?php

class Session
{

    private $last_login, $passed_id;

    public $username, $user_id;

    public const MAX_LOGIN_AGE = 60 * 60 * 24;

    public function __construct()
    {
        session_start();
        $this->check_stored_login();
    }

    public function login($user)
    {
        if ($user) {
            // prevent session fixation attacks
            session_regenerate_id();
            $this->user_id = $_SESSION['user_id'] = $user->id;
            $this->username = $_SESSION['username'] = $user->username;
            $this->last_login = $_SESSION['last_login'] = time();
        }
        return true;
    }

    public function is_logged_in()
    {
        return isset($this->user_id) && $this->last_login_is_recent();
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['last_login']);
        unset($this->user_id);
        unset($this->username);
        unset($this->last_login);
        return true;
    }

    private function check_stored_login()
    {
        if (isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
            $this->username = $_SESSION['username'];
            $this->last_login = $_SESSION['last_login'];
            $this->passed_id = $_SESSION['passed_id'] ?? 0;
        }
    }

    private function last_login_is_recent()
    {
        if (! isset($this->last_login)) {
            return false;
        } elseif (($this->last_login + self::MAX_LOGIN_AGE) < time()) {
            return false;
        } else {
            return true;
        }
    }

    public function message($msg_text = "", $msg_type = "primary")
    {
        if (! empty($msg_text)) {
            $_SESSION['message']['text'] = $msg_text;
            $_SESSION['message']['type'] = $msg_type;
            return true;
        } else {
            $output = $_SESSION['message_text'] ?? '';
            self::clear_message();
            return $output;
        }
    }

    public function clear_message()
    {
        unset($_SESSION['message']['text']);
        unset($_SESSION['message']['type']);
    }

    public function print_message()
    {
        if(isset($_SESSION['message']['text']) && $_SESSION['message']['text'] != ''){
            $output = sprintf("<div class='alert alert-%s alert-dismissible fade show' role='alert'>%s<button type='button' class='close' data-dismiss='alert' aria-label='Close'> <span aria-hidden='true'>&times;</span> </button></div>", $_SESSION['message']['type'],$_SESSION['message']['text']);
            $this->clear_message();
            echo $output;
            return;
        } else {
            return false;
        }
    }
    
    public function set_passed_id($id){
        $_SESSION['passed_id'] = $id;
        $this->passed_id = $id;
        return true;
    }
    
    public function get_passed_id(){
        $output = $this->passed_id ?? 0;
        unset($_SESSION['passed_id']);
        unset($this->passed_id);
        return $output;
    }
    
    public function unset_passed_id(){
        unset($_SESSION['passed_id']);
        unset($this->passed_id);
    }
}
