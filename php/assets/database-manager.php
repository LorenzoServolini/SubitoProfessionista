<?php

/**
 * Classe per la gestione del database MySQL
 */
class DatabaseManager {

    private $host = "127.0.0.1";
    private $user = "root";
    private $password = "";
    private $database = "subitoprofessionista";
    private $connection = null;

    public function __construct() {
        $this->openConnection();
    }

    public function isOpened(){
        return ($this->connection !== NULL);
    }

    private function openConnection(){
        if(!$this->isOpened()){

            $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->database)
            or die ("Connessione al database fallita: (" .  mysqli_connect_errno() . ") " . mysqli_connect_error());
        }
    }

    public function closeConnection(){
        if($this->isOpened()){
            mysqli_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Esegue una prepared query e restituisce il relativo statement
     *
     * @param string $query Query da eseguire
     * @param array $params Array di parametri (da sostituire agli eventuali '?' nella query)
     *
     * @return mysqli_stmt Statement della query
     */
    public function prepared_query($query, $params) {
        if(!$this->isOpened())
            $this->openConnection();


        $stmt = $this->connection->prepare($query);
        if (!$stmt)
            die("prepare() fallita: (" . $this->connection->errno . ") " .$this->connection->error);

        $values = array();
        $types = '';
        foreach($params as $type => &$value) {

            // Se è un array significa che ci sono più parametri dello stesso tipo $type
            if(is_array($value)) {
                foreach($value as &$param) {
                   /*
                    * Si usa la '&' poiché bind_param() richiede che i parametri
                    * siano riferimenti [bind_param($types, &$var1, &...$vars)]
                    */
                    $values[] = &$param;
                    $types .= $type;
                }
            } else {
                /*
                 * Si usa la '&' poiché bind_param() richiede che i parametri
                 * siano riferimenti [bind_param($types, &$var1, &...$vars)]
                 */
                $values[] = &$value;
                $types .= $type;
            }
        }

        // Chiama bind_param() con un numero di parametri dinamico
        if (call_user_func_array(array($stmt, 'bind_param'), array_merge(array($types), $values)) === FALSE)
            die("bind_param() fallita: (" . $stmt->errno . ") " . $stmt->error);

        // Eseguo la query
        if (!$stmt->execute())
            die("execute() fallita: (" . $stmt->errno . ") " . $stmt->error);

        return $stmt;
    }

    /**
     * Restituisce il result set ottenuto dall'esecuzione di una query
     *
     * @param mysqli_stmt $stmt - Statement da cui prelevare il result set
     *
     * @return mysqli_result Result set
     */
    public function getResult($stmt) {
        $result = $stmt->get_result() or die("get_result() fallita: (" . $stmt->errno . ") " . $stmt->error);

        $this->closeStatement($stmt);

        return $result;
    }

    public function query($query) {
        if(!$this->isOpened())
            $this->openConnection();

        $result = $this->connection->query($query) or die("Query fallita: " . mysqli_error($this->connection));

        return $result;
    }

    /**
     * Libera lo memoria relativa al result set specificato
     *
     * @param mysqli_result $result Result set da chiudere
     */
    public function closeResult($result) {
        $result->close();
    }

    /**
     * Libera lo memoria associata allo statement specificato
     *
     * @param mysqli_stmt $stmt Prepared statement da chiudere
     */
    public function closeStatement($stmt) {
        $stmt->close();
    }

    /**
     * Esegue il fetch sul result set specificato
     *
     * @param mysqli_result $result Result set su cui eseguire il fetch
     * @param string $column Colonna di cui fare il fetch
     * @param string $type Tipologia di fetch
     *
     * @return mixed Restituisce i dati estratti dal result set
     */
    public function fetch($result, $column = '', $type = '') {
        if($column !== '') {
            switch($type) {
                case 'array':
                    $out_value = $result->fetch_array()[$column];
                    break;
                case 'row':
                    $out_value = $result->fetch_row()[$column];
                    break;
                default: // Default: $type = 'assoc'
                    $out_value = $result->fetch_assoc()[$column];
                    break;
            }

            $this->closeResult($result);
        }
        else {
            switch($type) {
                case 'array':
                    $out_value = $result->fetch_array();
                    break;
                case 'row':
                    $out_value = $result->fetch_row();
                    break;
                default: // Default: $type = 'assoc'
                    $out_value = $result->fetch_assoc();
                    break;
            }

            if($out_value === NULL)
                $this->closeResult($result);
        }

        return $out_value;
    }

    /**
     * Conta il numero di righe nel result set specificato
     *
     * @param mysqli_result $result Result set di cui contare le righe
     *
     * @return int Numero di righe del result set
     */
    public function countRows($result) {
        return $result->num_rows;
    }

    /**
     * Trasforma la colonna specificata in una stringa (concatena i risultati con un separatore)
     *
     * @param mysqli_result $result Result set contenente tutti i risultati
     * @param string $column Colonna da trasformare in stringa
     * @param string $separator Caratteri con cui separare i vari risultati presenti nella colonna
     *
     * @return string Stringa contenente tutti i valori presenti nella colonna all'interno del result set
     */
    public function columnToString($result, $column, $separator){
        $output = "";

        while($riga = $result->fetch_assoc())
            $output .= $riga[$column] . $separator;

        return $output;
    }

    /**
     * Controlla l'esistenza della professione specificata
     *
     * @param string $professione - Professione da controllare
     *
     * @return bool True se la professione esiste sul database, altrimenti false
     */
    public function existsProfession($professione) {
        if(!$this->isOpened())
            $this->openConnection();


        $stmt = $this->prepared_query("SELECT EXISTS(SELECT 1 FROM professione WHERE Nome = ?) AS trovato", array('s' => $professione));
        $result = $this->getResult($stmt);

        return (bool) $this->fetch($result, 'trovato');
    }

    /**
     * Controlla se l'email specificata è registrata a nome di un utente
     *
     * @param string $email - Email da controllare
     *
     * @return bool True se l'email è di un utente, altrimenti false
     */
    public function isUser($email) {
        if(!$this->isOpened())
            $this->openConnection();


        $sql = "SELECT EXISTS(SELECT 1 FROM utente WHERE Email = ?) AS found_user";
        $stmt = $this->prepared_query($sql, array('s' => $email));
        $result = $this->getResult($stmt);

        return (bool) $this->fetch($result, 'found_user');
    }

    /**
     * Controlla se l'email specificata è registrata a nome di un professionista
     *
     * @param string $email - Email da controllare
     *
     * @return bool True se l'email è di un professionista, altrimenti false
     */
    public function isProfessional($email) {
        if(!$this->isOpened())
            $this->openConnection();


        $sql = "SELECT EXISTS(SELECT 1 FROM professionista WHERE Email = ?) AS found";
        $stmt = $this->prepared_query($sql, array('s' => $email));
        $result = $this->getResult($stmt);

        return (bool) $this->fetch($result, 'found');
    }

    /**
     * Controlla se il proprietario dell'email specificata ha accettato di condividere lo storico
     *
     * @param string $email - Email del proprietario di cui controllare la condivisione dello storico
     * @param string $type - Tipo di proprietario: 'utente' o 'professionista'
     *
     * @return bool True se ha accettato di condividere lo storico, false altrimenti
     */
    public function hasSharedInfo($email, $type) {
        if(!$this->isOpened())
            $this->openConnection();

        switch ($type) {
            case 'utente':
                $sql = "SELECT CondivisioneStorico FROM utente WHERE Email = ?";
                break;

            default: // Default: professionista
                $sql = "SELECT CondivisioneStorico FROM professionista WHERE Email = ?";
                break;
        }

        $stmt = $this->prepared_query($sql, array('s' => $email));
        $result = $this->getResult($stmt);

        return $this->fetch($result, 'CondivisioneStorico') === 1;
    }

    /**
     * Controlla se l'email specificata è disponibile o è già registrata
     *
     * @param string $email - Email da controllare
     *
     * @return bool True se l'email è disponibile, altrimenti false
     */
    public function availableEmail($email) {
        return !$this->isUser($email) && !$this->isProfessional($email);
    }

    /**
     * Cerca nel database la password dell'utente specificato
     *
     * @param string $email - Email dell'utente di cui ottenere la password
     *
     * @return string|null La password dell'utente se viene trovata, altrimenti null
     */
    public function getUserPassword($email) {
        if(!$this->isOpened())
            $this->openConnection();


        $sql = "SELECT Password FROM utente WHERE Email = ?";
        $stmt = $this->prepared_query($sql, array('s' => $email));
        $result = $this->getResult($stmt);

        return $this->fetch($result, 'Password');
    }

    /**
     * Cerca nel database la password del professionista specificato
     *
     * @param string $email - Email del professionista di cui ottenere la password
     *
     * @return string|null La password del professionista se viene trovata, altrimenti null
     */
    public function getProfessionalPassword($email) {
        if(!$this->isOpened())
            $this->openConnection();


        $sql = "SELECT Password FROM professionista WHERE Email = ?";
        $stmt = $this->prepared_query($sql, array('s' => $email));
        $result = $this->getResult($stmt);

        return $this->fetch($result, 'Password');
    }

    /**
     * Cerca nel database le informazioni specificate di un professionista
     *
     * @param string $email - Email del professionista di cui ottenere le info
     * @param string $info - Colonne da reperire sul database (clausola SELECT)
     *
     * @return array|null Se viene trovato, le informazioni del professionista, altrimenti null
     */
    public function getProfessionalInfo($email, $info) {
        if(!$this->isOpened())
            $this->openConnection();


        $sql = "SELECT {$info} FROM professionista WHERE Email = ?";
        $stmt = $this->prepared_query($sql, array('s' => $email));
        $result = $this->getResult($stmt);
        $row = $this->fetch($result);

        if($row === null) // Professionista non trovato
            return null;
        else {
            $this->closeResult($result);

            return $row;
        }
    }
}

$database = new DatabaseManager();