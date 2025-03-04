# Documentation API - Structure du Projet

Ce document synthétise les rôles de chaque fichier dans l'API.

---

## 📂 /db

- **db.php**  
  Établit la connexion PDO à la base de données MySQL et gère les erreurs de connexion.

---

## 📂 /user

- **addBateau.php**  
  Ajoute un bateau pour un utilisateur via une requête POST.  
  *Paramètres requis :* user_id, nom, immatriculation, hauteur_mat.

- **getCreneaux.php**  
  Récupère les créneaux disponibles pour un pont, filtrés par période (été/hiver) déterminée en fonction du mois courant.

- **getDisponibilites.php**  
  Retourne les créneaux d’un pont pour une date donnée.  
  La période est déterminée à partir de la date sélectionnée (été si avril-octobre, sinon hiver).  
  Calcule le taux d’occupation (réservations confirmées et maintenance) et indique si le créneau est complet.

- **getPonts.php**  
  Liste tous les ponts disponibles.

- **getUserBateaux.php**  
  Récupère les bateaux associés à un utilisateur (affiche nom, immatriculation et hauteur).

- **reserveCreneau.php**  
  Crée une réservation pour un créneau, en vérifiant que tous les champs (pont, créneau, date, bateau) sont renseignés.

- **UpdateReservationStatus.php**  
  Permet de modifier le statut d'une réservation (gestion côté utilisateur).

- **deleteBateau.php**  
  Supprime un bateau, après vérification de l’appartenance à l’utilisateur.

---

## 📂 /admin

- **getReservationsEnAttente.php**  
  Liste les réservations en attente avec les détails du pont et du bateau (nom, immatriculation, hauteur) ainsi que l’utilisateur associé.

- **getUserReservations.php**  
  Récupère l’historique des réservations d’un utilisateur avec statut et date.

- **newUpdateReservationStatus.php**  
  Permet à l’admin de changer le statut des réservations (confirmer, annuler, maintenance).

- **getStatistics.php**  
  Génère des statistiques sur l’occupation des ponts et le nombre de réservations par période.

---

## 📂 /auth

- **login.php**  
  Authentifie l’utilisateur (POST : email, password).

- **register.php**  
  Inscrit un nouvel utilisateur en validant les informations (nom, email, password).

- **logout.php**  
  Déconnecte l’utilisateur (invalide le token/session).

---

## 🗄️ Structure de la Base de Données

- **db_new.sql**
  