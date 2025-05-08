<?php
namespace App\Model;

class  Etudiant {
    private $id;
    private $nom;
    private $promotion;
    private $telephone;
    private $password;

    public function getId() {
        return $this->id;
    }
    public function getNom() {
        return $this->nom;
    }
    public function getPromotion() {
        return $this->promotion;
    }
    public function getTelephone() {
        return $this->telephone;
    }
    public function getPassword() {
        return $this->password;
    }
}