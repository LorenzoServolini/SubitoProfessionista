/**
 * Questa funzione si occupa di:
 *
 * 1) inizializzare i contatori per i due campi "Nome" e "Cognome"
 *
 * 2) controllare che venga riempito almeno un campo prima di poter eseguire
 *    un filtraggio dei risultati.
 *    In pratica si occupa di tenere abilitato il bottone di submit (della
 *    form per il filtraggio) solo quando almeno un campo di input è compilato.
 *
 * 3) resettare il campo delle province una volta premuto il tasto di reset.
 *    Ciò non avviene di default poiché il tasto di reset riporta il campo delle
 *    province (menù a selezione multipla) al valore iniziale, che era stato impostato
 *    alle ultime province inserite come filtro.
 */
function addFilterHandlers() {
    const form = document.getElementById("filtra");
    const name = document.getElementById("name");
    const surname = document.getElementById("surname");
    const provinces = document.getElementById("province");


    /* Aggiunge il contatore ai due campi per il filtraggio contenenti il nome e il cognome */
    initCounter(form, name);
    initCounter(form, surname);


    /*
     * Verifica che almeno un campo per il filtraggio dei risultati sia stato inserito
     * prima di abilitare di permette il filtraggio dei risultati (= invio della form)
     */
    provinces.addEventListener("input", function () { checkInput(provinces, name, surname); });
    name.addEventListener("input", function () { checkInput(provinces, name, surname); });
    surname.addEventListener("input", function () { checkInput(provinces, name, surname); });


    /*
     * Chiama checkInput() una prima volta al caricamento della pagina ricerca.php per
     * verificare se c'è già qualche campo non vuoto compilato correttamente (nel qual
     * caso il pulsante va abilitato - di default inizialmente è disabilitato).
     *
     * Si possono avere dei campi già non vuoti quando si è già eseguito un filtraggio (e i campi
     * sono riempiti automaticamente con i parametri precedentemente specificati) oppure se un
     * utente giunge alla pagina tramite un link con i parametri (GET) per il filtraggio già specificati
     */
    checkInput(provinces, name, surname);



    /* Resetta correttamente il campo per delle province e i due campi "Nome" e "Cognome" */
    form.addEventListener("reset", function () { resetFields(provinces, name, surname); });
}

/**
 * Mantiene disabilitato il pulsante per sottomettere la form finché non
 * viene riempito almeno uno dei campi per il filtraggio dei risultati.
 *
 * @param provinces Campo di input per le zone servite (province) dal professionista
 * @param name Campo di input per il nome del professionista
 * @param surname Campo di input per il cognome del professionista
 */
function checkInput(provinces, name, surname) {
    const button = document.querySelector("#filtra button.submit");

    // Controlla se tutti i campi di input sono vuoti
    const isAllEmpty = provinces.value === "" && name.value === "" && surname.value === "";

    // Se c'è almeno un campo non vuoto abilita il bottone, altrimenti mantienilo disabilitato
    button.disabled = isAllEmpty;
}

/**
 * Questa funzione resetta il menù delle province {@param provinces} e
 * i due campi di input {@param name} e {@param surname}.
 *
 * Non è sufficiente il comportamento automatico del browser perché, quando si preme
 * un bottone di reset, i campi "select" vengono riportati al campo con l'attributo
 * selected e i campi "input" vengono riportati al valore che l'attributo value aveva inizialmente.
 * Questi attributi sono stati impostati all'ultimo valore usato per il filtraggio
 * dei risultati per evitare che l'utente debba reinserirlo ogni volta.
 * Dunque, al reset della form, il browser riporterebbe il valore dei vari campi
 * a quello già presente adesso (lasciando quindi inalterata l'intera form).
 *
 * @param provinces Campo di input che contiene le province scelte al momento (nel filtraggio)
 * @param name Campo di input che contiene il nome usato per il filtraggio
 * @param surname Campo di input che contiene il cognome usato per il filtraggio
 */
function resetFields(provinces, name, surname) {
    /*
     * Rimuove l'attributo selected dalla professione attualmente impostata
     * per impostarlo all'opzione disabilitata che funge da placeholder
     */
    for (let i = 0; i < provinces.options.length; i++)
        provinces.options[i].removeAttribute("selected");

    name.setAttribute("value", "");
    surname.setAttribute("value", "");

    // Al momento del reset tutti i campi saranno vuoti => non può essere sottomessa la form
    document.querySelector("#filtra button.submit").disabled = true;
}