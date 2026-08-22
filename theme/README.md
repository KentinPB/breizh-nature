# Breizh'Nature - Thème WordPress Sur-Mesure

**Version :** 1.0
**Auteur :** Kentin.PB

## Description
Ce dossier contient le thème WordPress développé entièrement sur-mesure pour le portail des activités nature en Bretagne, **Breizh'Nature**. Conformément aux exigences du projet, il ne s'appuie sur aucun thème existant et gère exclusivement l'aspect visuel, l'ergonomie, et l'affichage dynamique des contenus (séparation stricte entre présentation et logique métier).

## Fonctionnalités Principales

* **Template Hierarchy Respecté :** Création de modèles spécifiques pour structurer l'affichage : accueil, pages standards, archive des activités, et détail d'une activité.
* **Filtrage AJAX Dynamique :** Implémentation d'un système de recherche avancée (par type, niveau, date, et statut terminé/à venir) qui met à jour le catalogue de la page `archive-activite.php` sans rechargement de la page entière.
* **Design Responsive & Moderne :** Interface épurée et fluide (utilisation de Flexbox et CSS Grid), entièrement responsive pour s'adapter aux ordinateurs, tablettes et smartphones.
* **Bannière de Consentement (RGPD) :** Affichage d'une bannière de gestion des cookies avec options d'acceptation/refus (structure présente dans le `footer.php`).
* **Performances & Sécurité :**
  * Nettoyage approfondi de la balise `<head>` (suppression des liens inutiles, balises génératrices de version).
  * Enqueue propre des scripts et styles via `functions.php`.

## Arborescence et Fichiers Clés

* `style.css` : Déclaration du thème et styles globaux de l'interface.
* `functions.php` : Cœur de la configuration du thème (supports, menus, enqueue des assets, logique de la requête AJAX, sécurité).
* `header.php` / `footer.php` : En-tête (navigation, logo dynamique) et pied de page globaux du site.
* `front-page.php` : Modèle de la page d'accueil personnalisée (qui met en avant l'association et les prochaines sorties).
* `archive-activite.php` : Catalogue global des activités, intégrant le formulaire de filtrage pour la requête AJAX.
* `single-activite.php` : Modèle de présentation détaillée d'une activité (avec barre latérale d'informations pratiques et appel au plugin de réservation).
* `page.php` : Modèle générique pour les pages standards institutionnelles (Mentions légales, etc.).
* `404.php` : Page d'erreur personnalisée incitant l'utilisateur à retourner sur les activités.
* `asset/js/` : Répertoire contenant les scripts Javascript du thème (`ajax-filter.js` et `cookies.js`). *(Note: Ces fichiers sont gérés séparément).*

## Prérequis
* Ce thème est conçu pour fonctionner en tandem avec le plugin métier **Breizh'Nature Réservations** (qui fournit le Custom Post Type `activite`, les taxonomies, et le shortcode de réservation).

## Installation

1. Uploadez le dossier complet du thème dans le répertoire `/wp-content/themes/` de votre installation WordPress.
2. Connectez-vous à l'administration de votre site WordPress.
3. Rendez-vous dans le menu **Apparence > Thèmes**.
4. Repérez le thème **Breizh'Nature** et cliquez sur **Activer**.
