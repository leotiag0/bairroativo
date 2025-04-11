<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $file = $_FILES['arquivo']['tmp_name'];
    $handle = fopen($file, 'r');
    $header = fgetcsv($handle);

    echo "<h2>Pré-visualização do arquivo CSV</h2>";
    echo "<table border='1' cellpadding='6' cellspacing='0'>";
    echo "<tr>";
    foreach ($header as $col) echo "<th>" . htmlspecialchars($col) . "</th>";
    echo "</tr>";

    $count = 0;
    while (($row = fgetcsv($handle)) !== FALSE && $count < 30) {
        echo "<tr>";
        foreach ($row as $cell) echo "<td>" . htmlspecialchars($cell) . "</td>";
        echo "</tr>";
        $count++;
    }
    echo "</table>";

    fclose($handle);
} else {
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pré-visualizar CSV</title>
</head>
<body>
    <h1>Pré-visualização de CSV</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".csv" required>
        <button type="submit">Visualizar</button>
    </form>
</body>
</html>
<?php } ?>
