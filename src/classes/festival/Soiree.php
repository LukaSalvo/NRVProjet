<?php



namespace iutnc\nrv\festival;

use iutnc\nrv\exception as E;
class Soiree{
    private string $nom;
    private string $date;
    private array $ListeSpectacle;
    private Lieu $lieu;


    public function __construct(string $nom, string $date){
        $this->nom = $nom;
        $this->date = $date;
        

    }

    public function __get($attribut): mixed{
        if(property_exists($this,$attribut)){
            return $this->$attribut;
        }
        throw new E\InvalidPropertyNameException("$attribut : invalid property");
    }

    public function setListeSpec(array $ListeSpectacle): void{
        $this->ListeSpectacle = $ListeSpectacle;
    }

    public function setLieu(Lieu $lieu): void{
        $this->lieu = $lieu;
    }

    public function addSpectacle(Spectacle $spectacle): void{
        $this->ListeSpectacle[] = $spectacle;
    }


}