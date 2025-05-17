<?php
try {
    $pdo = new PDO(
        "mysql:host=sql301.infinityfree.com;dbname=if0_38990085_tp;charset=utf8",
        "if0_38990085",
        "kti07lHRcgSwoj",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connexion OK";
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
}
