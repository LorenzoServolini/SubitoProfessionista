<?php

require_once __DIR__ . "/../assets/sessione-manager.php";
require_once __DIR__ . "/../assets/validation.php";

// Se l'utente ha già eseguito l'accesso riportarlo alla home
if(isLogged()) {
    header('Location: ../../index.php');
    exit();
}

if(empty($_POST['nome']) || empty($_POST['cognome']) || empty($_POST['professione']) || empty($_POST['province'])
    || empty($_POST['email']) || empty($_POST['psw']) || empty($_POST['psw-repeat']))
{
    setAlert('Non tutti i campi sono stati inseriti', 'red');
    header('Location: registrazione.php');
    exit();
}


$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$professione = $_POST['professione'];
$email = $_POST['email'];
$prezzi = (empty($_POST['prezzi']) ? null : htmlentities($_POST['prezzi']));
$descrizione = (empty($_POST['descrizione']) ? null : htmlentities($_POST['descrizione']));
$contatti = (empty($_POST['contatti']) ? null : htmlentities($_POST['contatti']));
$orari = (empty($_POST['orari']) ? null : htmlentities($_POST['orari']));
$genere = (empty($_POST['genere']) ? null : $_POST['genere']);
$copertura = $_POST['province'];
$password = $_POST['psw'];
$password_repeat = $_POST['psw-repeat'];
$share = ((isset($_POST['share']) && $_POST['share'] === 'on') ? '1' : '0');


$validation = new Validation();
$validation->name('nome')->value($nome)->customRegex('/^[A-Z][A-Z ]{0,19}$/i');
$validation->name('cognome')->value($cognome)->customRegex('/^[A-Z][A-Z ]{0,14}$/i');
$validation->name('email')->value($email)->pattern('email')->range(0, 50);
$validation->name('prezzi')->value($prezzi)->range(0, 130);
$validation->name('descrizione')->value($descrizione)->range(0, 200);
$validation->name('contatti')->value($contatti)->range(0, 100);
$validation->name('orari')->value($orari)->range(0, 300);
$validation->name('zone servite')->value($copertura)->vectorial()->range(1, PHP_INT_MAX);
$validation->name('password')->value($password)->range(5, 72);
$validation->name('conferma password')->value($password_repeat)->equal($password);

if (!$validation->isSuccess()) // Se non sono stati rispettati i formati di tutti i campi
{
    setAlert($validation->getError(), 'red');
    header('Location: registrazione.php');
    exit();
}

// In caso di genere non specificato o non riconosciuto (cioè diverso da M e F)
if ($genere !== 'M' && $genere !== 'F')
    $genere = 'U';


require_once __DIR__ . "/../assets/database-manager.php";
global $database;

// Controlla che la professione inserita esista
if (!$database->existsProfession($professione)) {
    $database->closeConnection();

    setAlert('La professione ' . Validation::purify($professione) . ' non esiste', 'red');
    header('Location: registrazione.php');
    exit();
}

// Controlla che l'email inserita non sia già registrata (ad un utente o a un professionista)
if (!$database->availableEmail($email)) {
    $database->closeConnection();

    setAlert("L'email {$email} risulta già registrata", "red");
    header('Location: registrazione.php');
    exit();
}

// Controlla che tutte le province inserite esistano
foreach ($copertura as $provincia) {
    $sql = "SELECT EXISTS(SELECT 1 FROM provincia WHERE Nome = ?) AS found;";
    $stmt = $database->prepared_query($sql, array('s' => $provincia));
    $result = $database->getResult($stmt);

    // Provincia inesistente
    if(!$database->fetch($result, 'found')) {
        $database->closeConnection();

        setAlert('La provincia '  . Validation::purify($provincia) . ' non esiste', 'red');
        header('Location: registrazione.php');
        exit();
    }
}



/* *** Registrazione con input verificati *** */
// Memorizza nome e cognome con la sola prima lettera maiuscola
$formatted_name = ucfirst(strtolower($nome));
$formatted_surname = ucfirst(strtolower($cognome));
$hashed_psw = password_hash($password, PASSWORD_BCRYPT);

$stmt = $database->prepared_query("INSERT INTO professionista VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array('s' => array($email, $hashed_psw, $genere, $formatted_name, $formatted_surname, $professione, $prezzi, implode(",", $copertura), $descrizione, $contatti, $orari, $share)));
$database->closeStatement($stmt);
$database->closeConnection();


// Ricorda l'utente in modo che al prossimo accesso al sito non debba effettuare il login di nuovo
setSession($email);

// Visualizza messaggio di successo colorato
setAlert('Registrazione avvenuta con successo', 'green');
header('Location: ../../index.php');