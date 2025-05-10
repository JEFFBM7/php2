<?php
// Démarre (ou retrouve) la session uniquement si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vide toutes les variables de session
$_SESSION = [];

// Détruit la session côté serveur
session_destroy();

// Optionnel : supprime aussi le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirige vers la page de login (ou index)
header('Location: /');

exit;
?>