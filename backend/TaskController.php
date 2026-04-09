<?php
session_start();

require_once 'conn.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {

   
    if  (empty($_POST['titel']) || empty($_POST['beschrijving']) || empty($_POST['deadline']) || empty($_POST['afdeling'])) {
        die("Alle verplichte velden moeten ingevuld zijn.");
    }

    $id = $_POST['id'];
    $titel = trim($_POST['titel']);
    $beschrijving = trim($_POST['beschrijving']);
    $afdeling = trim($_POST['afdeling']); 
    $status = $_POST['status'];
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $user = !empty($_POST['user']) ? $_POST['user'] : null;

    
    $allowedStatuses = ['todo', 'bezig', 'klaar'];
    if (!in_array($status, $allowedStatuses)) {
        die("Ongeldige status waarde.");
    }

  
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
          
            header("Location: ../tasks/details.php?id=" . $id);
            exit;
        } else {
            die("Fout bij het bijwerken van de taak.");
        }

    } catch (PDOException $e) {
        die("Database fout: " . $e->getMessage());
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $titel = $_POST['titel'];
    if (empty($titel)) {
        die("Titel is verplicht");
    }
    $beschrijving = $_POST['beschrijving'];
    if (empty($beschrijving)) {
        die("Beschrijving is verplicht");
    }
    $deadline = $_POST['deadline'];
    if (empty($deadline)) {
        die("Deadline is verplicht");
    }

    $afdeling = $_POST['afdeling'];
    if (empty($afdeling)) {
        die("Afdeling is verplicht");
    }


    $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    echo $titel . " / " . $beschrijving . " / " . $deadline . " / " . $afdeling;
    require_once 'conn.php';

    $query = "INSERT INTO taken (titel, beschrijving, deadline, afdeling, user)
    VALUES(:titel, :beschrijving, :deadline, :afdeling, :user)";

    $statement = $conn->prepare($query);

    $statement->execute([
        ":titel" => $titel,
        ":beschrijving" => $beschrijving,
        ":deadline" => $deadline,
        ":afdeling" => $afdeling,
        ":user" => $user
    ]);
    header("Location: ../index.php?msg=Melding aangepast");
}


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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_complete') {


    if (empty($_POST['id'])) {
        die("Taak ID is vereist.");
    }

    $taskId = $_POST['id'];


    try {
        $query = "UPDATE taken SET status = 'klaar' WHERE id = :id";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([':id' => $taskId]);

        if ($result) {
            $referrer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
            header("Location: " . $referrer);
            exit;
        } else {
            die("Fout bij het markeren van de taak als klaar.");
        }

    } catch (PDOException $e) {
        die("Database fout: " . $e->getMessage());
    }
}

header("Location: ../index.php");
exit;
