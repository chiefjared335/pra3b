<!doctype html>
<html lang="nl">

<head>
    <title>Registreren - Takenlijst</title>
    <?php require_once 'head.php'; ?>
</head>

<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div></div>
                <h1 class="logo">Takenlijst</h1>
                <div class="header-actions">
                    <a href="login.php">Terug naar login</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-intro auth-intro">
            <p class="eyebrow">Nieuw account</p>
            <h1>Account registreren</h1>
            <p>Maak een nieuw account aan zodat je kunt inloggen en met de takenlijst kunt werken.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="backend/LoginController.php" method="POST">
            <input type="hidden" name="action" value="register">

            <div class="form-group">
                <label for="naam">Naam:</label>
                <input type="text" id="naam" name="naam" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="username">Gebruikersnaam:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Wachtwoord:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Herhaal wachtwoord:</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Account aanmaken</button>
                <a href="login.php" class="btn btn-secondary">Ik heb al een account</a>
            </div>
        </form>
    </div>
</body>

</html>