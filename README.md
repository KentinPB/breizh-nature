# 🌿 Projet WordPress — Breizh'Nature

**Le portail des activités nature en Bretagne**

Ce dépôt contient le code source du projet "Breizh'Nature", développé pour une association souhaitant promouvoir les activités liées à la nature et au patrimoine breton (randonnées, sorties découverte, ateliers pédagogiques, etc.).

Le projet a été pensé pour être administrable par une personne non développeuse et respecte une architecture stricte séparant l'affichage de la logique métier.

## 📂 Architecture du Projet

Le dépôt est structuré selon les exigences du cahier des charges :

*   `/theme` : Contient le thème WordPress développé sur-mesure, gérant exclusivement la présentation, l'affichage et l'expérience utilisateur, en respectant la logique du Template Hierarchy.
*   `/plugin` : Contient l'extension indépendante "Breizh'Nature Réservations", regroupant toutes les fonctionnalités métier (Custom Post Types, taxonomies, formulaires et gestion des données).
*   `/documentation` : Regroupe l'ensemble des livrables, explications techniques et notes de configuration (accessibilité, SEO, sécurité).

## 🚀 Fonctionnalités Principales

*   **Catalogue d'Activités :** Recherche et filtrage dynamique (AJAX) des activités par type, niveau et date.
*   **Système de Réservation :** Formulaire sécurisé, gestion automatisée des places et désistement via système de suivi.
*   **Tableau de Bord Administration :** Interface sur-mesure pour visualiser le statut des réservations (En attente, Acceptée, Refusée).
*   **Rôles et Permissions :** Création d'un rôle "Gestionnaire" restreint.

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
3.  **Activation :** Activer le thème et l'extension personnalisée dans l'administration pour générer les tables SQL nécessaires.

## 📝 Conventions Git

Ce projet utilise des commits explicites pour une traçabilité claire (`feat:`, `fix:`, `security:`, `docs:`).

---
*Projet développé dans le cadre du module "Développement d'applications à l'aide d'un CMS".*
