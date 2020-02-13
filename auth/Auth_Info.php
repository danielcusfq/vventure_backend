<?php
// class that contains auth info
class Auth_Info {
    private $token;
    private $activation;
    private $id;

    public function __construct($id, $token, $activation){
        $this->set_id($id);
        $this->set_token($token);
        $this->set_activation($activation);
    }

    function set_id($id) {
        $this->id = $id;
    }
    function get_id() {
        return $this->id;
    }

    function set_token($token) {
        $this->token = $token;
    }
    function get_token() {
        return $this->token;
    }

    function set_activation($activation) {
        $this->activation = $activation;
    }
    function get_activation() {
        return $this->activation;
    }
}