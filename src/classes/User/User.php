<?php

namespace iutnc\nrv\User;

class User {

    protected int $id;
    protected string $name;
    protected string $email;
    protected string $password;
    protected int $role;

    public function __construct(int $id, string $email, string $nom, string $password, int $role) {
        $this->id = $id;
        $this->name = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    // Ajoutez des getters si nécessaire
    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getRole(): int {
        return $this->role;
    }
}