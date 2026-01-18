<?php
// Fonctions utilitaires communes

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

/**
 * Redirige vers une URL relative à BASE_URL.
 */
function redirect(string $path): void
{
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

/**
 * Retourne vrai si l'utilisateur est connecté.
 */
function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

/**
 * Retourne le rôle de l'utilisateur connecté (ou null).
 */
function current_user_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}

/**
 * Vérifie si l'utilisateur a un rôle donné.
 */
function has_role(string $role): bool
{
    return current_user_role() === $role;
}

/**
 * Récupère le panier depuis la session.
 * Structure : [ product_id => ['quantity' => int] ]
 */
function get_cart(): array
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

/**
 * Ajoute un produit au panier.
 */
function add_to_cart(int $product_id, int $quantity = 1): void
{
    $cart = get_cart();
    if (isset($cart[$product_id])) {
        $cart[$product_id]['quantity'] += $quantity;
    } else {
        $cart[$product_id] = ['quantity' => $quantity];
    }
    $_SESSION['cart'] = $cart;
}

/**
 * Met à jour la quantité d'un produit dans le panier.
 */
function update_cart(int $product_id, int $quantity): void
{
    $cart = get_cart();
    if ($quantity <= 0) {
        unset($cart[$product_id]);
    } else {
        $cart[$product_id]['quantity'] = $quantity;
    }
    $_SESSION['cart'] = $cart;
}

/**
 * Vide le panier.
 */
function clear_cart(): void
{
    $_SESSION['cart'] = [];
}


