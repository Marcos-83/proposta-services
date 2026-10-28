<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../app/models/UserModel.php';
require_once __DIR__ . '/../../app/models/ClientModel.php';
require_once __DIR__ . '/../../app/models/ProposalModel.php';
require_once __DIR__ . '/../../app/models/ProposalItemModel.php';
require_once __DIR__ . '/../../app/controllers/PdfController.php';
$token = trim($_GET['token'] ?? '');
if ($token) PdfController::downloadByToken($token);
else { http_response_code(400); echo 'Token inválido.'; }