# TrucsPasChers - Plateforme de Petites Annonces

TrucsPasChers est une application web PHP qui permet aux utilisateurs de créer un compte, de publier des produits à vendre et de consulter les produits des autres utilisateurs.

## Fonctionnalités

- Inscription et connexion utilisateur
- Création, édition et suppression de produits
- Recherche de produits par catégorie ou mot-clé
- Affichage des détails de produits
- Gestion du profil utilisateur
- Panier d'achat (en développement)
- Tutoriel interactif pour les nouveaux utilisateurs

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Composer
- Serveur web (Apache recommandé)

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone <url-du-depot>
   cd php2
   ```

2. **Installer les dépendances avec Composer**
   ```bash
   composer install
   ```

3. **Configurer la base de données**
   - Créer une base de données MySQL
   - Copier le fichier `.env.example` en `.env`
   - Modifier les informations de connexion à la base de données dans le fichier `.env`
   - Importer le fichier `db.sql` dans votre base de données

   ```bash
   mysql -u votre_utilisateur -p votre_base_de_donnees < db.sql
   ```

4. **Configurer le serveur web**
   - Configurer le serveur web pour pointer vers le répertoire `public/`
   - Assurez-vous que le module de réécriture est activé (mod_rewrite pour Apache)

## Structure du Projet

- `/public` - Point d'entrée de l'application et fichiers statiques
- `/src` - Code source PHP
   - `/Model` - Modèles de données
- `/views` - Fichiers de vues
   - `/layout` - Templates de mise en page
- `/vendor` - Dépendances gérées par Composer
- `/assets` - Ressources additionnelles

## Fonctionnement

L'application utilise un routeur personnalisé basé sur AltoRouter pour gérer les requêtes HTTP. Les routes sont définies dans le fichier `public/index.php` et les contrôleurs correspondants sont situés dans le répertoire `views`.

Le système MVC est simplifié :
- Modèles : classes dans `/src/Model/`
- Vues : fichiers PHP dans `/views/`
- Contrôleurs : intégrés dans les fichiers de vue

## Développement

Pour ajouter une nouvelle page :

1. Créer un fichier de vue dans le répertoire `/views/`
2. Ajouter la route correspondante dans `public/index.php`

```php
$router
   ->get('/nouvelle-page', 'nom_vue', 'nom_route')
```

## Déploiement

L'application peut être déployée sur n'importe quel serveur web compatible PHP. Assurez-vous que :

- Les permissions sont correctement configurées pour les répertoires d'upload
- Le fichier `.env` contient les informations de production
- Le mode DEBUG est désactivé dans l'environnement de production

## Licence

Tous droits réservés.
