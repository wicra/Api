<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

// EN-TÊTE DE LA REQUETE 
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

$today = date('Y-m-d');
$weekLater = date('Y-m-d', strtotime('+7 days'));

// REQUETE SQL POUR RECUPERER LES RESERVATIONS EN ATTENTE
$query = "SELECT 
            r.reservation_id, 
            r.statut,
            DATE(r.date_reservation) as reservation_date,
            u.name AS user_name,
            p.nom AS pont_name,
            b.nom AS bateau_name,
            b.immatriculation AS bateau_immatriculation,
            b.hauteur_mat AS bateau_hauteur,
            c.libelle, 
            c.heure_debut, 
            c.heure_fin,
            c.capacite_max,
            (SELECT COUNT(*) FROM reservations r2 
             WHERE r2.creneau_id = r.creneau_id 
               AND r2.statut IN ('confirmé', 'maintenance')
            ) AS confirmed_count
          FROM reservations r
          JOIN users u ON r.user_id = u.id 
          JOIN ponts p ON r.pont_id = p.pont_id
          JOIN creneaux c ON r.creneau_id = c.creneau_id
          JOIN bateaux b ON r.bateau_id = b.bateau_id
          WHERE r.statut = :statut 
            AND DATE(r.date_reservation) BETWEEN :today AND :weekLater";
$stmt = $conn->prepare($query);

// 
$stmt->execute([
    ':statut'   => 'en attente',
    ':today'    => $today,
    ':weekLater'=> $weekLater
]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 
echo json_encode(["success" => true, "reservations" => $reservations]);
exit();
?>