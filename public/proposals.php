<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/models/ClientModel.php';
require_once __DIR__ . '/../app/models/ProductModel.php';
require_once __DIR__ . '/../app/models/ProposalModel.php';
require_once __DIR__ . '/../app/models/ProposalItemModel.php';
require_once __DIR__ . '/../app/controllers/ProposalController.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? 'index');
$id     = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

match($action) {
    'create'     => ProposalController::create(),
    'store'      => ProposalController::store(),
    'edit'       => ProposalController::edit($id),
    'update'     => ProposalController::update($id),
    'delete'     => ProposalController::delete($id),
    'duplicate'  => ProposalController::duplicate($id),
    'updateStatus' => ProposalController::updateStatus($id),
    default      => ProposalController::index(),
};
