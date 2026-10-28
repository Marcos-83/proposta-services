<?php
// app/controllers/DashboardController.php

class DashboardController {

    public static function index(): void {
        requireUser();
        $userId = auth()['id'];

        $totalClients   = ClientModel::count($userId);
        $totalProposals = ProposalModel::countAll($userId);
        $totalApproved  = ProposalModel::countByStatus($userId, 'aprovado');
        $valueApproved  = ProposalModel::sumByStatus($userId, 'aprovado');
        $totalPending   = ProposalModel::countByStatus($userId, 'enviado');
        $followupsToday = ProposalModel::todayFollowups($userId);
        $chartData      = ProposalModel::approvedByMonth($userId);

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
