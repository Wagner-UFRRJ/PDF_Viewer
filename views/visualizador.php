<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizador PDF</title>
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/styles.css">
</head>
<body>
    <div id="viewer"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/pdf_viewer.js"></script>
    <script>
        PDFViewer.init('<?= $basePath ?>/?action=serve&doc=<?= htmlspecialchars($fileKey) ?>&token=<?= htmlspecialchars($token) ?>');
    </script>
</body>
</html>