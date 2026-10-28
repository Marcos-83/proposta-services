<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../app/models/AdminModel.php';
require_once __DIR__ . '/../../app/controllers/AdminController.php';

$action = $_POST['action'] ?? 'plans';
if ($action === 'save_plan') {
    AdminController::savePlan();
} else {
    AdminController::plans();
}
