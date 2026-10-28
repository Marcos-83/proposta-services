<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { AuthController::login(); }
else { AuthController::showLogin(); }
