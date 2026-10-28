<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/models/ProductModel.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? 'index');
$id     = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

match($action) {
    'create' => ProductController::create(),
    'store'  => ProductController::store(),
    'edit'   => ProductController::edit($id),
    'update' => ProductController::update($id),
    'delete' => ProductController::delete($id),
    default  => ProductController::index(),
};
