/**
 * Questa funzione si occupa di:
 *
 * 1) aggiungere la data odierna agli attributi "max", "title" e "value" del
 *    campo di input contenente la data dell'intervento
 *
 * 2) attivare il contatore di caratteri al campo contenente la descrizione dell'intervento
 */
function addInputHandlers() {
    /* Aggiunge la data odierna agli attributi dell'elemento contenente la data dell'intervento */
    addCurrentDate(document.getElementById("data"), true);


    /* Inizializza il contatore del campo per la descrizione */
    initCounter(document.getElementById("registra-intervento"), document.getElementById("descrizione"));
}