<?php
namespace App\Model;

/**
 * Class Produit
 * @package App\Model
 *
 * Représente un produit avec un nom, une description et un prix.
 */


class Produit {
    private $id;
    private $etudiant_id;
    private $image;
    private $nom;
    private $description;
    private $prix;
    private $devis;

    /**
   
     * @param int $id
     * @param int $etudiant_id
     * @param string $image
     * @param string $nom
     * @param string $description
     * @param float $prix
     * @param string $devis
     */
    public function getId() {
        return $this->id;
    }
    public function getEtudiantId() {
        return $this->etudiant_id;
    }
    public function getImage() {
        return $this->image;
    }
    public function getNom() {
        return $this->nom;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getPrix() {
        return $this->prix;
    }
    public function getDevis() {
        return $this->devis;
    }
   
     

}

