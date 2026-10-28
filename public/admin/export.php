<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../app/models/AdminModel.php';
require_once __DIR__ . '/../../app/models/PlanModel.php';
require_once __DIR__ . '/../../app/controllers/AdminController.php';
$type = $_GET['type'] ?? 'users';
AdminController::exportReport($type);
