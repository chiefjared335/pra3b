<!doctype html>
<html lang="nl">

<head>
    <title>Login - Takenlijst</title>
    <?php require_once 'head.php'; ?>
</head>

<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <h1 class="logo">Takenlijst</h1>
                <div></div>
            </div>
        </div>
    </header>

    <div class="container">
        <h1>Login</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="backend/LoginController.php" method="POST">
            <input type="hidden" name="action" value="login">
            
            <div class="form-group">
                <label for="username">Gebruikersnaam:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Wachtwoord:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Inloggen</button>
        </form>
    </div>
</body>

</html>
