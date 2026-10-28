<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../app/models/AdminModel.php';
require_once __DIR__ . '/../../app/controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AdminController::saveSettings();
} else {
    AdminController::dashboard();
}
