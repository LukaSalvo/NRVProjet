<?php



namespace iutnc\nrv\festival;

use iutnc\nrv\exception as E;
class Soiree{
    private string $nom;
    private string $date;
    private array $ListeSpectacle;
    private Lieu $lieu;


    public function __construct(string $nom, string $date, array $ListeSpectacle, Lieu $lieu){
        $this->id_soiree = $id;
        $this->date = $date;
        $this->ListeSpectacle = $ListeSpectacle;
        $this->lieu = $lieu;

    }

    public function __get($attribut): mixed{
        if(property_exists($this,$attribut)){
            return $this->$attribut;
        }
        throw new E\InvalidPropertyNameException("$attribut : invalid property");
    }


}