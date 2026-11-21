# S8 — Matrice des configurations

## 1. Matrice des environnements

| Paramètre | Développement local | Tests CI | Production |
|---|---|---|---|
| APP_ENV | local | testing | production |
| APP_DEBUG | true | true | false |
| APP_KEY | .env.docker local | Générée par CI | Secret sécurisé |
| DB_CONNECTION | mysql | mysql | mysql |
| DB_HOST | db | 127.0.0.1 | Serveur DB |
| DB_DATABASE | alba_docker | alba_test | Variable sécurisée |
| DB_USERNAME | root (local uniquement) | root (test uniquement) | Utilisateur dédié |
| DB_PASSWORD | .env.docker | Variable CI de test | Secret sécurisé |
| APP_URL | localhost:8000 | Environnement CI | URL HTTPS |

## 2. Gestion des secrets

- `.env.docker` contient la configuration locale et ne doit jamais être versionné.
- `.env.docker.example` contient uniquement des valeurs fictives.
- `.env` et les fichiers `.env.*` sont ignorés par Git, sauf `.env.example` et `.env.docker.example`.
- GitHub Actions utilise `GITHUB_TOKEN` pour publier les images sur GHCR.
- En production, APP_KEY et les identifiants de base de données doivent être stockés dans les secrets de l'environnement GitHub ou dans un gestionnaire de secrets.
- Aucun secret ne doit être intégré dans une image Docker.

## 3. Images Docker publiées

| Image | Tags prévus |
|---|---|
| Backend | latest et SHA du commit |
| Frontend | latest et SHA du commit |

Registre : GitHub Container Registry (GHCR)

## 4. Sécurité et limites

- Les Pull Requests construisent les images sans les publier.
- La publication est réservée aux événements push sur develop et main.
- Les images doivent être vérifiées après publication.
- La configuration locale actuelle est destinée au développement, pas à la production.
- Les valeurs de démonstration ne doivent pas être réutilisées en production.

## 5. État de réalisation

- [x] Configuration Docker externalisée dans `.env.docker`.
- [x] Modèle `.env.docker.example` sans secrets réels.
- [x] Protection du fichier local par `.gitignore`.
- [x] Workflow GHCR ajouté.
- [x] Matrice des configurations documentée.
- [x] Publication effective des images GHCR.
- [x] Captures d'écran des images publiées.

