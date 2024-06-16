<?php

namespace Classes;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once "./vendor/autoload.php";


class Jwthandler
{
    protected $jwt_secret;
    protected $token;
    protected $issuedAt;
    protected $expire;
    protected $jwt;

    public function __construct()
    {
        // default time zone set
        date_default_timezone_set("America/Sao_Paulo");
        $this->issuedAt = time();

        // token validity (3600 seconds = 1Hr)
        $this->expire = $this->issuedAt + 3600;

        // set your secret for your token or signature
        $this->jwt_secret = "this_is_my_secret";
    }

    public function jwtEncodeData($iss, $data)
    {
        $this->token = [
            "iss" => $iss,
            "aud" => $iss,
            "iat" => $this->issuedAt,
            "exp" => $this->expire,
            "data" => $data
        ];

        $this->jwt = JWT::encode($this->token, $this->jwt_secret, 'HS256');
        return $this->jwt;
    }

    public function jwtDecoderData($jwt_token)
    {
        try {
            // $decode = JWT::decode($jwt_token, new Key($this->jwt_secret, 'HS256'), $header = new \stdClass());
            $decode = JWT::decode($jwt_token, new Key($this->jwt_secret, 'HS256'));
            return [
                "data" => $decode->data
            ];
        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage()
            ];
        }
    }
}