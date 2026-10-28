<?php
// public/index.php
require_once __DIR__ . '/../config/app.php';

if (auth()) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}
