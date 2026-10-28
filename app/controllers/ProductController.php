<?php
// app/controllers/ProductController.php

class ProductController {

    public static function index(): void {
        requireUser();
        $products = ProductModel::all(auth()['id']);
        require_once __DIR__ . '/../views/products/index.php';
    }

    public static function create(): void {
        requireUser();
        $product = null;
        require_once __DIR__ . '/../views/products/form.php';
    }

    public static function store(): void {
        requireUser();
        verifyCsrf();
        $data = [
            'type'        => $_POST['type'] ?? 'servico',
            'description' => trim($_POST['description'] ?? ''),
            'price'       => (float) str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0'),
        ];
        if (!$data['description']) {
            flash('error', 'A descrição é obrigatória.');
            redirect('products.php?action=create');
        }
        ProductModel::create(auth()['id'], $data);
        flash('success', 'Produto/Serviço cadastrado com sucesso!');
        redirect('products.php');
    }

    public static function edit(int $id): void {
        requireUser();
        $product = ProductModel::find($id, auth()['id']);
        if (!$product) { redirect('products.php'); }
        require_once __DIR__ . '/../views/products/form.php';
    }

    public static function update(int $id): void {
        requireUser();
        verifyCsrf();
        $data = [
            'type'        => $_POST['type'] ?? 'servico',
            'description' => trim($_POST['description'] ?? ''),
            'price'       => (float) str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0'),
        ];
        if (!$data['description']) {
            flash('error', 'A descrição é obrigatória.');
            redirect('products.php?action=edit&id=' . $id);
        }
        ProductModel::update($id, auth()['id'], $data);
        flash('success', 'Produto/Serviço atualizado com sucesso!');
        redirect('products.php');
    }

    public static function delete(int $id): void {
        requireUser();
        verifyCsrf();
        ProductModel::delete($id, auth()['id']);
        flash('success', 'Produto/Serviço movido para a lixeira.');
        redirect('products.php');
    }
}