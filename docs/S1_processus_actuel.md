# Processus de livraison actuel (état initial)

## 1. Description

Aujourd'hui, toutes les étapes sont manuelles et se font sur un seul poste Windows.

| Étape | Comment c'est fait aujourd'hui |
|---|---|
| Développement | Édition du code dans le dossier `htdocs` de XAMPP |
| Démarrage backend | `php artisan serve` (port 8000) |
| Démarrage frontend | `npm start` (port 3000) |
| Base de données | MySQL de XAMPP, base créée à la main (phpMyAdmin), puis `php artisan migrate` |
| Configuration | Fichier `.env` local, copié à la main |
| Tests | `php artisan test` lancé à la main, quand j'y pense |
| Qualité du code | Aucun contrôle automatique (Pint installé mais non imposé) |
| Versionnement | Dépôt GitHub public, sans règle de branche, sans revue |
| Livraison | Copie du dossier du projet, aucune image ni version numérotée |
| Surveillance | Aucune, les erreurs sont lues dans `storage/logs/laravel.log` |

## 2. Constats mesurés à l'état initial

| Constat | Preuve |
|---|---|
| Aucun fichier `.github/workflows`, `Dockerfile` ni `docker-compose` | Inspection du dépôt |
| 4 tests en échec sur l'état initial | `php artisan test` (voir capture S1_05) |
| `http://localhost:8000/api` écrit en dur 89 fois dans le frontend | Recherche dans `alba-frontend/src` |
| Classe `Vente` dans le fichier `vente.php` : non chargée sous Linux (PSR-4) | Avertissement de `composer install` |
| 67 vulnérabilités dans les dépendances npm (3 critiques, 32 élevées) | `npm install` / `npm audit` |
| Routes API presque toutes accessibles sans authentification | Lecture de `routes/api.php` |
| CORS ouvert à toutes les origines | `config/cors.php` |
| Fichiers de configuration sensibles et fichiers de test parasites à la racine | Inspection du dépôt |

## 3. Risques de ce fonctionnement

1. **Non reproductible** : l'application ne tourne que sur mon poste, avec mon XAMPP.
2. **Aucune barrière qualité** : un code qui casse les tests peut être poussé sans alerte.
3. **Défauts invisibles** : un problème de casse de fichier n'apparaît qu'au déploiement sous Linux.
4. **Sécurité** : secrets locaux, dépendances vulnérables, API ouverte, aucun contrôle automatique.
5. **Aucun retour arrière** : sans version numérotée, impossible de revenir proprement à la version précédente.
6. **Aucune visibilité** : sans route de santé ni supervision, une panne n'est détectée que par l'utilisateur.

## 4. Conclusion

Le processus actuel dépend entièrement de gestes manuels. Le projet DevOps doit le remplacer par une chaîne où chaque étape est automatisée, vérifiée et traçable (voir le schéma du flux cible).
