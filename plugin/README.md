# Breizh'Nature Réservations - Plugin WordPress

**Version :** 1.0
**Auteur :** Kentin.PB

## Description
L'extension **Breizh'Nature Réservations** est le cœur métier du portail des activités nature en Bretagne. Elle gère de manière autonome et sécurisée la création des activités, le système de réservation public, et le traitement des données côté administration, indépendamment du thème utilisé.

## Fonctionnalités Principales

* **Catalogue d'activités (CPT & Taxonomies) :**
  * Création du Custom Post Type `activite`.
  * Taxonomies personnalisées : `type_activite` et `niveau_difficulte`.
  * Méta-boîtes dédiées pour les informations pratiques (date, heure, lieu, tarif, places, date limite).
* **Système de Réservation Public :**
  * Formulaire de réservation sécurisé via le shortcode `[bnr_reservation]`.
  * Validation stricte des données côté serveur (Regex pour nom, téléphone, email).
  * Gestion des capacités (places restantes) et des dates limites d'inscription.
* **Gestion des Réservations (Back-office) :**
  * Interface d'administration dédiée pour lister les demandes.
  * Modification des statuts (En attente, Acceptée, Refusée, Annulée) avec envoi automatique d'e-mails transactionnels.
* **Suivi pour les Utilisateurs (Magic Link) :**
  * Système de gestion sans compte utilisateur via le shortcode `[bnr_gestion_reservations]`.
  * Génération de liens éphémères (valables 15 minutes) envoyés par e-mail pour annuler une réservation.
* **API REST Personnalisée :**
  * Endpoints publics pour exposer le catalogue : `/wp-json/breizhnature/v1/activites` et `/wp-json/breizhnature/v1/categories`.
* **Tableau de bord & Rôles :**
  * Widget de statistiques sur le tableau de bord WordPress (activités à venir, réservations en attente/acceptées/annulées).
  * Création d'un rôle `gestionnaire` restreint pour l'administration quotidienne.

## Architecture Technique

Le plugin est découpé de manière modulaire pour une maintenance optimale :
* `breizh-nature-reservations.php` : Fichier principal d'initialisation.
* `includes/cpt-activite.php` : Déclaration du CPT et des taxonomies.
* `includes/metaboxes.php` : Gestion des champs personnalisés.
* `includes/base-de-donnees.php` : Création de la table personnalisée `wp_reservations`.
* `includes/formulaire.php` : Logique des shortcodes et traitement sécurisé.
* `includes/admin-reservations.php` : Interface de gestion côté back-office.
* `includes/dashboard.php` : Widget de statistiques.
* `includes/roles.php` : Gestion des permissions.
* `includes/api-rest.php` : Définition des routes de l'API REST.
* `includes/seeder.php` : Script d'injection de données de test (à désactiver en production).

## Installation

1. Uploadez le dossier `breizh-nature-reservations` dans le répertoire `/wp-content/plugins/` de votre installation WordPress.
2. Activez l'extension depuis le menu **Extensions** de WordPress.
3. Lors de l'activation, la table de base de données personnalisée sera automatiquement créée.

## Utilisation des Shortcodes

* `[bnr_reservation]` : À insérer dans le modèle de page de l'activité (ex: `single-activite.php`) pour afficher le formulaire de demande.
* `[bnr_gestion_reservations]` : À insérer sur une page classique pour permettre aux visiteurs de recevoir leur lien magique d'annulation.
