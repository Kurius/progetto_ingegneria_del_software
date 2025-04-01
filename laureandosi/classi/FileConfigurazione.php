<?php

class  FileConfigurazione
{
    private static $cdlData = null; // Contiene i dati relativi ai corsi di laurea
    private static $esamiInformatici = null; // Contiene gli esami di informatica
    private static $filtriData = null; // Contiene i filtri per gli esami da rimuovere o non considerare

    // Funzione che carica il JSON negli array della classe, restituendo un array vuoto in caso di errore
    private static function caricaJson(string $filePath): array
    {
        // Verifica se il file esiste
        if (!file_exists($filePath)) {
            return []; // Ritorna un array vuoto se il file non esiste
        }

        // Carica il contenuto del file JSON
        $json = file_get_contents($filePath);
        // Decodifica il JSON in un array associativo
        $data = json_decode($json, true);

        // Verifica che i dati decodificati siano un array
        return is_array($data) ? $data : []; // Ritorna l'array, altrimenti ritorna un array vuoto
    }

    // Inizializzazione dei dati dai file JSON se non sono già stati caricati
    private static function init(): void
    {
        // Carica i dati solo se non sono già stati caricati
        if (self::$cdlData === null) {
            // Carica i dati relativi ai corsi di laurea
            self::$cdlData = self::caricaJson(__DIR__."/../config/cdl.json");
            // Carica i dati degli esami di informatica
            self::$esamiInformatici = self::caricaJson(__DIR__."/../config/esami_inf.json");
            // Carica i filtri degli esami da escludere o non considerare
            self::$filtriData = self::caricaJson(__DIR__."/../config/filtri.json");
        }
    }

    /**
     * Restituisce l'elenco degli esami da rimuovere dalla carriera di uno studente.
     * Considera i filtri generali per il corso di laurea e quelli specifici per la matricola.
     */
    public static function getEsamiNonCarriera(string $cdl, string $matricola = null): array
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Recupera gli esami da rimuovere per il corso di laurea specificato
        $esamiDaTogliere = self::$filtriData[$cdl]["*"]["da_togliere"] ?? [];

        // Se è presente una matricola, aggiunge eventuali esami da rimuovere specifici per quella matricola
        if ($matricola && isset(self::$filtriData[$cdl][$matricola]["da_togliere"])) {
            $esamiDaTogliere = array_merge($esamiDaTogliere, self::$filtriData[$cdl][$matricola]["da_togliere"]);
        }

        // Restituisce gli esami da rimuovere come array
        return is_array($esamiDaTogliere) ? $esamiDaTogliere : [];
    }

    /**
     * Restituisce l'elenco degli esami che non devono essere presi in considerazione per il calcolo della media.
     * Considera i filtri generali per il corso di laurea e quelli specifici per la matricola.
     */
    public static function getEsamiNonMedia(string $cdl, string $matricola = null): array
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Recupera gli esami non considerati per la media per il corso di laurea specificato
        $esamiNonMedia = self::$filtriData[$cdl]["*"]["non_media"] ?? [];

        // Se è presente una matricola, aggiunge eventuali esami non considerati per la media specifici per quella matricola
        if ($matricola && isset(self::$filtriData[$cdl][$matricola]["non_media"])) {
            $esamiNonMedia = array_merge($esamiNonMedia, self::$filtriData[$cdl][$matricola]["non_media"]);
        }

        // Restituisce gli esami che non devono essere considerati per la media
        return is_array($esamiNonMedia) ? $esamiNonMedia : [];
    }

    // Restituisce gli esami di informatica configurati
    public static function getEsamiInformatici(): array
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Restituisce gli esami di informatica configurati
        return is_array(self::$esamiInformatici) ? self::$esamiInformatici : [];
    }

    // Restituisce il numero totale di CFU previsti per il corso di laurea specificato
    public static function getCfuCurricolari(string $cdl): int
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Restituisce i CFU totali per il corso di laurea
        return self::$cdlData[$cdl]["crediti_totali"] ?? 0;
    }

    // Restituisce il valore del voto di lode per il corso di laurea specificato
    public static function getValoreLode(string $cdl): int
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Restituisce il valore del voto di lode per il corso di laurea
        return self::$cdlData[$cdl]["valore_lode"] ?? 0;
    }

    // Restituisce la formula di calcolo del voto per il corso di laurea specificato
    public static function getFormulaVoto(string $cdl): string
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Restituisce la formula di calcolo del voto
        return self::$cdlData[$cdl]["formula"] ?? "";
    }

    // Restituisce le informazioni sul parametro di calcolo del voto per il corso di laurea specificato
    public static function getInfoParametro(string $cdl): array
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Restituisce le informazioni sul parametro di calcolo del voto
        return self::$cdlData[$cdl]["info_parametro"] ?? [];
    }

    // Restituisce il messaggio della commissione per il corso di laurea specificato
    public static function getMessaggioProspetto(string $cdl): string
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Restituisce il messaggio della commissione
        return self::$cdlData[$cdl]["msg_commissione"] ?? "Messaggio non disponibile.";
    }

    // Restituisce il corpo dell'email per il corso di laurea specificato
    public static function getEmailBody(string $cdl): string
    {
        // Inizializza i dati se non sono già stati caricati
        self::init();

        // Restituisce il corpo dell'email
        return self::$cdlData[$cdl]["corpo_email"] ?? "Corpo email non disponibile.";
    }
}
