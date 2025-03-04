<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

require '../db/db.php';

// Vérification des paramètres obligatoires
if (!isset($_GET['pont_id']) || empty($_GET['pont_id']) ||
    !isset($_GET['date']) || empty($_GET['date'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Paramètres 'pont_id' et/ou 'date' manquants"
    ]);
    exit();
}

$pont_id = intval($_GET['pont_id']);
$date    = $_GET['date'];  // attendu au format 'YYYY-MM-DD'

// Déterminer la période selon le mois de la date sélectionnée
$dateMonth = date('n', strtotime($date)); // mois sans les zéros devant
if ($dateMonth >= 4 && $dateMonth <= 10) {
    $periode = 'été';
} else {
    $periode = 'hiver';
}

$query = "
    SELECT
        c.creneau_id,
        c.type_creneau,
        c.heure_debut,
        c.heure_fin,
        c.capacite_max,
        -- nombre de réservations en statut confirmé
        (
            SELECT COUNT(*) 
            FROM reservations r
            WHERE r.creneau_id = c.creneau_id
              AND DATE(r.date_reservation) = :date
              AND r.statut = 'confirmé'
        ) AS reservations_confirmees,
        -- nombre de réservations en statut maintenance
        (
            SELECT COUNT(*)
            FROM reservations r2
            WHERE r2.creneau_id = c.creneau_id
              AND DATE(r2.date_reservation) = :date
              AND r2.statut = 'maintenance'
        ) AS reservations_maintenance
    FROM creneaux c
    WHERE c.pont_id = :pont_id
      AND c.periode = :periode
    ORDER BY c.heure_debut
";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':pont_id' => $pont_id,
        ':date'    => $date,
        ':periode' => $periode
    ]);
    $creneaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // On ajoute un champ "complet" en parcourant le résultat
    foreach ($creneaux as &$creneau) {
        $resConfirm = intval($creneau['reservations_confirmees']);
        $resMaint   = intval($creneau['reservations_maintenance']);
        $capacite   = intval($creneau['capacite_max']);
        $creneau['complet'] = (($resConfirm + $resMaint) >= $capacite);
    }

    http_response_code(200);
    echo json_encode([
        "success"  => true,
        "creneaux" => $creneaux
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la récupération des disponibilités : " . $e->getMessage()
    ]);
}
exit();
?>