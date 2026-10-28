<?php
require_once __DIR__ . '/../../config/app.php';

echo "<h2>Diagnóstico Admin</h2>";

// 1. Sessão
$user = auth();
echo "<p><strong>Sessão:</strong> ";
if ($user) {
    echo "Logado como: " . htmlspecialchars($user['name']);
    echo " | role: " . htmlspecialchars($user['role'] ?? 'NÃO SALVO NA SESSÃO');
} else {
    echo "NÃO LOGADO";
}
echo "</p>";

// 2. Colunas da tabela users
echo "<p><strong>Colunas em 'users':</strong> ";
try {
    $cols = db()->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    echo implode(', ', $cols);
} catch(Exception $e) {
    echo "ERRO: " . $e->getMessage();
}
echo "</p>";

// 3. Tabelas existentes
echo "<p><strong>Tabelas no banco:</strong> ";
try {
    $tables = db()->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo implode(', ', $tables);
} catch(Exception $e) {
    echo "ERRO: " . $e->getMessage();
}
echo "</p>";

// 4. Usuário admin no banco
echo "<p><strong>Usuário admin no banco:</strong> ";
try {
    $admin = db()->query("SELECT id, name, email, role, status FROM users WHERE role='admin' LIMIT 1")->fetch();
    if ($admin) {
        echo "ID={$admin['id']} | {$admin['name']} | {$admin['email']} | role={$admin['role']} | status={$admin['status']}";
    } else {
        echo "Nenhum usuário com role='admin' encontrado";
    }
} catch(Exception $e) {
    echo "ERRO: " . $e->getMessage();
}
echo "</p>";

// 5. Testar AdminController
echo "<p><strong>Teste AdminController:</strong> ";
try {
    require_once __DIR__ . '/../../app/models/AdminModel.php';
    require_once __DIR__ . '/../../app/models/PlanModel.php';
    require_once __DIR__ . '/../../app/controllers/AdminController.php';
    echo "OK — classes carregadas";
} catch(Throwable $e) {
    echo "ERRO: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine();
}
echo "</p>";

echo "<hr><p><a href='index.php'>Tentar abrir admin/index.php</a></p>";
