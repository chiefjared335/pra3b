<?php
require_once '../backend/conn.php';

$selectedAfdeling = $_GET['afdeling'] ?? 'all';
$selectedUser = $_GET['user'] ?? 'all';

try {

    $afdelingen = ['personeel', 'horeca', 'techniek', 'inkoop', 'klantenservice', 'groen'];



        $sql = "SELECT * FROM taken WHERE 1=1";
        $params = [];


        if ($selectedAfdeling !== 'all') {
            $sql .= " AND afdeling = :afdeling";
            $params[':afdeling'] = $selectedAfdeling;
        }


        if ($selectedUser !== 'all') {
            $sql .= " AND user = :user";
            $params[':user'] = $selectedUser;
        }


        $sql .= " ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

    $allTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $usersStmt = $conn->prepare("SELECT id, naam FROM users");
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

    $userLookup = [];
    foreach ($users as $user) {
        $userLookup[$user['id']] = $user['naam'];
    }

 
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
    <?php require_once '../head.php'; ?>
</head>

<body>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <nav>
                <a href="create.php">Nieuwe Taak</a><br>
                <a href="../index.php" class="afdeling-link">takenlijst</a>
            </nav>
            <h1 class="logo">Takenlijst</h1>
            <div></div>
        </div>
    </div>
</header>

<div class="container">
    <h1>Takenlijst</h1>


 <form method="GET">
    <label for="afdeling">Kies afdeling:</label>
    <select name="afdeling" id="afdeling" class="form-input1" onchange="this.form.submit()">
        <option value="all">Alle afdelingen</option>
        <?php foreach ($afdelingen as $afd): ?>
            <option value="<?php echo $afd; ?>" <?php if ($selectedAfdeling === $afd) echo 'selected'; ?>>
                <?php echo $afd; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="user">Kies gebruiker:</label>
    <select name="user" id="user" class="form-input1" onchange="this.form.submit()">
        <option value="all">Alle gebruikers</option>
        <?php foreach ($users as $usr): ?>
            <option value="<?php echo $usr['id']; ?>" <?php if ($selectedUser == $usr['id']) echo 'selected'; ?>>
                <?php echo $usr['naam']; ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

    <div class="kanban-board">


        <div class="kanban-column">
            <h2 class="column-header">Te Doen (<?php echo count($todoTasks); ?>)</h2>
            <?php if (empty($todoTasks)): ?>
                <p>Geen taken</p>
            <?php else: ?>
                <ul class="tasks-list">
                    <?php foreach ($todoTasks as $task): ?>
                        <li class="task-item">
                            <a href="details.php?id=<?php echo $task['id']; ?>">
                                <?php echo htmlspecialchars($task['titel']); ?>
                            </a>
                            <p><strong>Afdeling:</strong> <?php echo htmlspecialchars($task['afdeling']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>



        <div class="kanban-column">
            <h2 class="column-header">Bezig (<?php echo count($bezigTasks); ?>)</h2>
            <?php if (empty($bezigTasks)): ?>
                <p>Geen taken</p>
            <?php else: ?>
                <ul class="tasks-list">
                    <?php foreach ($bezigTasks as $task): ?>
                        <li class="task-item">
                            <a href="details.php?id=<?php echo $task['id']; ?>">
                                <?php echo htmlspecialchars($task['titel']); ?>
                            </a>
                            <p><strong>Afdeling:</strong> <?php echo htmlspecialchars($task['afdeling']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>


        <div class="kanban-column">
            <h2 class="column-header">Klaar (<?php echo count($klaarTasks); ?>)</h2>
            <?php if (empty($klaarTasks)): ?>
                <p>Geen taken</p>
            <?php else: ?>
                <ul class="tasks-list">
                    <?php foreach ($klaarTasks as $task): ?>
                        <li class="task-item">
                            <a href="details.php?id=<?php echo $task['id']; ?>">
                                <?php echo htmlspecialchars($task['titel']); ?>
                            </a>
                            <p><strong>Afdeling:</strong> <?php echo htmlspecialchars($task['afdeling']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>