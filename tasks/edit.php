<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }

    $id = $_GET["id"];
    if (!isset($id)) die("Task ID not set");

    require_once '../backend/conn.php';

    // Fetch task data
    $query = "SELECT * FROM taken WHERE id = :id";
    $statement = $conn->prepare($query);
    $statement->execute([":id" => $id]);
    $task = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        die("Taak niet gevonden");
    }

    // Fetch all users for dropdown
    $usersQuery = "SELECT id, naam FROM users ORDER BY naam";
    $usersStmt = $conn->prepare($usersQuery);
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="nl">

<head>
    <title>Taak Bewerken</title>
    <?php require_once '../head.php'; ?>
</head>

<body>
    <div class="container">
        <header class="site-header">
            <h1>Taak Bewerken</h1>
            <a href="details.php?id=<?php echo $id; ?>">Terug naar details</a>
        </header>

        <div class="task-edit-form">
            <form action="<?php echo $base_url; ?>/backend/TaskController.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id']); ?>">

                <div class="form-group">
                    <label for="titel">Titel: *</label>
                    <input type="text" id="titel" name="titel" class="form-input" value="<?php echo htmlspecialchars($task['titel']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="beschrijving">Beschrijving: *</label>
                    <textarea id="beschrijving" name="beschrijving" class="form-input" rows="5" required><?php echo htmlspecialchars($task['beschrijving']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="afdeling">Afdeling: *</label>
                    <select id="afdeling" name="afdeling" class="form-input" required>
                        <option value="">– kies een afdeling –</option>
                        <option value="personeel" <?php echo $task['afdeling'] === 'personeel' ? 'selected' : ''; ?>>personeel</option>
                        <option value="horeca" <?php echo $task['afdeling'] === 'horeca' ? 'selected' : ''; ?>>horeca</option>
                        <option value="techniek" <?php echo $task['afdeling'] === 'techniek' ? 'selected' : ''; ?>>techniek</option>
                        <option value="inkoop" <?php echo $task['afdeling'] === 'inkoop' ? 'selected' : ''; ?>>inkoop</option>
                        <option value="klantenservice" <?php echo $task['afdeling'] === 'klantenservice' ? 'selected' : ''; ?>>klantenservice</option>
                        <option value="groen" <?php echo $task['afdeling'] === 'groen' ? 'selected' : ''; ?>>groen</option>
                 </select>
                </div>
                <div class="form-group">
                    <label for="cat">Categorie: *</label>
                    <select id="cat" name="cat" class="form-input" required>
                        <option value="">– kies een categorie –</option>
                        <option value="zwart" <?php echo $task['cat'] === 'zwart' ? 'selected' : ''; ?>> geen Urgente</option>
                        <option value="rood" <?php echo $task['cat'] === 'rood' ? 'selected' : ''; ?>>geen prioriteit</option>
                        <option value="oranje" <?php echo $task['cat'] === 'oranje' ? 'selected' : ''; ?>>Lage Prioriteit</option>
                        <option value="groen" <?php echo $task['cat'] === 'groen' ? 'selected' : ''; ?>>Middelmatige Prioriteit</option>
                        <option value="blauw" <?php echo $task['cat'] === 'blauw' ? 'selected' : ''; ?>>Belangrijk</option>
                        <option value="donkergroen" <?php echo $task['cat'] === 'donkergroen' ? 'selected' : ''; ?>>Heel Belangrijk</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status: *</label>
                    <select id="status" name="status" class="form-input" required>
                        <option value="todo" <?php echo $task['status'] === 'todo' ? 'selected' : ''; ?>>Te Doen</option>
                        <option value="bezig" <?php echo $task['status'] === 'bezig' ? 'selected' : ''; ?>>Bezig</option>
                        <option value="klaar" <?php echo $task['status'] === 'klaar' ? 'selected' : ''; ?>>Klaar</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="deadline">Deadline:</label>
                    <input type="date" id="deadline" name="deadline" class="form-input" value="<?php echo $task['deadline'] ? htmlspecialchars($task['deadline']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="user">Toegewezen aan:</label>
                    <select id="user" name="user" class="form-input">
                        <option value="">Niet toegewezen</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo $task['user'] == $user['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['naam']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                    <a href="details.php?id=<?php echo $id; ?>" class="btn btn-secondary">Annuleren</a>
                </div>
            </form>
            <hr>
         <form action="<?php echo $base_url; ?>/backend/taskController.php" method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" value="Verwijderen" class="btn btn-delete confirm" onclick="return confirm('Weet je zeker dat je deze taak wilt verwijderen?');">
        </form>


        </div>

    </div>

</body>

</html>
