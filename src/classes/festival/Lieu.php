<?php


namespace iutnc\nrv\festival;

use iutnc\nrv\exception as E;

class Lieu  {



    

    private string  $nom_lieu;

    private int $nb_place ;

    private string $nom_emplacement;


    public function __construct(int $nb_place, string $nom_emplacement , string $nom_lieu) {
        $this->nb_place = $nb_place;
        $this->nom_emplacement = $nom_emplacement;
        $this->nom_lieu = $nom_lieu;
    }

    public function __tostring(): string {
        return $this->nom_lieu;
    }



}