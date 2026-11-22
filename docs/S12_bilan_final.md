# S12 - Bilan final et validation du projet DevOps



## 1. Objectifs



Cette dernière semaine a pour objectif de valider l'ensemble de la chaîne DevOps du projet Park Manager, depuis l'intégration du code jusqu'à la publication et au déploiement des images Docker.



Les objectifs sont :

- Vérifier le pipeline CI/CD complet.

- Publier les images Docker sur GitHub Container Registry (GHCR).

- Déployer les images publiées.

- Vérifier la disponibilité des services.

- Consolider les preuves et la documentation du projet.

- Préparer la démonstration finale.



## 2. Validation du pipeline CI/CD



Après la correction du problème de formatage détecté par Laravel Pint dans `backend/routes/api.php`, le pipeline a été relancé.



Le commit de correction est :



`d4d3880`



Le pipeline GitHub Actions numéro \*\*53\*\* a validé les étapes suivantes :



- Backend Laravel : réussite.

- Frontend React : réussite.

- Audit des dépendances : réussite.

- Détection des secrets : réussite.

- Package des artefacts : réussite.

- Build des images Docker : réussite.



La publication GHCR était ignorée dans cette exécution déclenchée par une Pull Request.



Après la fusion de la Pull Request, le pipeline numéro \*\*54\*\* a exécuté avec succès les sept jobs, y compris la publication des images Docker.



## 3. Publication des images Docker sur GHCR



Les images suivantes ont été publiées sur GitHub Container Registry :



- `ghcr.io/hybaezzar170-tech/alba-backend:latest`

- `ghcr.io/hybaezzar170-tech/alba-frontend:latest`



La récupération des images publiées a été effectuée avec :



```powershell

docker compose pull backend frontend

```



Les deux images ont été téléchargées avec succès.



## 4. Déploiement final



Le déploiement des nouvelles images a été réalisé avec :



```powershell

docker compose up -d backend frontend

```



Les conteneurs ont démarré correctement.



La commande suivante a permis de vérifier leur état :



```powershell

docker compose ps

```



Résultat observé :



- Backend : `healthy`.

- Frontend : `healthy`.

- MySQL : `healthy`.



Les trois services sont donc opérationnels après le déploiement.



## 5. Tests de disponibilité



### 5.1 Test Backend



Commande utilisée :



```powershell

Invoke-RestMethod -Uri http://localhost:8000/api/health -Method Get

```



Résultat :



- `status : ok`

- `service : backend`

- `database : connected`



Le backend communique correctement avec la base de données MySQL.



### 5.2 Test Frontend



Commande utilisée :



```powershell

(Invoke-WebRequest -Uri http://localhost:3001 -UseBasicParsing).StatusCode

```



Résultat :



`200`



La page de connexion Park Manager s'affiche correctement dans le navigateur.



Ces tests valident la disponibilité HTTP des services. Ils ne constituent pas à eux seuls une validation complète de tous les parcours fonctionnels de l'application.



## 6. Sécurité et qualité



Les contrôles automatisés du pipeline ont été validés :



- Audit des dépendances Backend et Frontend.

- Détection des secrets.

- Vérification de la qualité du code.

- Analyse des images Docker avec Trivy.



Le problème de formatage Laravel Pint détecté dans le fichier `routes/api.php` a été corrigé, puis vérifié avec succès.



## 7. Supervision et gestion des incidents



Les travaux de supervision et de simulation d'incident sont détaillés dans :



`S11\_supervision\_incident\_rollback.md`



Ils comprennent :

- Les Health Checks Docker.

- La surveillance des conteneurs.

- L'analyse des journaux.

- La simulation d'un arrêt du Frontend.

- Le diagnostic et le rétablissement du service.

- Le test de rollback et la documentation de son échec sur une ancienne image.



## 8. Preuves du déploiement final



Les preuves sont conservées dans le dossier `docs/Captures`.



Capture principale :



`S12\_01\_deploiement\_final.png`



Elle montre l'application Park Manager et la réponse positive du Backend.



## 9. Bilan général du projet



Le projet Park Manager a permis de mettre en œuvre une chaîne DevOps couvrant les principales étapes du cycle de livraison :



- Gestion du code source avec Git et GitHub.

- Intégration continue avec GitHub Actions.

- Tests et contrôles qualité automatisés.

- Analyse de sécurité des dépendances, des secrets et des images Docker.

- Construction et publication des images Docker sur GHCR.

- Déploiement avec Docker Compose.

- Supervision et vérification de la disponibilité des services.

- Gestion et documentation d'incidents.



Le déploiement final a été validé : les trois conteneurs sont `healthy`, le Backend retourne un état `ok` avec une base de données connectée et le Frontend répond avec le code HTTP 200.



## 10. Limites et améliorations possibles



Plusieurs améliorations peuvent être envisagées :



- Ajouter davantage de tests fonctionnels et d'intégration.

- Automatiser les tests après le déploiement.

- Mettre en place une supervision centralisée avec des tableaux de bord.

- Améliorer les procédures de rollback et tester systématiquement les anciennes versions avant leur utilisation.

- Ajouter un environnement de staging séparé de l'environnement de développement.

- Renforcer la gestion des sauvegardes de la base de données.



## 11. Conclusion



La chaîne DevOps du projet Park Manager a été mise en place et validée à travers les pipelines CI/CD, la publication des images Docker, le déploiement et les tests de disponibilité.



Les résultats obtenus démontrent le fonctionnement des principaux composants de la chaîne et permettent de préparer la démonstration finale du projet.


