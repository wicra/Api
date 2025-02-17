<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['pont_id']) || !isset($_GET['date'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

$pont_id = intval($_GET['pont_id']);
$date = $_GET['date']; // au format 'YYYY-MM-DD'

try {
    // Récupère les réservations du pont pour la journée
    $query = "SELECT * FROM reservations WHERE pont_id = ? AND DATE(date_debut) = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$pont_id, $date]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Construction de l'emploi du temps pour chaque heure (00h à 23h)
    $schedule = [];
    for ($hour = 0; $hour < 24; $hour++) {
        // La plage horaire de l'heure considérée
        $slotStart = sprintf("%s %02d:00:00", $date, $hour);
        $slotEnd   = sprintf("%s %02d:00:00", $date, $hour + 1);
        $status = "Pont fermé";
        $reservationDetails = null;

        // Pour chaque réservation, on vérifie si l'heure correspond à la plage horaire
        foreach ($reservations as $res) {
            // Si l'heure de début est antérieure ou égale au début du créneau et la fin est après le début du créneau
            // OU si le début de la réservation tombe dans le créneau
            if (($res['date_debut'] <= $slotStart && $res['date_fin'] > $slotStart) ||
                ($res['date_debut'] >= $slotStart && $res['date_debut'] < $slotEnd)) {
                $status = "Pont ouvert";
                $reservationDetails = $res;
                break;
            }
        }
        $schedule[] = [
            "hour" => sprintf("%02d:00", $hour),
            "status" => $status,
            "reservation" => $reservationDetails
        ];
    }

    echo json_encode(["success" => true, "schedule" => $schedule]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}

exit();
?>