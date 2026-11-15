# Schémas de la Semaine 1

## 1. Architecture de l'application (état actuel)

```mermaid
flowchart LR
    U["Utilisateur<br/>navigateur"] -->|"HTTP :3000"| F["Frontend React 18<br/>npm start"]
    F -->|"API REST JSON :8000<br/>URL écrite en dur"| B["Backend Laravel 10<br/>php artisan serve<br/>auth Sanctum"]
    B -->|"SQL :3306"| D[("MySQL XAMPP<br/>base alba_app")]
    B -.->|"journaux"| L["storage/logs/laravel.log"]
    B -.->|"email, SMS"| X["Services externes<br/>hors périmètre"]
```

| Composant | Rôle | Point faible actuel |
|---|---|---|
| React | Interface (25 composants) | Adresse de l'API écrite 89 fois dans le code |
| Laravel | API REST, règles métier | Routes presque toutes publiques, CORS ouvert |
| MySQL | Données | Base créée à la main |
| Journal | Diagnostic | Fichier local, aucune supervision |

## 2. Flux de livraison actuel

```mermaid
flowchart LR
    A["Développeur<br/>poste Windows"] --> B["Modification du code<br/>dans XAMPP htdocs"]
    B --> C["Tests à la main<br/>php artisan test"]
    C --> D["git push<br/>sans règle ni revue"]
    D --> E["Dépôt GitHub"]
    E --> F["Copie manuelle du dossier"]
    F --> G["Exécution sur le poste<br/>aucune version numérotée"]
```

Problèmes : aucune vérification automatique, aucun artefact versionné, aucun retour arrière, aucune supervision.

## 3. Flux cible de la chaîne DevOps

```mermaid
flowchart TD
    A["Branche feature/*"] --> B["Pull request vers develop"]
    B --> C{"CI GitHub Actions"}
    C --> C1["Style : Pint, ESLint"]
    C --> C2["Analyse statique : PHPStan"]
    C --> C3["Tests PHPUnit<br/>MySQL en service"]
    C --> C4["Tests API : Newman"]
    C --> C5["Sécurité : Gitleaks, audit, Trivy"]
    C1 & C2 & C3 & C4 & C5 --> G{"Quality gate<br/>tout est vert ?"}
    G -->|"non"| R["Merge bloqué<br/>correction demandée"]
    G -->|"oui"| M["Merge dans main + tag vX.Y.Z"]
    M --> I["Build des images Docker<br/>backend, web, frontend"]
    I --> P["Push vers ghcr.io<br/>tags sha-xxxx et vX.Y.Z"]
    P --> V["Validation manuelle<br/>GitHub Environment"]
    V --> T["Déploiement de test<br/>runner auto-hébergé + Docker Compose"]
    T --> S{"Smoke tests<br/>/api/health + parcours clé"}
    S -->|"échec"| RB["Rollback automatique<br/>vers l'image précédente"]
    S -->|"succès"| O["Supervision<br/>journaux et santé"]
    O --> INC["Incident détecté"]
    INC --> RB
```

| Étape | Semaine | Lot |
|---|---|---|
| Dépôt, branches, pull requests | 2 | Git et collaboration |
| Pipeline minimale, puis build | 3 à 4 | CI et build |
| Style, analyse statique, tests, quality gate | 5 à 6 | Qualité et tests |
| Images Docker et registre | 7 à 8 | Docker et registre |
| Configurations et secrets | 8 | Configuration |
| Déploiement et smoke tests | 9 | Déploiement |
| Sécurité (DevSecOps) | 10 | Sécurité |
| Supervision, incident, rollback | 11 à 12 | Supervision |
