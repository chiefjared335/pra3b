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
    $query = "SELECT * FROM taken WHERE id = :id";
    $statement = $conn->prepare($query);
    $statement->execute([
        ":id"    => $id,
    ]);
    $task = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        die("Taak niet gevonden");
    }
?>

<!doctype html>
<html lang="nl">

<head>
    <title>Ticket Details</title>
    <?php require_once '../head.php'; ?>
</head>

<body>
    <div class="container">
        <header class="site-header">
            <h1>Taak Details</h1>
            <a href="<?php echo $base_url; ?>/index.php">Terug naar overzicht</a>
        </header>
        

        <div class="task-details">
            <div class="detail-row">
                <strong>Titel:</strong>
                <span><?php echo htmlspecialchars($task['titel']); ?></span>
            </div>

            <div class="detail-row">
                <strong>Beschrijving:</strong>
                <span><?php echo htmlspecialchars($task['beschrijving']); ?></span>
            </div>

            <div class="detail-row">
                <strong>Afdeling:</strong>
                <span><?php echo htmlspecialchars($task['afdeling']); ?></span>
            </div>

            <div class="detail-row">
                <strong>Status:</strong>
                <span><?php echo htmlspecialchars($task['status']); ?></span>
            </div>

            <div class="detail-row">
                <strong>Deadline:</strong>
                <span><?php echo $task['deadline'] ? date('d-m-Y', strtotime($task['deadline'])) : 'Geen deadline'; ?></span>
            </div>

            <div class="detail-row">
                <strong>Aangemaakt op:</strong>
                <span><?php echo $task['created_at'] ? date('d-m-Y H:i', strtotime($task['created_at'])) : 'Onbekend'; ?></span>
            </div>

            <div class="detail-row">
                <strong>Toegewezen aan gebruiker ID:</strong>
                <span><?php echo $task['user'] ? htmlspecialchars($task['user']) : 'Niet toegewezen'; ?></span>
            </div>
        </div>

        <div class="task-actions">
            <a href="edit.php?id=<?php echo $task['id']; ?>" class="btn btn-primary">Taak aanpassen</a>
        </div>

    </div>

</body>

</html>
