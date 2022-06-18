/**
 * Registra tutti i listener degli eventi necessari per le form di iscrizione al sito.
 * In particolare, la funzione si occupa di:
 *
 * 1) controllare il matching delle password
 * 2) attivare i contatori relativi ai campi di input
 */
function addSignupHandlers() {
    const psw = document.getElementById("psw");
    const pswRepeat = document.getElementById("psw-repeat");

    /* Verifica che le password inserite combacino */
    psw.addEventListener("input", function () { checkPassword(psw, pswRepeat); });
    pswRepeat.addEventListener("input", function () { checkPassword(psw, pswRepeat); });


    /* Inizializza il contatore per ogni campo di input */
    const inputFields = document.querySelectorAll("#registra input.input-field:not(#psw-repeat), #registra textarea.input-field");
    const form = document.getElementById("registra");

    inputFields.forEach(function(field) { initCounter(form, field); });
}

/**
 * Si assicura che le password inserite combacino prima che
 * la form possa essere inviata
 *
 * @param password Password inserita nel primo campo
 * @param repeated Password inserita (come conferma) nel secondo campo
 */
function checkPassword(password, repeated) {
    if (repeated.value !== "") {
        const feedback = document.getElementById("message");
        const button = document.querySelector("#registra button.submit");

        if(password.value !== repeated.value) {
            repeated.style.backgroundColor = "#ff5639";
            feedback.textContent = "Le password non combaciano";
            feedback.style.color = "#c32000";
            button.disabled = true;
        }
        else {
            repeated.style.backgroundColor = "#4CAF50";
            feedback.textContent = "Password uguali!";
            feedback.style.color = "inherit";
            button.disabled = false;
        }
    }
}