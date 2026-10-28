<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/models/ClientModel.php';
require_once __DIR__ . '/../app/controllers/ClientController.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? 'index');
$id     = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

match($action) {
    'create' => ClientController::create(),
    'store'  => ClientController::store(),
    'edit'   => ClientController::edit($id),
    'update' => ClientController::update($id),
    'delete' => ClientController::delete($id),
    default  => ClientController::index(),
};
