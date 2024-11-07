<?php


namespace iutnc\nrv\festival;

use iutnc\nrv\exception as E;
class Spectable  {



    protected int $id_spectacle;
    protected string $nomSpectacle;

    protected string $style;

    protected int $duree;

    public function __construct(int $id_spectacle, string $nomSpectacle, string $style, int $duree){
        $this->id_spectacle = $id_spectacle;
        $this->nomSpectacle = $nomSpectacle;
        $this->style = $style;
        $this->duree = $duree;
    }



}