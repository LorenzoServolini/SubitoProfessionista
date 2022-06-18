<?php

require_once __DIR__ . "/database-manager.php";

class MultiPage {

    /* Query da dividere per pagine */
    private $select;
    private $from;
    private $where;
    private $group;
    private $order;
    private $param;

    /* Gestione paginazione */
    private $itemsPerPage;
    private $totalItems; // Numero totale di elementi
    private $pages; // Numero totale di pagine
    private $page; // Pagina selezionata da mostrare, letta da $_GET['page']

    /**
     * @param string $select Colonne da selezionare con la clausola SELECT
     * @param string $from Contenuto della clausola FROM
     * @param string $where Clausole WHERE da applicare alla query
     * @param string|null $group Clausola GROUP BY da applicare alla query
     * @param string $order Clausola ORDER BY da applicare alla query
     * @param array $param Parametri da sostituire nella prepared query (al posto dei '?')
     * @param int $itemsPerPage Numero di elementi da mostrare per ciascuna pagina
     */
    public function __construct($select, $from, $where, $group, $order, $param, $itemsPerPage) {

        /* Query */
        $this->select = $select;
        $this->from = $from;
        $this->where = $where;
        if($group !== null && $group !== '') $this->group = $group;
        $this->order = $order;
        $this->param = $param;


        /* Paginazione */
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = $this->countRows();
        $this->pages = (int) ceil($this->totalItems / $this->itemsPerPage);

        /*
         * $page = pagina da mostrare
         *
         * Se $_GET['page'] contiene:
         * 1) un numero di pagina troppo grande => $page = ultima pagina ($pages)
         * 2) un formato errato (che viene filtrato da filter_input) => $page = prima pagina (1)
         */
        $this->page = min($this->pages,
            /*
             * Restituisce il valore di default se $_GET['page']:
             * 1) Non esiste (non è stato passato come parametro)
             * 2) Non è un numero
             * 3) È un numero inferiore al valore minimo (min_range)
             */
            filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
                'options' => array(
                    'default'   => 1,
                    'min_range' => 1
                )
            )));
    }

    /**
     * Costruisce la query in base ai parametri passati (from, where e param)
     * per contare il numero di risultati totali.
     *
     * @return int Restituisce il numero di risultati ottenuti
     */
    private function countRows() {
        global $database;

        if(isset($this->group)) // Se è presente la clausola GROUP BY
            $query = "SELECT COUNT(*) AS totale FROM (SELECT COUNT(*) FROM {$this->from} WHERE {$this->where} GROUP BY {$this->group}) AS t1;";
        else
            $query = "SELECT COUNT(*) AS totale FROM {$this->from} WHERE {$this->where};";

        $stmt = $database->prepared_query($query, array('s' => $this->param));
        $result = $database->getResult($stmt);

        return $database->fetch($result, 'totale');
    }

    /**
     * @return bool True se il result set è vuoto, altrimenti false
     */
    public function isEmpty() {
        return $this->totalItems === 0;
    }

    /**
     * Costruisce la query in base ai parametri passati (select, from, where...).
     * Restituisce i risultati appartenenti alla pagina specificata nel parametro GET 'page' ($_GET['page'])
     * oppure {@link null} se il result set della query costruita è vuoto.
     *
     * @return mysqli_result|null Restituisce i risultati appartenenti alla pagina $_GET['page']
     * oppure null se non ci sono elementi
     */
    public function getCurrentPageItems() {
        if($this->isEmpty())
            return null;

        global $database;

        $offset = ($this->page - 1) * $this->itemsPerPage; // Calcola l'offset per la query (clausola OFFSET)

        // Query per mostrare i risultati relativi alla pagina specificata
        $query = "SELECT {$this->select} FROM {$this->from} WHERE {$this->where}";
        if(isset($this->group))
            $query .= " GROUP BY {$this->group}";
        $query .= " ORDER BY {$this->order}";
        $query .= " LIMIT {$this->itemsPerPage}"; // Limita il numero di elementi visibili per pagina
        $query .= " OFFSET {$offset};"; // Offset a partire dal quale gli elementi verranno mostrati

        return $database->getResult($database->prepared_query($query, array('s' => $this->param)));
    }

    /**
     * Costruisce il codice HTML della barra di navigazione delle pagine
     *
     * @param string $href URL base da inserire nei pulsanti che permettono lo spostamento tra le varie pagine
     *
     * @return string|null Restituisce il codice HTML della barra che permette la navigazione tra le diverse pagine
     * esistenti oppure null se non ci sono pagine da mostrare (cioè il result set della query costruita è vuoto)
     */
    public function toHtml($href) {
        if($this->isEmpty())
            return null;

        // Link per la prima pagina
        $prevLinks = "<a" . ($this->page <= 1 ? ' class="disabled"' : '') . " href=\"{$href}" . '&page=1" title="Prima pagina">&laquo;</a>';

        // Link per la pagina precedente
        $prevLinks .= " <a" . ($this->page <= 1 ? ' class="disabled"' : '') . " href=\"{$href}&page=" . ($this->page - 1) . '" title="Pagina precedente">&lsaquo;</a>';

        // Link per la pagina successiva
        $nextLinks = "<a" . ($this->page >= $this->pages ? ' class="disabled"' : '') . " href=\"{$href}&page=" . ($this->page + 1) . '" title="Prossima pagina">&rsaquo;</a>';

        // Link per l'ultima pagina
        $nextLinks .= " <a" . ($this->page >= $this->pages ? ' class="disabled"' : '') . " href=\"{$href}&page={$this->pages}" . '" title="Ultima pagina">&raquo;</a>';


        // HTML della barra di navigazione contenete i pulsanti per spostarsi tra le pagine
        return "<div id=\"paging\"><p> {$prevLinks} Pagina {$this->page} di {$this->pages} {$nextLinks}</p></div>";
    }
}