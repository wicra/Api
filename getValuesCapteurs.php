<?php
require 'db.php';

try {
    // Récupère pour chaque pont la dernière mesure selon date_heure
    $query = "
      SELECT c.pont_id, p.nom, c.niveau_eau, c.temperature, c.humidite, c.date_heure
      FROM capteurs c
      JOIN ponts p ON c.pont_id = p.pont_id
      WHERE c.date_heure IN (
          SELECT MAX(date_heure)
          FROM capteurs
          GROUP BY pont_id
      )
      ORDER BY p.pont_id
    ";
    $stmt = $conn->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "capteurs" => $data]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>