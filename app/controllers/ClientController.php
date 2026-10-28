<?php
// app/controllers/ClientController.php

class ClientController {

    public static function index(): void {
        requireUser();
        $clients = ClientModel::all(auth()['id']);
        require_once __DIR__ . '/../views/clients/index.php';
    }

    public static function create(): void {
        requireUser();
        require_once __DIR__ . '/../views/clients/form.php';
    }

    public static function store(): void {
        requireUser();
        verifyCsrf();
        $data = [
            'name'  => trim($_POST['name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];
        if (!$data['name']) {
            flash('error', 'O nome do cliente é obrigatório.');
            redirect('clients.php?action=create');
        }
        ClientModel::create(auth()['id'], $data);
        flash('success', 'Cliente cadastrado com sucesso!');
        redirect('clients.php');
    }

    public static function edit(int $id): void {
        requireUser();
        $client = ClientModel::find($id, auth()['id']);
        if (!$client) { redirect('clients.php'); }
        require_once __DIR__ . '/../views/clients/form.php';
    }

    public static function update(int $id): void {
        requireUser();
        verifyCsrf();
        $data = [
            'name'  => trim($_POST['name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];
        if (!$data['name']) {
            flash('error', 'O nome do cliente é obrigatório.');
            redirect('clients.php?action=edit&id=' . $id);
        }
        ClientModel::update($id, auth()['id'], $data);
        flash('success', 'Cliente atualizado com sucesso!');
        redirect('clients.php');
    }

    public static function delete(int $id): void {
        requireUser();
        verifyCsrf();
        ClientModel::delete($id, auth()['id']);
        flash('success', 'Cliente movido para a lixeira.');
        redirect('clients.php');
    }
}