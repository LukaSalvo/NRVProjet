<?php

namespace iutnc\nrv\action;

class LogOutAction extends Action{
    public function execute(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['user']);
        session_destroy();

        return "Vous avez été déconnecté avec succès.";
    }
}
