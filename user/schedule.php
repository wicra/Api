<?php

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// VERIFICATION DE LA VALIDITE DE LA REQUETE ET DES PARAMETRES 'pont_id' ET 'date'
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['pont_id']) || !isset($_GET['date'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

// RECUPERATION ET TRAITEMENT DES PARAMETRES
$pont_id = intval($_GET['pont_id']);
$date = $_GET['date']; // au format 'YYYY-MM-DD'

try {
    // REQUETE SQL POUR RECUPERER UNIQUEMENT LES RESERVATIONS CONFIRMÉES OU EN MAINTENANCE POUR UN PONT A UNE DATE DONNEE
    $query = "SELECT * FROM reservations WHERE pont_id = ? AND DATE(date_debut) = ? AND statut IN ('confirmé', 'maintenance')";
    $stmt = $conn->prepare($query);
    $stmt->execute([$pont_id, $date]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // CONSTRUCTION DE L'EMPLOI DU TEMPS POUR CHAQUE HEURE (00h à 23h)
    $schedule = [];
    for ($hour = 0; $hour < 24; $hour++) {
        // PLAGE HORAIRE POUR L'HEURE CONSIDÉRÉE
        $slotStart = sprintf("%s %02d:00:00", $date, $hour);
        $slotEnd   = sprintf("%s %02d:00:00", $date, $hour + 1);
        $status = "Pont fermé";
        $reservationDetails = null;

        // VERIFICATION DE CHAQUE RÉSERVATION POUR DÉTERMINER SI ELLE CORRESPOND À LA PLAGE HORAIRE
        foreach ($reservations as $res) {
            if (($res['date_debut'] <= $slotStart && $res['date_fin'] > $slotStart) ||
                ($res['date_debut'] >= $slotStart && $res['date_debut'] < $slotEnd)) {
                
                // SI LE STATUT EST 'maintenance', DEFINIR LE STATUT EN CONSÉQUENCE
                if ($res['statut'] === 'maintenance') {
                    $status = "maintenance";
                } else {
                    $status = "Pont ouvert";
                }
                $reservationDetails = $res;
                break;
            }
        }

        // CONSTRUCTION DE L'ELEMENT DU SCHEDULE POUR L'HEURE COURANTE
        $schedule[] = [
            "hour" => sprintf("%02d:00", $hour),
            "status" => $status,
            "reservation" => $reservationDetails
        ];
    }

    // SUCCÈS : RENVOI DE L'EMPLOI DU TEMPS CONSTRUIT
    echo json_encode(["success" => true, "schedule" => $schedule]);
} catch (Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}

exit();
?>