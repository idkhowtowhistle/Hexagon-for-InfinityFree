<?php
require __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!tables_ready()) {
    echo json_encode([
        'data' => [
            'clicker' => 0,
        ],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db()->exec('UPDATE site_config SET page_clicker = page_clicker + 1 WHERE id = 1');
}

echo json_encode([
    'data' => [
        'clicker' => page_clicker(),
    ],
]);
