<?php
// Include your database connection file
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_POST['client_id'];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $numero_telephone = $_POST['numero_telephone'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $code_postal = $_POST['code_postal'];
    $pays = $_POST['pays'];

    // Update query
    $sql = "UPDATE clients SET 
                prenom = ?, 
                nom = ?, 
                email = ?, 
                numero_telephone = ?, 
                adresse = ?, 
                ville = ?, 
                code_postal = ?, 
                pays = ? 
            WHERE client_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $prenom, $nom, $email, $numero_telephone, $adresse, $ville, $code_postal, $pays, $client_id);
    
    if ($stmt->execute()) {
        echo "Profil mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du profil: " . $stmt->error;
    }
}
?>
