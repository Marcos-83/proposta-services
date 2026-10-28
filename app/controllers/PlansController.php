<?php
// app/controllers/PlansController.php

class PlansController {
    public static function index(): void {
        requireAuth();
        $plans     = PlanModel::all();
        $pageTitle = 'Planos & Preços';
        require_once __DIR__ . '/../views/plans/index.php';
    }
}
