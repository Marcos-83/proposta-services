<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/controllers/SettingsController.php';

$action = $_POST['action'] ?? 'index';

match($action) {
    'update'          => SettingsController::update(),
    'updatePassword'  => SettingsController::updatePassword(),
    'updateTemplate'  => SettingsController::updateTemplate(),
    default           => SettingsController::index(),
};
