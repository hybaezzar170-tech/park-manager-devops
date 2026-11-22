# S10 — Intégration des contrôles DevSecOps et traitement des alertes

## 1. Objectif

L'objectif est d'intégrer des contrôles de sécurité dans le pipeline CI/CD du projet Park Manager afin de détecter les vulnérabilités, les dépendances risquées et les secrets exposés.

## 2. Contrôles de sécurité intégrés

Les contrôles suivants sont intégrés dans `.github/workflows/ci.yml` :

- Composer Audit : vérification des dépendances PHP.
- NPM Audit : détection des vulnérabilités JavaScript.
- Gitleaks : détection des secrets exposés.
- Trivy : analyse des images Docker.
- Tests CI : vérification du Backend Laravel et du Frontend React.

## 3. Traitement des vulnérabilités Backend

Une première analyse Trivy a révélé plusieurs vulnérabilités liées principalement aux paquets système Debian.

Actions correctives réalisées :

- Mise à jour des paquets avec apt-get update et apt-get upgrade.
- Suppression des outils de compilation inutiles de l'image finale.
- Utilisation d'une construction Docker multi-stage.
- Reconstruction avec docker build --pull --no-cache.

## 4. Résultat du dernier scan Backend

Image analysée : alba-backend:ci

| Composant | Résultat |
|---|---:|
| Debian 13.8 | 0 vulnérabilité |
| Composer dependencies | 0 vulnérabilité |
| HIGH / CRITICAL | 0 |

Image analysée : alba-frontend:ci (Alpine 3.24.4)

| Composant | Résultat |
|---|---:|
| Alpine 3.24.4 | 0 vulnérabilité |
| HIGH / CRITICAL | 0 |

Le dernier scan Trivy, exécuté avec --ignore-unfixed --severity HIGH,CRITICAL, n'a détecté aucune vulnérabilité correspondant aux critères du contrôle.

## 5. Plan de remédiation

- Mettre à jour régulièrement les images Docker.
- Vérifier les résultats de Composer Audit et NPM Audit.
- Corriger les vulnérabilités détectées dans les dépendances.
- Ne jamais stocker les mots de passe ou clés secrètes dans le dépôt Git.
- Relancer les analyses après chaque modification importante.

## 6. Conclusion

Les contrôles DevSecOps ont été intégrés au pipeline CI/CD. La correction de l'image Backend a permis d'obtenir un résultat Trivy sans vulnérabilités HIGH ou CRITICAL détectées.

Les images Backend et Frontend ont toutes les deux obtenu 0 vulnérabilité HIGH ou CRITICAL lors des scans Trivy. L'exécution finale complète du pipeline CI reste à confirmer.

