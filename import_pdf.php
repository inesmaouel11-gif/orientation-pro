<?php
$conn = new mysqli("127.0.0.1", "root", "", "orientation_pro");

if ($conn->connect_error) {
    die("Erreur connexion : " . $conn->connect_error);
}

// ⚠️ simulation de données (comme si on lisait le PDF)
$data = [
    ["Informatique", "UNIV. ADRAR", 10.03, "Adrar"],
    ["Mathématiques", "UNIV. ADRAR", 10.60, "Adrar"],
    ["Génie Civil", "UNIV. CHLEF", 10.14, "Chlef"],
    ["Informatique", "UNIV. CHLEF", 12.93, "Chlef"],
    ["Informatique", "UNIV. LAGHOUAT", 10.91, "Laghouat"],
    ["Médecine", "RECRUTEMENT NATIONAL", 16.30, "National"]
];

// 🔁 on va multiplier les données pour simuler +1000 lignes
for ($i = 0; $i < 200; $i++) {

    foreach ($data as $row) {

        $filiere = $row[0];
        $etab = $row[1];
        $moyenne = $row[2] + rand(0, 100) / 100; // variation
        $wilaya = $row[3];

        // récupérer id_filiere
        $res1 = $conn->query("SELECT id_filiere FROM filieres WHERE nom='$filiere'");
        $f = $res1->fetch_assoc();

        // récupérer id_etab
        $res2 = $conn->query("SELECT id_etab FROM etablissements WHERE nom='$etab'");
        $e = $res2->fetch_assoc();

        if ($f && $e) {
            $id_filiere = $f['id_filiere'];
            $id_etab = $e['id_etab'];

            $conn->query("
                INSERT INTO seuils_admission 
                (id_filiere, id_etab, annee, serie_bac, wilaya, moyenne_min)
                VALUES
                ($id_filiere, $id_etab, 2024, 'Sciences Expérimentales', '$wilaya', $moyenne)
            ");
        }
    }
}

echo "✅ +1000 lignes insérées avec succès !";
?>