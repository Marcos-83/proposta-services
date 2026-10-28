<?php
// app/controllers/ProposalController.php

class ProposalController {

    public static function index(): void {
        requireUser();
        $userId  = auth()['id'];
        $filters = [
            'client_id'     => $_GET['client_id'] ?? '',
            'status'        => $_GET['status'] ?? '',
            'followup_date' => $_GET['followup_date'] ?? '',
        ];
        $proposals = ProposalModel::all($userId, $filters);
        $clients   = ClientModel::all($userId);
        require_once __DIR__ . '/../views/proposals/index.php';
    }

    public static function create(): void {
        requireUser();
        $userId   = auth()['id'];
        $proposal = null;
        $items    = [];
        $clients  = ClientModel::all($userId);
        $products = ProductModel::all($userId);
        require_once __DIR__ . '/../views/proposals/form.php';
    }

    public static function store(): void {
        requireUser();
        verifyCsrf();
        $userId = auth()['id'];
        $data = [
            'client_id'     => (int)($_POST['client_id'] ?? 0),
            'title'         => trim($_POST['title'] ?? ''),
            'status'        => $_POST['status'] ?? 'rascunho',
            'proposal_date' => $_POST['proposal_date'] ?? date('Y-m-d'),
            'followup_date' => !empty($_POST['followup_date']) ? $_POST['followup_date'] : null,
            'observations'  => trim($_POST['observations'] ?? ''),
            'total_value'   => 0,
        ];
        if (!$data['client_id'] || !$data['title']) {
            flash('error', 'Cliente e título são obrigatórios.');
            redirect('proposals.php?action=create');
        }

        // Create proposal
        $id = ProposalModel::create($userId, $data);

        // Save items from JSON (passed from JS)
        $itemsJson = trim($_POST['items_json'] ?? '[]');
        $items = json_decode($itemsJson, true);
        if (is_array($items)) {
            foreach ($items as $item) {
                $itemData = [
                    'product_id' => !empty($item['product_id']) ? (int)$item['product_id'] : null,
                    'description' => trim($item['description'] ?? ''),
                    'quantity'   => (float)($item['quantity'] ?? 1),
                    'unit_price' => (float)($item['unit_price'] ?? 0),
                ];
                if ($itemData['description']) {
                    ProposalItemModel::create($id, $itemData);
                }
            }
            ProposalModel::recalcTotal($id);
        }

        flash('success', 'Proposta criada com sucesso!');
        redirect('proposals.php?action=edit&id=' . $id);
    }

    public static function edit(int $id): void {
        requireUser();
        $userId   = auth()['id'];
        $proposal = ProposalModel::find($id, $userId);
        if (!$proposal) { redirect('proposals.php'); }
        $items    = ProposalItemModel::allByProposal($id);
        $clients  = ClientModel::all($userId);
        $products = ProductModel::all($userId);
        require_once __DIR__ . '/../views/proposals/form.php';
    }

    public static function update(int $id): void {
        requireUser();
        verifyCsrf();
        $userId = auth()['id'];
        $data = [
            'client_id'     => (int)($_POST['client_id'] ?? 0),
            'title'         => trim($_POST['title'] ?? ''),
            'status'        => $_POST['status'] ?? 'rascunho',
            'proposal_date' => $_POST['proposal_date'] ?? date('Y-m-d'),
            'followup_date' => !empty($_POST['followup_date']) ? $_POST['followup_date'] : null,
            'observations'  => trim($_POST['observations'] ?? ''),
        ];
        // Recalculate total from items
        $items = ProposalItemModel::allByProposal($id);
        $data['total_value'] = array_sum(array_column($items, 'total_price'));

        ProposalModel::update($id, $userId, $data);
        flash('success', 'Proposta atualizada com sucesso!');
        redirect('proposals.php?action=edit&id=' . $id);
    }

    public static function updateStatus(int $id): void {
        requireUser();
        verifyCsrf();
        $status = $_POST['status'] ?? '';
        $validStatuses = ['rascunho', 'enviado', 'aprovado', 'reprovado'];
        if (!in_array($status, $validStatuses)) {
            flash('error', 'Status inválido.');
        } else {
            ProposalModel::updateStatus($id, auth()['id'], $status);
            flash('success', 'Status atualizado!');
        }
        redirect('proposals.php');
    }

    public static function delete(int $id): void {
        requireUser();
        verifyCsrf();
        ProposalModel::delete($id, auth()['id']);
        flash('success', 'Proposta excluída com sucesso!');
        redirect('proposals.php');
    }

    // -- Items --

    public static function addItem(int $proposalId): void {
        requireUser();
        verifyCsrf();
        $proposal = ProposalModel::find($proposalId, auth()['id']);
        if (!$proposal) { redirect('proposals.php'); }

        $data = [
            'product_id' => !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null,
            'description' => trim($_POST['description'] ?? ''),
            'quantity'   => (float) str_replace(',', '.', $_POST['quantity'] ?? 1),
            'unit_price' => (float) str_replace(['.', ','], ['', '.'], $_POST['unit_price'] ?? '0'),
        ];
        if (!$data['description']) {
            flash('error', 'A descrição do item é obrigatória.');
        } else {
            ProposalItemModel::create($proposalId, $data);
            ProposalModel::recalcTotal($proposalId);
            flash('success', 'Item adicionado!');
        }
        redirect('proposals.php?action=edit&id=' . $proposalId);
    }

    public static function deleteItem(int $itemId, int $proposalId): void {
        requireUser();
        verifyCsrf();
        ProposalModel::find($proposalId, auth()['id']); // access check
        ProposalItemModel::delete($itemId);
        ProposalModel::recalcTotal($proposalId);
        flash('success', 'Item removido!');
        redirect('proposals.php?action=edit&id=' . $proposalId);
    }

    // -- Public view --

    public static function publicView(string $token): void {
        $proposal = ProposalModel::findByToken($token);
        if (!$proposal) {
            http_response_code(404);
            require_once __DIR__ . '/../views/public/404.php';
            return;
        }
        // Registra visualização do cliente
        ProposalModel::registerView($token);

        $items = ProposalItemModel::allByProposal($proposal['id']);
        require_once __DIR__ . '/../views/public/proposal.php';
    }

    public static function publicAction(string $token): void {
        $action = $_POST['action'] ?? '';
        $status = ($action === 'approve') ? 'aprovado' : (($action === 'reject') ? 'reprovado' : null);
        if ($status) {
            ProposalModel::updateStatusByToken($token, $status);
        }
        header('Location: ' . APP_URL . '/proposta/' . $token);
        exit;
    }
}
