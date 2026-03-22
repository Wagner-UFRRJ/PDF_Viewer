<?php
declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';
use App\Services\PDFViewer;
session_start();

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$action = $_GET['action'] ?? 'viewer';
$fileKey = $_GET['doc'] ?? 'relatorio';
$token = $_GET['token'] ?? '';

$allowedFiles = [
    'relatorio' => __DIR__.'/../storage/lista_estrutura_repeticao.pdf'
];

try {
    if ($action === 'serve') {
        // Verificação de referer
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $expectedReferer = 'http://' . $_SERVER['HTTP_HOST'] . $basePath . '/';
        if (strpos($referer, $expectedReferer) !== 0 && strpos($referer, 'https://' . $_SERVER['HTTP_HOST'] . $basePath . '/') !== 0) {
            http_response_code(403);
            exit('Acesso negado: referer inválido');
        }

        $viewer = new PDFViewer($fileKey, $allowedFiles);
        $viewer->verifyToken($token);

        // Remove o token (uso único)
        unset($_SESSION['pdf_tokens'][$fileKey]);

        $filePath = $viewer->getFilePath();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.basename($filePath).'"');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        readfile($filePath);
        exit;
    } else {
        $viewer = new PDFViewer($fileKey, $allowedFiles);
        $token = $viewer->generateToken();
        require __DIR__.'/../views/visualizador.php';
        exit;
    }
} catch (Throwable $e) {
    http_response_code(403);
    echo 'Erro: '.$e->getMessage();
    exit;
}