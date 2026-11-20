# Semaine 4 : build et artefacts traçables

## 1. Objectif

Faire produire à la pipeline, à chaque exécution validée, des livrables téléchargeables et reliés au code qui les a générés : le frontend compilé et le backend préparé pour la production. Ces livrables sont la matière première de la conteneurisation (Semaine 7).

## 2. Job ajouté : `package`

Le job `package` s'exécute après les jobs `backend` et `frontend` (`needs: [backend, frontend]`) : si l'un des deux échoue, aucun livrable n'est produit.

| Étape | Rôle |
|---|---|
| Calculer l'identifiant de version | Prendre les 7 premiers caractères du commit (`GITHUB_SHA`) |
| `npm ci` puis `npm run build` | Compiler le frontend à partir du `package-lock.json` |
| Publier `frontend-build-<sha>` | Livrable du frontend (dossier `build`) |
| `composer install --no-dev --optimize-autoloader` | Installer seulement les dépendances de production, avec un autoload optimisé |
| Archiver le backend | Créer `backend-<sha>.tar.gz` sans `tests` ni `.env` |
| Publier `backend-package-<sha>` | Livrable du backend |

Les artefacts sont conservés 14 jours.

## 3. Résultat du run

| Job | Durée | Résultat |
|---|---|---|
| Backend Laravel | 12 s | Réussi |
| Frontend React | 41 s | Réussi |
| Package artefacts | 51 s | Réussi |

| Livrable | Identifiant | Taille | Contenu vérifié |
|---|---|---|---|
| `frontend-build-e35c023` | `e35c023` | 1,05 Mo | `index.html`, dossier `static`, `manifest.json`, `asset-manifest.json`, icônes |
| `backend-package-e35c023` | `e35c023` | 3,74 Mo (archive de 3,94 Mo une fois extraite du zip) | `backend/app`, `backend/routes`, etc. |

## 4. Contrôles effectués sur les livrables

1. **Intégrité.** Le digest SHA-256 affiché par GitHub pour `backend-package-e35c023` (`d60e4c938d51eb2ccb0cc56088d73793b9ea675b380a25d78c50f1569c96a9ab`) a été comparé à l'empreinte calculée sur le fichier téléchargé avec `Get-FileHash` : les deux valeurs sont identiques. Le fichier n'a pas été altéré entre la pipeline et le poste de contrôle.
2. **Exclusions.** La recherche de `backend/.env` et de `backend/tests/` dans l'archive du backend ne renvoie aucune ligne : aucun secret local ni code de test dans le livrable de production.
3. **Contenu du frontend.** Le zip contient bien la page d'entrée et les ressources compilées.

## 5. Limites constatées

| Limite | Détail | Traitement prévu |
|---|---|---|
| Adresse de l'API écrite en dur | Le frontend compilé contient `http://localhost:8000/api` (89 occurrences dans les sources) : il ne fonctionne que sur le poste du développeur | Semaine 8 : configuration par variable d'environnement |
| Identifiant de version | Sur une pull request, GitHub fournit le commit de fusion temporaire (ici `e35c023`), et non le dernier commit de la branche (`5a8b098`). L'identifiant correspond au commit de fusion simulé. Sur `develop` et `main`, il correspondra au commit réel | Semaines 8 et 9 : tags `vX.Y.Z` pour les versions publiées |
| Pas de test automatique | Les tests PHPUnit ne sont pas encore exécutés par la pipeline (base MySQL nécessaire) | Semaines 5 et 6 |
| Avertissements Node.js 20 | Trois avertissements GitHub signalent que `actions/checkout@v4`, `setup-node@v4` et `upload-artifact@v4` ciblent Node.js 20, déprécié, et sont exécutées sur Node.js 24. Sans effet sur le résultat | Mise à jour des versions des actions lors d'une passe de maintenance |
| Rétention de 14 jours | Les artefacts expirent automatiquement | Le registre d'images (Semaine 8) prendra le relais |

## 6. Preuves

| Capture | Contenu |
|---|---|
| `S4_01_pipeline_3_jobs.png` | Pull request avec « 3 successful checks » |
| `S4_02_artefacts.png` | Section Artifacts : noms, tailles, digests |
| `S4_03_contenu_frontend.png` | Contenu du zip du frontend |
| `S4_04_contenu_backend.png` | Listing de l'archive backend et recherche vide de `.env` et `tests` |
| `S4_05_detail_job_package.png` | Détail des étapes du job `package` et durées |
| `S4_06_verification_empreinte.png` | Empreinte SHA-256 calculée, identique à celle de GitHub |
