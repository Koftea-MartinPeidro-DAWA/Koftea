<?php
session_start();
require __DIR__ . "/../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use Koftea\Producto;

$dataDir        = __DIR__ . "/data";
$productsFile   = $dataDir . "/productos.json";

$importResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['excel']) || $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
        $importResult = ['error' => 'No se pudo subir el archivo. Verifica que has seleccionado un fichero válido.'];
    } else {
        $excelFile = $_FILES['excel']['tmp_name'];

        try {
            if (!is_dir($dataDir)) {
                if (!@mkdir($dataDir, 0775, true)) {
                    throw new Exception("No se pudo crear el directorio de datos.");
                }
            }

            // Backup del JSON anterior
            if (file_exists($productsFile)) {
                $backupName = $dataDir . '/productos_backup_' . date('Ymd_His') . '.json';
                copy($productsFile, $backupName);
            }

            $spreadsheet = IOFactory::load($excelFile);
            $sheet       = $spreadsheet->getActiveSheet();
            $excel_data  = $sheet->toArray(null, true, true, true);

            $valid_headers = [
                "ID", "Categoría", "Nombre", "Descripción", "Procedencia/Origen",
                "Intensidad", "Formato", "Precio (€)", "Stock",
                "Detalle Específico", "Imagen (img)"
            ];

            if (!isset($excel_data[1]) || !is_array($excel_data[1])) {
                throw new Exception("El archivo no contiene una fila de cabeceras válida.");
            }

            // Mapa de cabeceras: ["Nombre cabecera" => "A", ...]
            $firstRow   = $excel_data[1];
            $header_map = [];
            foreach ($firstRow as $col => $value) {
                $key = trim((string)$value);
                if ($key !== '') $header_map[$key] = $col;
            }

            foreach ($valid_headers as $header) {
                if (!isset($header_map[$header])) {
                    throw new Exception("Falta el campo obligatorio: \"$header\"");
                }
            }

            array_shift($excel_data); // quitar fila de cabeceras

            $productes   = [];
            $errors      = [];
            $skus_vistos = [];

            foreach ($excel_data as $rowNum => $row) {
                // Saltar filas vacías
                $allEmpty = true;
                foreach ($row as $cell) {
                    if (trim((string)$cell) !== '') { $allEmpty = false; break; }
                }
                if ($allEmpty) continue;

                $sku = trim((string)($row[$header_map["ID"]] ?? ''));
                $nom = trim((string)($row[$header_map["Nombre"]] ?? ''));

                // Detectar duplicados por SKU
                if ($sku !== '' && isset($skus_vistos[$sku])) {
                    $errors[] = "Fila " . ($rowNum + 1) . ": SKU \"$sku\" duplicado — ignorado.";
                    continue;
                }

                // Normalizar precio
                $rawPrice = $row[$header_map["Precio (€)"]] ?? '';
                $rawPrice = preg_replace('/[^\d,.\-]/u', '', (string)$rawPrice);
                $rawPrice = str_replace(',', '.', $rawPrice);
                $price    = $rawPrice === '' ? 0.0 : (float)$rawPrice;

                if ($price < 0) {
                    $errors[] = "Fila " . ($rowNum + 1) . ": precio negativo para \"$nom\" — ignorado.";
                    continue;
                }

                $stock = (int)($row[$header_map["Stock"]] ?? 0);
                if ($stock < 0) {
                    $errors[] = "Fila " . ($rowNum + 1) . ": stock negativo para \"$nom\", se ajusta a 0.";
                    $stock = 0;
                }

                $productoObj = new Producto(
                    $sku,
                    $row[$header_map["Categoría"]]          ?? "",
                    $nom,
                    $row[$header_map["Descripción"]]        ?? "",
                    $row[$header_map["Procedencia/Origen"]] ?? "",
                    $row[$header_map["Intensidad"]]         ?? "",
                    $row[$header_map["Formato"]]            ?? "",
                    $price,
                    $stock,
                    $row[$header_map["Detalle Específico"]] ?? "",
                    $row[$header_map["Imagen (img)"]]       ?? ""
                );

                if ($sku !== '') $skus_vistos[$sku] = true;
                $productes[] = $productoObj->toArray();
            }

            // Estructura compatible con JSON Server: {"productes": [...]}
            $wrapper = [
                'productes' => $productes,
                '_meta' => [
                    'importat_el' => date('c'),
                    'importat_per' => $_SESSION['usuari'] ?? 'anònim',
                    'total' => count($productes),
                ]
            ];

            $json = json_encode($wrapper, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                throw new Exception("Error al codificar JSON: " . json_last_error_msg());
            }

            $written = @file_put_contents($productsFile, $json, LOCK_EX);
            if ($written === false) {
                throw new Exception("No se pudo escribir en $productsFile. Revisa los permisos del directorio /data/.");
            }

            $importResult = [
                'success' => true,
                'total'   => count($productes),
                'errors'  => $errors,
                'user'    => $_SESSION['usuari'] ?? 'anònim',
                'date'    => date('d/m/Y H:i:s'),
            ];

        } catch (Exception $e) {
            $importResult = ['error' => $e->getMessage()];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/cataleg.css">
    <title>Importar productes · KoffTea</title>
</head>
<body>

<header class="header">
    <h1><i class="fa-solid fa-file-import"></i> Importar productes</h1>
    <a href="index.php"><i class="fa-solid fa-house"></i> Tornar a l'inici</a>
</header>

<main>
    <div class="card">
        <h2><i class="fa-solid fa-table"></i> Càrrega de catàleg Excel</h2>
        <p class="subtitle">Importa el fitxer Excel del client per actualitzar automàticament el catàleg de productes.</p>

        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <label for="excel" id="file-label">
                <div class="upload-area" id="dropZone">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <p>Fes clic per seleccionar o arrossega el fitxer aquí</p>
                    <p class="formats">Formats acceptats: .xlsx, .xls</p>
                    <span id="file-name"></span>
                </div>
            </label>
            <input type="file" name="excel" id="excel" accept=".xlsx,.xls" required style="display:none">
            <button type="submit" class="btn">
                <i class="fa-solid fa-upload"></i> Importar productes
            </button>
        </form>

        <?php if ($importResult !== null): ?>
            <?php if (isset($importResult['error'])): ?>
                <div class="result error">
                    <div class="result-title">
                        <i class="fa-solid fa-circle-xmark"></i> Error en la importació
                    </div>
                    <p><?= htmlspecialchars($importResult['error'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>

            <?php else: ?>
                <div class="result success">
                    <div class="result-title">
                        <i class="fa-solid fa-circle-check"></i> Importació completada
                    </div>
                    <div class="stats">
                        <div class="stat">
                            <div class="num"><?= $importResult['total'] ?></div>
                            <div class="lbl">Productes importats</div>
                        </div>
                        <div class="stat">
                            <div class="num"><?= count($importResult['errors']) ?></div>
                            <div class="lbl">Files ignorades</div>
                        </div>
                    </div>
                    <div class="meta">
                        <i class="fa-solid fa-clock"></i> <?= $importResult['date'] ?> &nbsp;·&nbsp;
                        <i class="fa-solid fa-user"></i> <?= htmlspecialchars($importResult['user'], ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <?php if (!empty($importResult['errors'])): ?>
                        <div class="errors-list">
                            <p><i class="fa-solid fa-triangle-exclamation"></i> Files amb errors o duplicats:</p>
                            <ul>
                                <?php foreach ($importResult['errors'] as $err): ?>
                                    <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<footer>
    &copy; 2025 KoffTea Times
</footer>

<script>
    document.getElementById('excel').addEventListener('change', function () {
        const name = this.files[0] ? this.files[0].name : '';
        document.getElementById('file-name').textContent = name ? '📄 ' + name : '';
    });
</script>

</body>
</html>
