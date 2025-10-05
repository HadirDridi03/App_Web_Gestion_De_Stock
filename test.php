<?php
$mysqli = new mysqli("localhost", "root", "", "test"); // remplace "test" par ta base si besoin

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}
echo "Connexion réussie à MySQL !";
?>