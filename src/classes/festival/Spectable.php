<?php


namespace iutnc\nrv\festival;

use iutnc\nrv\exception as E;
class Spectable  {


    protected string $nomSpectacle;

    protected string $style;

    protected int $duree;

    public function __construct( string $nomSpectacle, string $style, int $duree){
        $this->nomSpectacle = $nomSpectacle;
        $this->style = $style;
        $this->duree = $duree;
    }


    public function __get($attribut): mixed {
        if(property_exists($this, $attribut)){
            return $this->$attribut;
        }else{
            return throw new E\InvalidPropertyNameException("erreur ");
        }
    }

    public function __set(string $attribut,mixed $value){
        if (property_exists($this,$attribut)){
            $this->$attribut = $value;
        }

        else throw new E\InvalidPropertyNameException("$attribut : invalid property");

    }



}