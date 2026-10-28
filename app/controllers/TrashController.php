<?php
// app/controllers/TrashController.php

class TrashController {

    public static function index(): void {
        requireUser();
        $userId    = auth()['id'];
        $clients   = ClientModel::trashed($userId);
        $products  = ProductModel::trashed($userId);
        $proposals = ProposalModel::trashed($userId);
        $tab       = $_GET['tab'] ?? 'proposals';
        require_once __DIR__ . '/../views/trash/index.php';
    }

    public static function restore(string $type, int $id): void {
        requireUser();
        verifyCsrf();
        $userId = auth()['id'];

        match($type) {
            'client'   => ClientModel::restore($id, $userId),
            'product'  => ProductModel::restore($id, $userId),
            'proposal' => ProposalModel::restore($id, $userId),
            default    => null,
        };

        $labels = ['client' => 'Cliente', 'product' => 'Produto/Serviço', 'proposal' => 'Proposta'];
        flash('success', ($labels[$type] ?? 'Item') . ' restaurado com sucesso!');
        redirect('trash.php?tab=' . $type . 's');
    }

    public static function forceDelete(string $type, int $id): void {
        requireUser();
        verifyCsrf();
        $userId = auth()['id'];

        match($type) {
            'client'   => ClientModel::forceDelete($id, $userId),
            'product'  => ProductModel::forceDelete($id, $userId),
            'proposal' => ProposalModel::forceDelete($id, $userId),
            default    => null,
        };

        $labels = ['client' => 'Cliente', 'product' => 'Produto/Serviço', 'proposal' => 'Proposta'];
        flash('success', ($labels[$type] ?? 'Item') . ' excluído permanentemente.');
        redirect('trash.php?tab=' . $type . 's');
    }
}