<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/models/ClientModel.php';
require_once __DIR__ . '/../app/models/ProposalModel.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
DashboardController::index();
