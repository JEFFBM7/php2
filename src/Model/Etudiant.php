<?php
namespace App\Model;

class  Etudiant {
    private $id;
    private $nom;
    private $promotion;
    private $telephone;
    private $password;
    private $last_login;
    private $avatar;
    private $photo_profile;

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
    
    public function getLastLogin() {
        return $this->last_login;
    }
    
    public function getAvatar() {
        return $this->avatar;
    }
    
    public function getPhotoProfile() {
        return $this->photo_profile;
    }
    
    public function getEmail() {
        // La colonne dans la base de données s'appelle emailetudiant
        return $this->emailetudiant ?? '';
    }
}