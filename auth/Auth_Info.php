<?php
// class that contains auth info
class Auth_Info {
    private $token;
    private $activation;

    public function __construct($token, $activation){
        $this->set_token($token);
        $this->set_activation($activation);
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