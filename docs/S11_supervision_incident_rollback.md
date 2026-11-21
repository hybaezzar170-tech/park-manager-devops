\# S11 - Supervision, incident et rollback



\## 1. Objectifs



Mettre en place la supervision des services Park Manager, détecter les incidents, diagnostiquer leurs causes et rétablir un fonctionnement stable.



\## 2. Supervision et Health Checks



\### 2.1 Health Check Backend



Une route API publique `/api/health` a été ajoutée dans Laravel. Elle vérifie la connexion à MySQL avec une requête SQL `SELECT 1`.



Test effectué :



```powershell

Invoke-RestMethod -Uri http://localhost:8000/api/health -Method Get

```



Résultat :

\- `status : ok`

\- `service : backend`

\- `database : connected`

\- Code HTTP : 200



\### 2.2 Health Checks Docker



Les contrôles de santé sont configurés pour :

\- MySQL : vérification avec `mysqladmin ping`.

\- Backend : vérification de l'API `/api/health`.

\- Frontend : vérification HTTP avec `wget`.



Les trois conteneurs ont été vérifiés avec `docker compose ps` et sont `healthy`.



\## 3. Indicateurs de supervision



Les indicateurs suivis sont :

\- État des conteneurs : `docker compose ps`.

\- État de santé : `docker inspect`.

\- Consommation CPU et mémoire : `docker stats --no-stream`.

\- Disponibilité HTTP du frontend : code 200.

\- Disponibilité du backend et connexion MySQL : `/api/health`.

\- Journaux des services : `docker compose logs`.



Les journaux ont permis de vérifier le démarrage des services et les requêtes HTTP.



\## 4. Simulation et résolution d'un incident



\### 4.1 Simulation



Le frontend a été volontairement arrêté avec :



```powershell

docker compose stop frontend

```



La commande `docker compose ps` a confirmé son indisponibilité. Un test HTTP sur `http://localhost:3001` a échoué avec un message indiquant que le serveur distant était inaccessible.



\### 4.2 Diagnostic



Les commandes utilisées :



```powershell

docker compose ps

docker compose logs --tail=30 frontend

```



Les journaux ont montré l'arrêt des processus Nginx. L'arrêt était volontaire.



\### 4.3 Rétablissement



Le frontend a été redémarré :



```powershell

docker compose start frontend

```



Le conteneur est redevenu `healthy` et le test HTTP a retourné 200. L'incident simulé a été résolu.



\## 5. Test de rollback



\### 5.1 Préparation



Une sauvegarde de la configuration Docker Compose a été créée sous le nom `docker-compose.s11-backup.yml`.



Une ancienne version des images Backend et Frontend a été récupérée depuis GHCR à partir du commit :



`c1ac24d681b6c48ee5edc8531c0aed0ea9df07bd`



\### 5.2 Tentative de rollback



Les images ont été temporairement remplacées par les images identifiées par le SHA du commit.



Le déploiement a été lancé avec :



```powershell

docker compose up -d --no-build --no-deps backend frontend

```



Le backend est redevenu `healthy`, mais le frontend est entré dans une boucle de redémarrage.



\### 5.3 Diagnostic de l'échec



Les journaux ont révélé l'erreur Nginx :



`unexpected end of parameter, expecting ";" in command line`



L'ancienne image frontend présentait donc une erreur de configuration empêchant son démarrage.



\### 5.4 Retour à la version stable



La sauvegarde Docker Compose a été restaurée, puis les services ont été redémarrés avec :



```powershell

docker compose up -d --no-build --no-deps backend frontend

```



Vérifications finales :

\- Frontend : HTTP 200.

\- Backend : `status = ok`.

\- Base de données : `connected`.

\- Backend, Frontend et MySQL : `healthy`.



Le retour à la version stable actuelle a réussi. La tentative de rollback vers l'ancienne image a échoué et a été documentée comme telle.



\## 6. Bilan



Cette semaine a permis de :

\- Mettre en place des Health Checks.

\- Surveiller l'état et les ressources des conteneurs.

\- Consulter et analyser les journaux Docker.

\- Simuler une indisponibilité du frontend.

\- Diagnostiquer et résoudre l'incident.

\- Tester une procédure de rollback et détecter un défaut dans une ancienne image.

\- Restaurer la configuration stable et vérifier le fonctionnement de l'application.



Les services sont actuellement opérationnels.

