<?php



namespace iutnc\nrv\User;


class User{

    protected int $id;
    protected string  $name;
    protected string $email;
    protected $password;

    public function __construct($email , $nom){
        $this->name = $nom;
        $this->email = $email;


    }
}