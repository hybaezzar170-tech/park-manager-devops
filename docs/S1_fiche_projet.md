# Fiche projet : chaîne DevOps pour Park Manager

## 1. Contexte

Park Manager est une application de gestion de parc automobile : véhicules, conducteurs, départements, contrats (vente, location, achat), documents (assurance, visite technique, taxe), alertes d'expiration, ouvriers, fournisseurs, pièces, maintenance et réparations. Elle a été développée pendant mon stage et fonctionne aujourd'hui uniquement en local.

## 2. Objectif du projet DevOps

Remplacer la livraison manuelle actuelle par une chaîne automatisée et reproductible :

1. chaque modification de code est vérifiée automatiquement (style, analyse statique, tests) ;
2. l'application est construite sous forme d'images Docker versionnées ;
3. elle est déployée dans un environnement de test avec une vérification automatique (smoke test) ;
4. elle est surveillée, et un retour à la version précédente (rollback) est possible.

## 3. Application support

| Élément | Choix |
|---|---|
| Backend | Laravel 10.50 (PHP 8.1), API REST, authentification Sanctum |
| Frontend | React 18 (Create React App) |
| Base de données | MySQL |
| Environnement actuel | XAMPP sous Windows |
| Taille | 41 migrations, une vingtaine de contrôleurs, 25 composants React |

L'application est réutilisée parce qu'elle est réelle, qu'elle a déjà une base de tests (PHPUnit) et un référentiel de défauts connus issu du module Test et validation des logiciels. Le travail est fait sur une **copie** du projet (`park-manager-devops`), distincte du projet de test.

## 4. Outils prévus

| Besoin | Outil | Rôle |
|---|---|---|
| Versionnement et collaboration | Git, GitHub | Branches, pull requests, historique |
| Intégration continue | GitHub Actions | Exécuter les vérifications à chaque push |
| Qualité du code | Laravel Pint, PHPStan (Larastan), ESLint | Style et analyse statique |
| Tests | PHPUnit, Newman (Postman) | Tests unitaires et tests d'API |
| Conteneurisation | Docker, Docker Compose | Images reproductibles (app, web, base, frontend) |
| Registre d'images | GitHub Container Registry (ghcr.io) | Stocker les images versionnées |
| Sécurité | Gitleaks, composer audit, npm audit, Trivy | Secrets, dépendances, images |
| Déploiement de test | Runner auto-hébergé + Docker Compose, GitHub Environments | Déploiement avec validation manuelle |
| Supervision | Route `/api/health`, journaux | Détecter et diagnostiquer un incident |

## 5. Organisation

- Équipe : emna et hyba
- Dépôt : `park-manager-devops`
- Stratégie de branches : `main` ← `develop` ← `feature/*`, intégration par pull request.
- Messages de commit : convention Conventional Commits (`feat:`, `fix:`, `ci:`, `docs:`).
- Rythme : 12 semaines, un lot par période, un commit de livrables par semaine.

## 6. Livrables attendus

- dépôt Git structuré avec historique et pull requests ;
- pipeline CI/CD (workflows GitHub Actions) ;
- images Docker et fichier Docker Compose ;
- gestion des configurations et des secrets ;
- déploiement de test avec smoke tests ;
- documentation de supervision, d'incident et de rollback ;
- rapport final et démonstration de la chaîne complète.

## 7. Périmètre et limites

- Inclus : toute la chaîne jusqu'à un environnement de test.
- Exclu : mise en production réelle, haute disponibilité, charge importante.
- Les 4 tests en échec à l'état initial correspondent à des défauts de l'application déjà identifiés dans le module Test et validation. Ils ne sont pas corrigés dans ce projet : ils sont isolés et documentés pour ne pas masquer la détection de nouvelles régressions.
- Les corrections de l'application ne font pas partie de l'objectif, sauf celles nécessaires au fonctionnement de la chaîne (exemple : casse du nom de fichier `vente.php`, bloquante sous Linux).
