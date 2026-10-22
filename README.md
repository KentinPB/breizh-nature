# 🌿 Projet WordPress — Breizh'Nature

**Le portail des activités nature en Bretagne**

Ce dépôt contient le code source du projet "Breizh'Nature", développé pour une association souhaitant promouvoir les activités liées à la nature et au patrimoine breton (randonnées, sorties découverte, ateliers pédagogiques, etc.).

Le projet a été pensé pour être administrable par une personne non développeuse et respecte une architecture stricte séparant l'affichage de la logique métier.

## 📂 Architecture du Projet

Le dépôt est structuré selon les exigences du cahier des charges :

*   `/theme` : Contient le thème WordPress développé sur-mesure, gérant exclusivement la présentation, l'affichage et l'expérience utilisateur, en respectant la logique du Template Hierarchy.
*   `/plugin` : Contient l'extension indépendante "Breizh'Nature Réservations", regroupant toutes les fonctionnalités métier (Custom Post Types, taxonomies, formulaires et gestion des données). Voir `plugin/README.md`.

Chaque dossier contient son propre README détaillé (`theme/README.md`, `plugin/README.md`).

## 🚀 Fonctionnalités Principales

*   **Catalogue d'Activités :** Recherche et filtrage dynamique (AJAX) des activités par type, niveau et date.
*   **Système de Réservation :** Formulaire sécurisé, gestion automatisée des places et désistement via système de suivi.
*   **Tableau de Bord Administration :** Interface sur-mesure pour visualiser le statut des réservations (En attente, Acceptée, Refusée).
*   **Rôles et Permissions :** Création d'un rôle "Gestionnaire" restreint.

## 🔒 Sécurité (code du plugin et du thème)

*   **CSRF :** nonce WordPress sur chaque formulaire (réservation, gestion, administration) et sur l'AJAX du catalogue.
*   **Injections SQL :** toutes les requêtes à paramètres passent par `$wpdb->prepare()` ; insertions et mises à jour typées (`%d`, `%s`).
*   **XSS :** entrées nettoyées (`sanitize_*`), sorties échappées (`esc_html`, `esc_attr`, `esc_url`).
*   **Validation serveur :** activité publiée, date limite, places restantes et nombre de participants revérifiés à l'enregistrement.
*   **Anti-spam :** champ honeypot invisible sur le formulaire de réservation.
*   **Annulation :** lien à jeton aléatoire, valable 15 minutes, à usage unique, comparé avec `hash_equals()`.
*   **Confidentialité :** message identique dans « Gérer mes réservations », qu'il existe des réservations ou non (pas d'énumération d'adresses).
*   **Moindre privilège :** rôle Gestionnaire avec la seule capacité `manage_reservations`, sans accès aux réglages du site.

## 🔌 Extensions (Plugins) Utilisées

L'environnement de production s'appuie sur une sélection rigoureuse d'extensions pour garantir sécurité, performances et écoconception :

*   **Breizh Nature Reservations** : L'extension métier sur-mesure développée pour ce projet (indépendante du thème).
*   **Performances & Écoconception :** *Autoptimize* (minification des ressources) et *W3 Total Cache* (mise en cache serveur/navigateur).
*   **Sécurité & Sauvegardes :** *Wordfence Security* (pare-feu), *WP Activity Log* (journal d'audit des actions utilisateurs) et *UpdraftPlus* (sauvegardes automatisées).
*   **Développement & Maintenance :** *Query Monitor* (débogage et analyse des requêtes SQL).
*   **Visibilité (SEO) :** *Yoast SEO* (optimisation du référencement naturel technique).
*   **Communication & Contacts :** *WP Mail SMTP* (délivrabilité des e-mails) et *WPForms Lite* (formulaires de contact génériques).
*   **Anti-Spam :** *Akismet Anti-spam* (protection des formulaires).

## 🛠️ Prérequis et Installation

1.  **Environnement local :** Disposer d'un serveur local avec PHP et MySQL.
2.  **Déploiement du code :** Copier `/theme` dans `wp-content/themes/breizh-nature` et `/plugin` dans `wp-content/plugins/breizh-nature-reservations`.
3.  **Activation :** Activer le thème et l'extension personnalisée dans l'administration. L'activation crée la table SQL `{prefixe}reservations`, le rôle « Gestionnaire » et la capacité `manage_reservations`.
4.  **Données de test (développement uniquement) :** ajouter `define( 'BNR_ENABLE_SEEDER', true );` dans `wp-config.php`, puis ouvrir l'administration une fois. Ne jamais définir cette constante en production.

## 📝 Conventions Git

Ce projet utilise des commits explicites pour une traçabilité claire (`feat:`, `fix:`, `security:`, `docs:`).

---
*Projet développé dans le cadre du module "Développement d'applications à l'aide d'un CMS".*
