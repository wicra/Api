<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

try {
    // REQUETE SQL POUR RECUPERER POUR CHAQUE PONT LA DERNIÈRE MESURE SELON date_heure
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
    
    // SUCCÈS : RENVOIE LES VALEURS DES CAPTEURS TROUVÉES
    echo json_encode(["success" => true, "capteurs" => $data]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>