<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../backend/config.php';
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Taak</title>
    <?php require_once '../head.php'; ?>
</head>

<body>

<?php
require_once '../backend/conn.php';

$query = "SELECT * FROM taken";
$statement = $conn->prepare($query);
$statement->execute();
$meldingen = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">

<header class="site-header">
    <h1>Nieuwe Taak Aanmaken</h1>
</header>



<div class="task-edit-form">

<form action="../backend/taskController.php" method="POST">

<input type="hidden" name="action" value="create">

<div class="form-group">
<label for="titel">Taak:</label>
<input type="text" id="titel" name="titel" class="form-input" placeholder="Naam van de taak">
</div>

<div class="form-group">
<label for="beschrijving">Beschrijving:</label>
<input type="text" id="beschrijving" name="beschrijving" class="form-input" placeholder="Beschrijving van de taak">
</div>

<div class="form-group">
<label for="deadline">Deadline:</label>
<input type="date" id="deadline" name="deadline" class="form-input">
</div>

<div class="form-group">
<label for="afdeling">Afdeling:</label>
<select name="afdeling" id="afdeling" class="form-input">
<option value="">– kies een afdeling –</option>
<option value="personeel">Personeel</option>
<option value="horeca">Horeca</option>
<option value="techniek">Techniek</option>
<option value="inkoop">Inkoop</option>
<option value="klantenservice">Klantenservice</option>
<option value="groen">Groen</option>
</select>
</div>
<div class="form-group">
    <label for="cat">Categorie:</label>
    <select name="cat" id="cat" class="form-input">
        <option value="">– kies een categorie –</option>
        <option value="zwart">Geen Urgente (Zwart)</option>
        <option value="rood">Geen prioriteit (Rood)</option>
        <option value="oranje">Lage Prioriteit (Oranje)</option>
        <option value="groen">Middelmatige Prioriteit (Groen)</option>
        <option value="blauw">Belangrijk (Blauw)</option>
        <option value="donkergroen">Heel Belangrijk (Donker groen)</option>
    </select>
</div>

<div class="form-actions">
<button type="submit" class="btn btn-primary">Taak aanmaken</button>
</div>

</form>

</div>

<a href="../index.php" class="btn btn-secondary">Terug naar overzicht</a>
</div>

</body>
</html>
