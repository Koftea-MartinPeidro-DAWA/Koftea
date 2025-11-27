<?php
require __DIR__ . "/../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use Koftea\Producto;

// Guardar en data/productos.json dentro del proyecto
$dataDir = __DIR__ . "/../data";
$db_table_productes = $dataDir . "/productos.json";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['excel']) || $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
        echo "<p style='color:red'>Error: No se pudo subir el archivo.</p>";
        exit;
    }

    $excelFile = $_FILES['excel']['tmp_name'];

    try {
        // Asegurar que el directorio data exista
        if (!is_dir($dataDir)) {
            if (!@mkdir($dataDir, 0775, true)) {
                throw new Exception("No se pudo crear el directorio de datos: $dataDir. En la terminal ejecuta:\n" .
                    "sudo mkdir -p " . escapeshellcmd($dataDir) . " && sudo chown -R www-data:www-data " . escapeshellcmd($dataDir) . " && sudo chmod -R 775 " . escapeshellcmd($dataDir));
            }
        }

        $spreadsheet = IOFactory::load($excelFile);
        $sheet = $spreadsheet->getActiveSheet();
        $excel_data = $sheet->toArray(null, true, true, true);

        $valid_headers = [
            "ID", "Categoría", "Nombre", "Descripción", "Procedencia/Origen",
            "Intensidad", "Formato", "Precio (€)", "Stock",
            "Detalle Específico", "Imagen (img)"
        ];

        // Comprobar que hay al menos una fila de cabeceras
        if (!isset($excel_data[1]) || !is_array($excel_data[1])) {
            throw new Exception("El archivo no contiene una fila de cabeceras válida.");
        }

        // Construir mapa de cabeceras: ["Nombre cabecera" => "A", ...]
        $firstRow = $excel_data[1];
        $header_map = [];
        foreach ($firstRow as $col => $value) {
            $key = trim((string)$value);
            if ($key !== '') {
                $header_map[$key] = $col;
            }
        }

        // Validar que existan todas las cabeceras obligatorias
        foreach ($valid_headers as $header) {
            if (!isset($header_map[$header])) {
                throw new Exception("Falta el campo obligatorio: $header");
            }
        }

        // Quitar la fila de cabeceras
        array_shift($excel_data);

        $productes = [];

        foreach ($excel_data as $row) {
            // Saltar filas completamente vacías
            $allEmpty = true;
            foreach ($row as $cell) {
                if (trim((string)$cell) !== '') { $allEmpty = false; break; }
            }
            if ($allEmpty) continue;

            // Normalizar precio (coma -> punto, eliminar símbolos)
            $rawPrice = $row[$header_map["Precio (€)"]] ?? '';
            $rawPrice = preg_replace('/[^\d,.\-]/u', '', (string)$rawPrice);
            $rawPrice = str_replace(',', '.', $rawPrice);
            $price = $rawPrice === '' ? 0.0 : (float)$rawPrice;

            $productoObj = new Producto(
                $row[$header_map["ID"]] ?? "",
                $row[$header_map["Categoría"]] ?? "",
                $row[$header_map["Nombre"]] ?? "",
                $row[$header_map["Descripción"]] ?? "",
                $row[$header_map["Procedencia/Origen"]] ?? "",
                $row[$header_map["Intensidad"]] ?? "",
                $row[$header_map["Formato"]] ?? "",
                $price,
                (int)($row[$header_map["Stock"]] ?? 0),
                $row[$header_map["Detalle Específico"]] ?? "",
                $row[$header_map["Imagen (img)"]] ?? ""
            );

            $productes[] = $productoObj->toArray();
        }

        $json = json_encode($productes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new Exception("Error al codificar JSON: " . json_last_error_msg());
        }

        // Intento de escritura con bloqueo
        $written = @file_put_contents($db_table_productes, $json, LOCK_EX);
        if ($written === false) {
            // Mensaje claro con comandos para arreglar permisos
            $suggest = "Para arreglar permisos en Linux ejecuta:\n" .
                       "sudo chown -R www-data:www-data " . escapeshellcmd($dataDir) . "\n" .
                       "sudo chmod -R 775 " . escapeshellcmd($dataDir);
            throw new Exception("No se pudo escribir en $db_table_productes. " . $suggest);
        }

        echo "<p style='color:green'>Datos importados correctamente.</p>";
        echo "<p>Total de productos añadidos: <b>" . count($productes) . "</b></p>";

    } catch (Exception $e) {
        echo "<p style='color:red'>Error al procesar el archivo: " . nl2br(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Importar productos</title>
</head>
<body>
    <h2>Importar listado de productos desde Excel</h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Selecciona el archivo Excel (.xlsx):</label><br><br>
        <input type="file" name="excel" accept=".xlsx,.xls" required>
        <br><br>
        <button type="submit">Importar</button>
    </form>
</body>
</html>
