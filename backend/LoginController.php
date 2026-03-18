<?php

require_once 'conn.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    session_start();

    // Validate required fields
    if (empty($_POST['username']) || empty($_POST['password'])) {
        header("Location: ../login.php?error=Alle velden zijn verplicht");
        exit;
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Query user by username
        $query = "SELECT id, username, password FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Make sure we regenerate a session ID because if
            // you dont do this, it could be vulnerable to exploits
            session_regenerate_id();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to index page
            header("Location: ../index.php");
            exit;
        } else {
            // Generic error message for security
            header("Location: ../login.php?error=Ongeldige gebruikersnaam of wachtwoord");
            exit;
        }

    } catch (PDOException $e) {
        // Log error and show generic message to user
        error_log("Login error: " . $e->getMessage());
        header("Location: ../login.php?error=Er is een fout opgetreden. Probeer opnieuw.");
        exit;
    }
}

// If not a valid POST request, redirect to login page
header("Location: ../login.php");
exit;

?>
