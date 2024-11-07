<?php



namespace iutnc\nrv\User;


class User{

    protected int $id;
    protected string  $name;
    protected string $email;
    protected $password;
    protected $role;

    public function __construct($email , $nom, $role){
        $this->name = $nom;
        $this->email = $email;
        $this->role = $role;


    }
}