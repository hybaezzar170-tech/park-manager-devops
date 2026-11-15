# Semaine 3 : première pipeline d'intégration continue

## 1. Objectif

Vérifier automatiquement, à chaque push et à chaque pull request, que le backend et le frontend s'installent et se construisent sur un environnement Linux neuf, au lieu de dépendre du poste Windows du développeur.

## 2. Déclenchement

| Événement | Branches |
|---|---|
| `push` | `develop`, `main` |
| `pull_request` | vers `develop` ou `main` |

Le fichier du workflow est `.github/workflows/ci.yml`. Il est exécuté par GitHub Actions sur `ubuntu-latest`.

## 3. Jobs

Les deux jobs sont indépendants et s'exécutent en parallèle.

| Job | Étape | Rôle |
|---|---|---|
| Backend Laravel | `actions/checkout` | Récupérer le code |
| | `setup-php` (PHP 8.1) | Installer la même version que le projet |
| | `composer install` | Installer les dépendances à partir du `composer.lock` |
| | `composer dump-autoload --optimize --strict-psr` | Échouer si un fichier PHP ne respecte pas la casse et le nom attendus (sensible sous Linux) |
| | copie de `.env.example` et `key:generate` | Préparer un environnement sans secret |
| | `php artisan --version` | Vérifier que Laravel démarre |
| Frontend React | `actions/checkout` | Récupérer le code |
| | `setup-node` (Node 22, cache npm) | Installer Node et réutiliser le cache |
| | `npm ci` | Installer exactement les versions du `package-lock.json` |
| | `npm run build` (`CI=false`) | Construire le frontend |

## 4. Résultat : un premier échec utile

| Run | Commit | Backend | Frontend | Cause |
|---|---|---|---|---|
| 1 | `d33ac3d` | Échec en 15 s | Réussi en 42 s | Étape PSR-4 |
| 2 | `513324b` | Réussi en 53 s | Réussi en 31 s | Corrigé |

**Analyse du premier échec.** L'étape de conformité PSR-4 a échoué : deux contrôleurs déclaraient une classe qui ne correspondait pas à leur chemin ou à leur nom de fichier.

- `app/Http/Controllers/ReclamationController.php` déclarait `App\Http\Controllers\Api\ReclamationController`.
- `app/Http/Controllers/Api/UserControllerSimple.php` déclarait `App\Http\Controllers\Api\UserController`.

Chacun était un doublon d'un fichier correct (`Api/ReclamationController.php`, `Api/UserController.php`). Composer les ignorait déjà (message « Skipping »), donc l'application ne les chargeait jamais, ce que les recherches dans le dépôt ont confirmé : aucune référence à ces fichiers, et les routes utilisent les contrôleurs de `Api/`.

**Correction.** Les deux doublons sont supprimés dans un second commit de la même pull request. Les tests PHPUnit donnent le même résultat avant et après (4 réussis, 4 en échec, défauts connus ANO-001, ANO-003, ANO-004 et TC-VEH-07), ce qui prouve l'absence d'effet sur le comportement.

**Pourquoi c'est utile.** Ce défaut était invisible sous Windows, où Composer ne produit qu'un avertissement. La pipeline l'a transformé en erreur bloquante, ce que fera aussi un build Docker sous Linux. C'est le principe de l'intégration continue : détecter tôt, sur un environnement proche de la production.

## 5. Limites de cette semaine

- Les tests PHPUnit ne sont pas encore exécutés par la pipeline : ils ont besoin d'une base MySQL et seront intégrés en Semaine 5 et 6.
- Quatre tests échouent à l'état initial (défauts connus de l'application, suivis dans le projet Test et validation). Ils seront isolés dans un groupe dédié afin que la pipeline puisse passer au vert sans masquer de nouvelles régressions.
- La protection de `main` n'est pas appliquée par GitHub sur un dépôt privé en plan gratuit : une pipeline rouge n'empêche pas techniquement la fusion. La règle « on ne fusionne pas une pull request rouge » est respectée par discipline.
- `npm install` signale 67 vulnérabilités dans les dépendances du frontend (3 critiques) : elles seront traitées dans le lot DevSecOps (Semaine 10).

## 6. Preuves

| Capture | Contenu |
|---|---|
| `S3_01_workflow_ci_yml.png` | Le fichier `ci.yml` dans le dépôt |
| `S3_02_premier_run_rouge.png` | Premier run : 1 échec, 1 succès |
| `S3_03_detail_echec.png`s | Journal de l'étape PSR-4 en échec |
| `S3_04_run_vert.png` | Run vert après correction |
| `S3_05_pr_checks.png` | Pull request : « All checks have passed » |
| `S3_06_duree_pipeline.png` | Durée des jobs |
