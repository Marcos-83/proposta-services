<?php
// public/proposta/index.php
// Handles: /proposta/{token} and /proposta/{token} POST

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../app/models/UserModel.php';
require_once __DIR__ . '/../../app/models/ClientModel.php';
require_once __DIR__ . '/../../app/models/ProposalModel.php';
require_once __DIR__ . '/../../app/models/ProposalItemModel.php';
require_once __DIR__ . '/../../app/controllers/ProposalController.php';

// Extract token from PATH_INFO or query string
$token = '';

// Try PATH_INFO first (from .htaccess rewrite)
if (!empty($_SERVER['PATH_INFO'])) {
    $token = trim($_SERVER['PATH_INFO'], '/');
} elseif (!empty($_GET['token'])) {
    $token = trim($_GET['token']);
}

if (!$token) {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ProposalController::publicAction($token);
} else {
    ProposalController::publicView($token);
}
