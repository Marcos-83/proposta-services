<?php
// public/trash.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/models/ClientModel.php';
require_once __DIR__ . '/../app/models/ProductModel.php';
require_once __DIR__ . '/../app/models/ProposalModel.php';
require_once __DIR__ . '/../app/models/ProposalItemModel.php';
require_once __DIR__ . '/../app/controllers/TrashController.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? 'index');
$id     = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$type   = $_GET['type'] ?? ($_POST['type'] ?? '');

match($action) {
    'restore'     => TrashController::restore($type, $id),
    'forceDelete' => TrashController::forceDelete($type, $id),
    default       => TrashController::index(),
};