<?php
// Ensure errors are not displayed directly to the user in a JSON API
// but are logged for development.
ini_set('display_errors', 0);
// error_reporting(E_ALL); // Commented to avoid strict errors in production JSON

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Setup base directory for consistent includes
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/vendor/autoload.php';

use App\Model\Panier;
use App\Model\Connection; // For fetching product details

header('Content-Type: application/json');

// Log received data for debugging
$request_method = $_SERVER['REQUEST_METHOD'];
$input_data = [];
if ($request_method === 'POST') {
    $input_data = $_POST;
} elseif ($request_method === 'GET') {
    $input_data = $_GET;
}

error_log("[Cart API] Request received. Method: $request_method. Data: " . print_r($input_data, true));

$action = $input_data['action'] ?? null;
$productId = isset($input_data['productId']) ? filter_var($input_data['productId'], FILTER_VALIDATE_INT) : null;
// Default quantity to 1 for add, but for update, it must be explicitly provided.
// For add action, if quantity is not provided or invalid, it defaults to 1 later in the 'add' case.
$quantity = isset($input_data['quantite']) ? filter_var($input_data['quantite'], FILTER_VALIDATE_INT) : null;

$response = ['success' => false, 'message' => 'Invalid action or missing parameters.', 'cart_total_items' => 0];

// Ensure the Panier class is loaded and initialize the number of items
if (!class_exists(Panier::class)) {
    error_log("[Cart API] CRITICAL: Panier class not found.");
    $response['message'] = "Critical error: Panier class could not be loaded.";
    echo json_encode($response);
    exit;
}

try {
    $response['cart_total_items'] = Panier::getNombreArticles(); // Initial count

    if (!$action) {
        throw new InvalidArgumentException("No action specified.");
    }
    error_log("[Cart API] Action: $action, ProductID: $productId, Quantity: $quantity");

    switch ($action) {
        case 'add':
            if (!$productId || $productId <= 0) throw new InvalidArgumentException("Invalid product ID.");
            // If quantity is not provided for add, or is invalid (e.g., non-numeric, <=0), default to 1.
            if ($quantity === null || $quantity === false || $quantity <= 0) {
                $quantity = 1;
            }

            // Note: Panier::ajouter in the provided code fetches product details itself.
            // We could pass the details directly if the API received them, but here we rely on Panier::ajouter.
            $ajoutReussi = Panier::ajouter((int)$productId, (int)$quantity);

            if ($ajoutReussi) {
                $response = ['success' => true, 'message' => 'Product added to cart.', 'cart_total_items' => Panier::getNombreArticles()];
            } else {
                error_log("[Cart API] Add: Failed to add product ID: $productId. Product might not exist or DB error in Panier::ajouter.");
                // The error message could be more specific if Panier::ajouter returned information
                $response['message'] = 'Unable to add product (ID: ' . $productId . '). It might not exist.';
            }
            break;

        case 'update':
            if (!$productId || $productId <= 0) throw new InvalidArgumentException("Invalid product ID for update.");
            // For update, quantity must be explicitly provided and be a non-negative integer.
            if ($quantity === null || $quantity === false || $quantity < 0) {
                throw new InvalidArgumentException("Invalid quantity for update. Must be a positive integer or zero.");
            }

            Panier::mettreAJourQuantite($productId, $quantity); // Use the correct method
            $response = ['success' => true, 'message' => 'Quantity updated.', 'cart_total_items' => Panier::getNombreArticles()];
            break;

        case 'remove':
            if (!$productId || $productId <= 0) throw new InvalidArgumentException("Invalid product ID for removal.");
            Panier::supprimer($productId); // Use the correct method
            $response = ['success' => true, 'message' => 'Product removed from cart.', 'cart_total_items' => Panier::getNombreArticles()];
            break;

        case 'clear':
            Panier::vider();
            $response = ['success' => true, 'message' => 'Cart cleared.', 'cart_total_items' => 0];
            break;

        case 'get_cart_count':
            $response = ['success' => true, 'cart_total_items' => Panier::getNombreArticles()];
            break;

        default:
            error_log("[Cart API] Unrecognized action: $action");
            $response['message'] = "Unrecognized action '$action'.";
            break;
    }
} catch (InvalidArgumentException $e) {
    error_log("[Cart API] Validation Error: " . $e->getMessage() . " for action '$action'. Input: " . print_r($input_data, true));
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("[Cart API] Database Error: " . $e->getMessage() . " for action '$action'. SQL: " . (isset($stmt) && $stmt ? $stmt->queryString : 'N/A'));
    $response['message'] = 'Database error. Please try again.';
} catch (Exception $e) {
    error_log("[Cart API] General Error: " . $e->getMessage() . " for action '$action'. Trace: " . $e->getTraceAsString());
    $response['message'] = 'A server error occurred. Please try again later.';
}

error_log("[Cart API] Response: " . print_r($response, true));
echo json_encode($response);
exit;
?>