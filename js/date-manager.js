/**
 * Aggiunge la data odierna all'attributo "max" e all'attributo "title"
 * dell'elemento passato come parametro.
 * L'attributo "max" serve per evitare che venga inserita una data
 * futura mentre l'attributo "title" contiene la specifica visibile all'utente
 * "Data massima: <data odierna>" (dove <data odierna> è inserita in questa funzione).
 *
 * Se il parametro value è true la data odierna verrà impostata
 * anche all'attributo "value" dell'elemento specificato (in pratica
 * il campo di input avrà al suo interno la data di oggi).
 *
 * @param campo Campo di input a cui impostare gli attributi
 * @param value True se si vuole impostare anche l'attributo "value", false altrimenti
 */
function addCurrentDate(campo, value = false) {
    const date = new Date();
    let dd = date.getDate();
    let mm = date.getMonth() + 1;
    if(dd < 10)
        dd = '0' + dd
    if(mm < 10)
        mm = '0' + mm

    // L'attributo "max" e "value" richiedono un formato specifico per le date (YYYY-mm-dd)
    campo.setAttribute("max", date.getFullYear() + '-' + mm + '-' + dd);
    if(value)
        campo.setAttribute("value", date.getFullYear() + '-' + mm + '-' + dd);

    // Aggiunge al title la data nel formato italiano (dd/mm/YYYY)
    campo.setAttribute("title", campo.getAttribute("title") + (dd + '/' + mm + '/' + date.getFullYear()));
}