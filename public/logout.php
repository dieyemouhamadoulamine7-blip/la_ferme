<?php
require_once __DIR__ . '/../includes/functions.php';

// Déconnexion simple : on supprime les données de session utilisateur
$_SESSION = [];
session_destroy();

redirect('index.php');


