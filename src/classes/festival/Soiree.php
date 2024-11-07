<?php



namespace iutnc\nrv\festival;

use iutnc\nrv\exception as E;
class Soiree{

    protected int $id_soiree;
    protected $date;

    protected Lieu $id_lieu;


    public function __construct(int $id, $date){
        $this->id_soiree = $id;
        $this->date = $date;

    }

    public function __get($attribut): mixed{
        if(property_exists($this,$attribut)){
            return $this->$attribut;
        }
        throw new E\InvalidPropertyNameException("$attribut : invalid property");
    }


}