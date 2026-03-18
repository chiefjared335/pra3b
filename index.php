<?php
require_once 'backend/conn.php';

try {
    $stmt = $conn->prepare("SELECT * FROM taken ORDER BY created_at DESC");
    $stmt->execute();
    $allTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $usersStmt = $conn->prepare("SELECT id, naam FROM users");
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Make an array to easily associate the users ID with their name
    $userLookup = [];
    foreach ($users as $user) {
        $userLookup[$user['id']] = $user['naam'];
    }

    // Seperate arrays for each task status
    $todoTasks = [];
    $bezigTasks = [];
    $klaarTasks = [];

    foreach ($allTasks as $task) {
        if ($task['status'] === 'todo') {
            $todoTasks[] = $task;
        } elseif ($task['status'] === 'bezig') {
            $bezigTasks[] = $task;
        } elseif ($task['status'] === 'klaar') {
            $klaarTasks[] = $task;
        }
    }
} catch (PDOException $e) {
    die("Error fetching tasks: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="nl">

<head>
    <title>Takenlijst</title>
    <?php require_once 'head.php'; ?>
</head>

<body>

    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <nav>
                    <a href="tasks/create.php">Nieuwe Taak</a><br>
                    <a href="tasks/afdeling.php" class="afdeling-link">Afdelingen</a>
                </nav>
                <h1 class="logo">Takenlijst</h1>
                <div></div>
            </div>
        </div>
    </header>
    

    <div class="container">
        <h1>Takenlijst</h1>

        <div class="kanban-board">
            <!-- Column 1: To Do -->
            <div class="kanban-column">
                <h2 class="column-header">Te Doen (<?php echo count($todoTasks); ?>)</h2>
                <?php if (empty($todoTasks)): ?>
                    <p>Geen taken</p>
                <?php else: ?>
                    <ul class="tasks-list">
                        <?php foreach ($todoTasks as $task): ?>
                            <li class="task-item">
                                <a href="tasks/details.php?id=<?php echo $task['id']; ?>">
                                    <?php echo htmlspecialchars($task['titel']); ?>
                                </a>
                                <p><strong>Beschrijving:</strong> <?php echo htmlspecialchars($task['beschrijving']); ?></p>
                                <p><strong>Afdeling:</strong> <?php echo htmlspecialchars($task['afdeling']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
                                <p><strong>Deadline:</strong> <?php echo $task['deadline'] ? date('d-m-Y', strtotime($task['deadline'])) : 'Geen deadline'; ?></p>
                                <p><strong>Toegewezen aan:</strong> <?php echo isset($userLookup[$task['user']]) ? htmlspecialchars($userLookup[$task['user']]) : 'Niet toegewezen'; ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Column 2: In Progress -->
            <div class="kanban-column">
                <h2 class="column-header">Bezig (<?php echo count($bezigTasks); ?>)</h2>
                <?php if (empty($bezigTasks)): ?>
                    <p>Geen taken</p>
                <?php else: ?>
                    <ul class="tasks-list">
                        <?php foreach ($bezigTasks as $task): ?>
                            <li class="task-item">
                                <a href="tasks/details.php?id=<?php echo $task['id']; ?>">
                                    <?php echo htmlspecialchars($task['titel']); ?>
                                </a>
                                <p><strong>Beschrijving:</strong> <?php echo htmlspecialchars($task['beschrijving']); ?></p>
                                <p><strong>Afdeling:</strong> <?php echo htmlspecialchars($task['afdeling']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
                                <p><strong>Deadline:</strong> <?php echo $task['deadline'] ? date('d-m-Y', strtotime($task['deadline'])) : 'Geen deadline'; ?></p>
                                <p><strong>Toegewezen aan:</strong> <?php echo isset($userLookup[$task['user']]) ? htmlspecialchars($userLookup[$task['user']]) : 'Niet toegewezen'; ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Column 3: Done -->
            <div class="kanban-column">
                <h2 class="column-header">Klaar (<?php echo count($klaarTasks); ?>)</h2>
                <?php if (empty($klaarTasks)): ?>
                    <p>Geen taken</p>
                <?php else: ?>
                    <ul class="tasks-list">
                        <?php foreach ($klaarTasks as $task): ?>
                            <li class="task-item">
                                <a href="tasks/details.php?id=<?php echo $task['id']; ?>">
                                    <?php echo htmlspecialchars($task['titel']); ?>
                                </a>
                                <p><strong>Beschrijving:</strong> <?php echo htmlspecialchars($task['beschrijving']); ?></p>
                                <p><strong>Afdeling:</strong> <?php echo htmlspecialchars($task['afdeling']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
                                <p><strong>Deadline:</strong> <?php echo $task['deadline'] ? date('d-m-Y', strtotime($task['deadline'])) : 'Geen deadline'; ?></p>
                                <p><strong>Toegewezen aan:</strong> <?php echo isset($userLookup[$task['user']]) ? htmlspecialchars($userLookup[$task['user']]) : 'Niet toegewezen'; ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>
