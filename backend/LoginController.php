<?php

require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    header("Location: ../login.php");
    exit;
}

$action = $_POST['action'];

if ($action === 'login') {
    session_start();

    if (empty($_POST['username']) || empty($_POST['password'])) {
        header("Location: ../login.php?error=Alle velden zijn verplicht");
        exit;
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $query = "SELECT id, username, password FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: ../index.php");
            exit;
        }

        header("Location: ../login.php?error=Ongeldige gebruikersnaam of wachtwoord");
        exit;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        header("Location: ../login.php?error=Er is een fout opgetreden. Probeer opnieuw.");
        exit;
    }
}

if ($action === 'register') {
    $naam = trim($_POST['naam'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($naam === '' || $username === '' || $password === '' || $password_confirm === '') {
        header("Location: ../register.php?error=Alle velden zijn verplicht");
        exit;
    }

    if ($password !== $password_confirm) {
        header("Location: ../register.php?error=Wachtwoorden komen niet overeen");
        exit;
    }

    if (strlen($password) < 8) {
        header("Location: ../register.php?error=Het wachtwoord moet minimaal 8 tekens bevatten");
        exit;
    }

    try {
        $query = "SELECT id FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $username]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            header("Location: ../register.php?error=Deze gebruikersnaam bestaat al");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (naam, username, password) VALUES (:naam, :username, :password)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':naam' => $naam,
            ':username' => $username,
            ':password' => $hashedPassword,
        ]);

        header("Location: ../login.php?success=Account succesvol aangemaakt. Je kunt nu inloggen.");
        exit;
    } catch (PDOException $e) {
        error_log("Register error: " . $e->getMessage());
        header("Location: ../register.php?error=Er is een fout opgetreden. Probeer opnieuw.");
        exit;
    }
}

header("Location: ../login.php");
exit;

?>
