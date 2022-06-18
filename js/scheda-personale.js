/**
 * Questa funzione si occupa di:
 *
 * 1) aggiungere la data odierna agli attributi "max" e "title" dei
 *    campi di input contenenti le date utili al filtraggio dei risultati
 *    (data iniziale e data finale)
 *
 * 2) controllare che venga riempito almeno un campo prima di poter eseguire
 *    un filtraggio dei risultati.
 *    In pratica si occupa di tenere abilitato il bottone di submit (della
 *    form per il filtraggio) solo quando almeno un campo di input è compilato.
 *
 * 3) controllare che la data iniziale inserita non sia superiore alla data finale
 *
 * 4) resettare il campo della professione una volta premuto il tasto di reset.
 *    Ciò non avviene di default poiché il tasto di reset riporta il campo
 *    della professione (menù a tendina) al valore iniziale, che era stato impostato
 *    all'ultima professione inserita come filtro.
 *    Dunque, al reset, si riporta il menù al valore disabilitato (placeholder) "Professione".
 */
function addFilterHandlers() {
    const fromDate = document.getElementById("from");
    const toDate = document.getElementById("to");
    const profession = document.getElementById("professione");


    /* Aggiunge la data odierna agli attributi di quei campi che contengono una data */
    addCurrentDate(fromDate,  false);
    addCurrentDate(toDate,  false);


    /*
     * Controlla i campi per il filtraggio dei risultati:
     * 1) verifica che almeno un campo sia stato riempito
     * 2) verificare la coerenza delle date inserite
     */
    profession.addEventListener("input", function () { checkInput(profession, fromDate, toDate); });
    fromDate.addEventListener("input", function () { checkInput(profession, fromDate, toDate); });
    toDate.addEventListener("input", function () { checkInput(profession, fromDate, toDate); });

    /*
     * Chiama checkInput() una prima volta al caricamento della pagina scheda-personale.php per
     * verificare se c'è già qualche campo non vuoto compilato correttamente (nel qual caso il
     * pulsante va abilitato - di default inizialmente è disabilitato).
     *
     * Si possono avere dei campi già non vuoti quando si è già eseguito un filtraggio (e i campi
     * sono riempiti automaticamente con i parametri precedentemente specificati) oppure se un
     * utente giunge alla pagina tramite un link con i parametri (GET) per il filtraggio già specificati
     */
    checkInput(profession, fromDate, toDate);



    /* Resetta correttamente il campo per il filtraggio della professione */
    document.getElementById("filtra").addEventListener("reset", function () { resetProfession(profession); });
}

/**
 * Verifica:
 * 1) Che almeno uno dei campi per il filtraggio dei risultati sia non vuoto
 * 2) Che la data finale non sia inferiore alla data iniziale
 *
 * Se queste due condizioni non sono verificate il pulsante per sottomettere
 * la form viene disabilitato.
 *
 * @param profession Campo di input per la professione
 * @param fromDate Campo di input per la data iniziale
 * @param toDate Campo di input per la data finale
 */
function checkInput(profession, fromDate, toDate) {
    const button = document.querySelector("#filtra button.submit");

    // Controlla se tutti i campi di input sono vuoti
    const isAllEmpty = profession.value === "" && fromDate.value === "" && toDate.value === "";
    if(isAllEmpty)
        button.disabled = true;

    // Se è stata impostata sia una data iniziale che una finale
    if(fromDate.value !== "" && toDate.value !== "") {
        const feedback = document.getElementById("message");

        // Controlla se la data di inizio preceda la data di fine
        if(Date.parse(toDate.value) < Date.parse(fromDate.value)) {
            feedback.textContent = "La data iniziale non può precedere quella finale!";
            feedback.style.color = "#c32000";
            button.disabled = true;
        }
        else {
            feedback.textContent = "";
            feedback.style.color = "inherit";
            button.disabled = false;
        }
    } else if (!isAllEmpty) // Se c'è almeno un campo non vuoto e non sono inserite entrambe le date
        button.disabled = false;
}

/**
 * Questa funzione resetta il campo della professione {@param professione}.
 *
 * Non è sufficiente il comportamento automatico del browser perché, quando
 * si preme un bottone di reset, i campi "select" vengono riportati al campo
 * con l'attributo selected.
 * Questo attributo è però impostato all'ultimo valore usato per il filtraggio
 * dei risultati per evitare che l'utente debba reinserirlo ogni volta.
 * Dunque, al reset della form, il browser riporterebbe il valore del campo "select"
 * a quello che già è selezionato adesso (poiché contiene l'attributo selected)
 * e ciò non deve accadere.
 *
 * @param professione Campo di input che contiene la professione scelta al momento
 */
function resetProfession(professione) {
    /*
     * Rimuove l'attributo selected dalla professione attualmente impostata
     * per impostarlo all'opzione disabilitata che funge da placeholder
     */
    professione.querySelector('[selected]').removeAttribute('selected');
    professione.querySelector('[disabled]').toggleAttribute('selected', true);

    // Al momento del reset tutti i campi saranno vuoti => non può essere sottomessa la form
    document.querySelector("#filtra button.submit").disabled = true;
}