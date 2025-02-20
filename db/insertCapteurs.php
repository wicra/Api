<?php
// filepath: /Applications/MAMP/htdocs/Api/insertCapteurs.php
require 'db.php';

// Pour éviter que le script ne se termine (exécution infinie)
set_time_limit(0);

// Supposons que vous ayez deux ponts; adaptez ce tableau à votre base
$pontIds = [1, 2, 3];

while (true) {
    // Sélectionne aléatoirement un pont
    $pont_id = $pontIds[array_rand($pontIds)];
    
    // Génère des valeurs aléatoires pour les mesures
    $niveau_eau = rand(50, 100) / 10;   // Exemple : 5.0 à 10.0
    $temperature = rand(150, 300) / 10;   // Exemple : 15.0 à 30.0 °C
    $humidite = rand(300, 900) / 10;      // Exemple : 30.0 à 90.0 %

    // Prépare et exécute l'insertion dans la table capteurs
    $query = "INSERT INTO capteurs (pont_id, niveau_eau, temperature, humidite) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$pont_id, $niveau_eau, $temperature, $humidite]);

    echo "Données insérées pour le pont_id $pont_id: Niveau d'eau = $niveau_eau, Température = $temperature, Humidité = $humidite\n";

    // Pause de 10 secondes avant la prochaine insertion
    sleep(10);
}
?>