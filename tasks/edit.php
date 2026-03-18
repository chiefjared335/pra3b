<?php
    // Start session and check if user is logged in
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
                    <input type="text" id="afdeling" name="afdeling" class="form-input" value="<?php echo htmlspecialchars($task['afdeling']); ?>" required>
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
        </div>

    </div>

</body>

</html>
