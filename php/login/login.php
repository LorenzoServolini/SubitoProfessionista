<?php
    require_once __DIR__ . "/../assets/sessione-manager.php";

    // Se l'utente ha già eseguito l'accesso riportarlo alla home
    if(isLogged()) {
        header('Location: ../../index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="login SubitoProfessionista">
    <meta name="description" content="Pagina di login di SubitoProfessionista">

    <link rel="shortcut icon" type="image/x-icon" href="../../img/favicon.ico">

    <link rel="stylesheet" type="text/css" href="../../css/layout.css">

    <title>Login - SubitoProfessionista</title>
</head>

<body>
    <nav>
        <ul>
            <li><a href="../../index.php">Home</a></li>
            <li><a href="../registra-professionista/registrazione.php">Registrazione - professionista</a></li>
            <li><a href="../registra-utente/registrazione.php">Registrazione - utente</a></li>
            <li><a class="active" href="login.php">Login</a></li>
            <li class="right"><a href="../manuale.php">About</a></li>
        </ul>
    </nav>

    <div class="page-container">
        <h1>Accedi</h1>

        <form action="login-handler.php" method="post" id="login" class="pannello">
            <img src="../../img/avatar.png" alt="Avatar">

            <div class="main-panel">
                <?php showAlert(); ?>

                <label for="email"><b>Email</b></label>
                <input class="input-field" id="email" type="email" placeholder="Email" name="email" title="Indirizzo email" autocomplete="on" tabindex="1" required>

                <label for="psw"><b>Password</b></label>
                <input class="input-field" id="psw" type="password" placeholder="Password" name="psw" title="Password" autocomplete="off" tabindex="2" required>
                <button type="submit" class="submit" tabindex="3">Login</button>

                <button type="reset" class="cancel" tabindex="4">Reset</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../../html/layouts/footer.html"; ?>
</body>
</html>