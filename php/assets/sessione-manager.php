<?php

session_start();

/**
 * Imposta l'email con cui l'utente ha eseguito il login dentro la variabile di sessione
 *
 * @param string $email Email dell'utente
 */
function setSession($email) {
    $_SESSION['SB-logged'] = $email;
}

/**
 * @return bool True se l'utente è loggato, false altrimenti
 */
function isLogged() {
    return isset($_SESSION['SB-logged']);
}

/**
 * Restituisce l'email memorizzata nella variabile di sessione
 *
 * @return string Email dell'utente loggato
 */
function getLoginEmail() {
    return $_SESSION['SB-logged'];
}

/**
 * @return bool True se c'è un messaggio di alert da mostrare
 */
function isAlertPresent() {
    return isset($_SESSION['SB-alert']);
}

/**
 * Restituisce il messaggio di alert da visualizzare
 *
 * @return string Messaggio di alert
 */
function getAlertMessage() {
    return $_SESSION['SB-alert']['message'];
}

/**
 * Restituisce il colore del messaggio di alert da visualizzare
 *
 * @return string Colore del messaggio da mostrare
 */
function getAlertColor() {
    return $_SESSION['SB-alert']['color'];
}

/**
 * Imposta un messaggio di alert
 *
 * @param string $message Messaggio dell'alert
 * @param string $color Colore dell'alert
 */
function setAlert($message, $color) {
    $_SESSION['SB-alert'] = array('message' => $message, 'color' => $color);
}

/**
 * Rimuove il messaggio di alert
 */
function unsetAlert() {
    unset($_SESSION['SB-alert']);
}

/**
 * Se presente, mostra il messaggio di alert all'interno di un paragrafo formattato
 */
function showAlert() {
    if(isAlertPresent()) {
        echo '<p class="alert ' . getAlertColor() . '">' . getAlertMessage() . '</p>';

        unsetAlert(); // Rimuove l'alert
    }
}