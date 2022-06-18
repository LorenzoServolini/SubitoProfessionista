<?php

/**
 * Classe per la validazione di campi di input (ricevuti dalle form)
 */
class Validation {

    /**
     * @var string $name Nome del campo di input
     */
    private $name = null;

    /**
     * @var string $value Valore ricevuto in input
     */
    private $value = null;

    /**
     * @var bool $numeric True se il campo è un numero, altrimenti false
     */
    private $numeric = false;

    /**
     * @var string $errors Errori che si sono verificati
     */
    private $errors = array();

    /**
     * Setta il nome del campo di input
     *
     * @param string $name Nome del campo di input
     *
     * @return $this Oggetto {@link Validation}
     */
    public function name($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Setta il valore passato in input
     *
     * @param string|array $value Valore passato in input
     *
     * @return $this Oggetto {@link Validation}
     */
    public function value($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Pattern da applicare al riconoscimento dell'espressione regolare
     *
     * @param string $pattern Nome del pattern da applicare
     *
     * @return $this Oggetto {@link Validation}
     */
    public function pattern($pattern) {
        if($pattern === 'date') {
            if(!self::isExistingDate($this->value))
                $this->errors[] = 'La ' . $this->name . ' inserita non rispetta il formato "anno-mese-giorno"';
        }
        else {
            if(preg_match(self::buildRegex($pattern), $this->value) !== 1)
                $this->errors[] = 'Formato del campo \'' . $this->name . '\' non rispettato';
        }

        return $this;
    }

    /**
     * Espressione regolare personalizzata
     *
     * @param string $regex Regex da applicare
     *
     * @return $this Oggetto {@link Validation}
     */
    public function customRegex($regex) {
        if(!preg_match($regex, $this->value))
            $this->errors[] = 'Formato del campo \'' . $this->name . '\' non rispettato';

        return $this;
    }

    /**
     * Imposta il campo come numerico e controlla che sia un numero
     *
     * @return $this Oggetto {@link Validation}
     */
    public function numeric() {
        $this->numeric = true;

        if(!is_numeric($this->value))
            $this->errors[] = 'Formato del campo \'' . $this->name . '\' non rispettato';

        return $this;
    }

    /**
     * Controlla che il campo sia un array
     *
     * @return $this Oggetto {@link Validation}
     */
    public function vectorial() {
        if(!is_array($this->value))
            $this->errors[] = 'Formato del campo \'' . $this->name . '\' non rispettato';

        return $this;
    }

    /**
     * Valore minimo e massimo del campo in input
     *
     * @param int $min Significato che dipende dal campo in input:
     * - Se il campo in input è numerico: valore minimo accettato
     * - Se il campo in input è una stringa: lunghezza minima della stringa
     * - Se il campo in input è una data: anno minimo permesso
     * @param int $max Significato che dipende dal campo in input:
     * - Se il campo in input è numerico: valore massimo accettato
     * - Se il campo in input è una stringa: lunghezza massima della stringa
     * - Se il campo in input è una data: timestamp corrispondente alla data massima
     *
     * @return $this Oggetto {@link Validation}
     */
    public function range($min, $max) {
        if($this->numeric) // Se è un numero
        {
            if($this->value < $min || $this->value > $max)
                $this->errors[] = 'Formato del campo \'' . $this->name . '\' non rispettato';
        }
        elseif(self::isDate($this->value)) // Se è una data
        {
            /*
             * Con strtotime(), usato più avanti per il valore di massimo, gli anni < 1902 vengono
             * mappati in modo non prevedibile a seconda degli input forniti.
             * Esempi:
             * con strtotime(60-06-28): si ottiene la data "01 gennaio 1970"
             * con strtotime(30-06-28): si ottiene la data "28 giugno 2030"
             * con strtotime(26-05-1901): si ottiene la data "01 gennaio 1970"
             * con strtotime(26-05-1902): si ottiene la data corretta "26 maggio 1902"
             *
             * Per evitare di ottenere un anno valido (es. 2018) anche nei casi in cui non lo è,
             * si impedisce l'uso di anni < 1902
             */
            if($min < 1902)
                $min = 1902;


            $year = (int) explode('-', $this->value)[0];
            if ($year < $min)
                $this->errors[] = 'La ' . $this->name . ' non può essere inferiore al 1 gennaio ' . $min;

            /*
             * Il controllo "strtotime($this->value) === false" è necessario per assicurarsi che non sia
             * stato inserito un anno troppo grande. In questo caso però, a differenza del caso di sopra in cui
             * l'anno è troppo piccolo, non si ha un "traboccamento" ma semplicemente ci viene restituito
             * false, poiché i long non bastano a rappresentare il timestamp di quell'anno troppo grande
             */
            if (strtotime($this->value) === false || strtotime($this->value) > $max)
                $this->errors[] = 'La ' . $this->name . ' non può superare il ' . date('Y-m-d', $max);
        }
        elseif(is_array($this->value)) // Se è un array
        {
            if(count($this->value) < $min || count($this->value) > $max)
                $this->errors[] = 'Nel campo \'' . $this->name . '\' non è stato inserito il numero corretto di elementi';
        }
        else // Se è una stringa
        {
            if(strlen($this->value) < $min || strlen($this->value) > $max)
                $this->errors[] = 'Lunghezza del campo \'' . $this->name . '\' non rispettata';
        }

        return $this;
    }

    /**
     * Controlla l'uguaglianza del campo con un valore specificato
     *
     * @param string $value Valore con cui controllare l'uguaglianza
     *
     * @return $this Oggetto {@link Validation}
     */
    public function equal($value) {
        if(strcmp($this->value, $value) !== 0)
            $this->errors[] = 'Il valore del campo \'' . $this->name . '\' non corrisponde';

        return $this;
    }

    /**
     * Restituisce true se la validazione dei campi non ha generato errori, altrimenti false
     *
     * @return boolean True se la validazione dei campi è andata a buon fine, altrimenti false
     */
    public function isSuccess() {
        return empty($this->errors);
    }

    /**
     * Restituisce il primo errore che si è verificato durante la validazione.
     * Se non ci sono errori, restituisce null.
     *
     * @return string|null Primo errore della validazione se presente, altrimenti null
     */
    public function getError(){
        return $this->isSuccess() ? null : $this->errors[0];
    }

    /**
     * @var string[] $patterns Elenco di possibili pattern usati per il matching
     */
    private static $patterns = array(
        'date_ymd'      => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
        'email'         => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}'
    );

    /**
     * Purifica la stringa per prevenire attacchi XSS
     *
     * @param string $string Stringa da filtrare
     *
     * @return string Stringa sanificata
     */
    public static function purify($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * Verifica se il valore specificato è una data nel formato YYYY-mm-dd
     *
     * @param string $value Valore da validare
     *
     * @return boolean True se il valore specificato è una data nel formato YYYY-mm-dd, altrimenti false
     */
    public static function isDate($value) {
        return preg_match(self::buildRegex('date_ymd'), $value) === 1;
    }

    /**
     * Verifica se il valore specificato è una data (nel formato YYYY-mm-dd) valida.
     * Ad esempio, la data 2020-50-40 non è una data valida.
     *
     * @param string $value Valore da validare
     *
     * @return boolean True se il valore specificato è una data (nel formato YYYY-mm-dd) esistente, altrimenti false
     */
    public static function isExistingDate($value) {
        if(!self::isDate($value))
            return false;

        $array = explode('-', $value); // array: [0] => anno, [1] => mese, [2] => giorno

        return count($array) === 3 && checkdate($array[1], $array[2], $array[0]);
    }

    /**
     * Trasforma un pattern in un'espressione regolare
     *
     * @param string $pattern Pattern da trasformare in regex
     *
     * @return string Espressione regolare
     */
    public static function buildRegex($pattern) {
        return ('/^'. self::$patterns[$pattern] .'$/');
    }
}