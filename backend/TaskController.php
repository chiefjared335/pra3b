<?php

require_once 'conn.php';

// Handle task update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {

    // Validate required fields
    if  (empty($_POST['titel']) || empty($_POST['beschrijving']) || empty($_POST['afdeling'])) {
        die("Alle verplichte velden moeten ingevuld zijn.");
    }

    $id = $_POST['id'];
    $titel = trim($_POST['titel']);
    $beschrijving = trim($_POST['beschrijving']);
    $afdeling = trim($_POST['afdeling']);
    $status = $_POST['status'];
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $user = !empty($_POST['user']) ? $_POST['user'] : null;

    // Validate status value
    $allowedStatuses = ['todo', 'bezig', 'klaar'];
    if (!in_array($status, $allowedStatuses)) {
        die("Ongeldige status waarde.");
    }

    // Validate user exists if provided
    if ($user !== null) {
        try {
            $userCheckStmt = $conn->prepare("SELECT id FROM users WHERE id = :user_id");
            $userCheckStmt->execute([':user_id' => $user]);
            if (!$userCheckStmt->fetch()) {
                die("Opgegeven gebruiker bestaat niet.");
            }
        } catch (PDOException $e) {
            die("Fout bij valideren gebruiker: " . $e->getMessage());
        }
    }

    // Update task in database
    try {
        $query = "UPDATE taken
                  SET titel = :titel,
                      beschrijving = :beschrijving,
                      afdeling = :afdeling,
                      status = :status,
                      deadline = :deadline,
                      user = :user
                  WHERE id = :id";

        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            ':titel' => $titel,
            ':beschrijving' => $beschrijving,
            ':afdeling' => $afdeling,
            ':status' => $status,
            ':deadline' => $deadline,
            ':user' => $user,
            ':id' => $id
        ]);

        if ($result) {
            // Redirect back to details page
            header("Location: ../tasks/details.php?id=" . $id);
            exit;
        } else {
            die("Fout bij het bijwerken van de taak.");
        }

    } catch (PDOException $e) {
        die("Database fout: " . $e->getMessage());
    }
}

// Handle task creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $titel = $_POST['titel'];
    if (empty($titel)) {
        die("Titel is verplicht");
    }
    $beschrijving = $_POST['beschrijving'];
    if (empty($beschrijving)) {
        die("Beschrijving is verplicht");
    } 
    $afdeling = $_POST['afdeling'];
    if (empty($afdeling)) {
        die("Afdeling is verplicht");
    }
    echo $titel . " / " . $beschrijving . " / " . $afdeling;
    require_once 'conn.php';

    $query = "INSERT INTO taken (titel, beschrijving, afdeling)
    VALUES(:titel, :beschrijving, :afdeling)";

    $statement = $conn->prepare($query);

    $statement->execute([
        ":titel" => $titel,
        ":beschrijving" => $beschrijving,
        ":afdeling" => $afdeling
    ]);
    header("Location: ../index.php?msg=Melding aangepast");
}

// If not a valid POST request, redirect to index
header("Location: ../index.php");

// Handle task deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        die("Geen taak ID opgegeven.");
    }

    try {
        $query = "DELETE FROM taken WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([":id" => $id]);

        header("Location: ../index.php?msg=Taak verwijderd");
        exit;
    } catch (PDOException $e) {
        die("Database fout: " . $e->getMessage());
    }
}

// If not a valid POST request, redirect to index
header("Location: ../index.php");
exit;