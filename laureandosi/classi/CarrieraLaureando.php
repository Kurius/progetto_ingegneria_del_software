<?php

require_once 'GestioneCarrieraStudente.php'; // Importa la classe per la gestione della carriera dello studente
require_once 'FileConfigurazione.php'; // Importa la classe per la gestione della configurazione degli esami
require_once 'EsameLaureando.php'; // Importa la classe EsameLaureando

class CarrieraLaureando
{
    public string $nome; // Nome dello studente
    public string $cognome; // Cognome dello studente
    public string $matricola; // Matricola dello studente
    public string $email; // Email istituzionale dello studente
    public ?int $dataIscrizione = null; // Data di iscrizione (utile per il bonus di Ingegneria Informatica)
    public string $dataLaurea; // Data della laurea
    public array $esami = []; // Array contenente gli esami sostenuti dal laureando
    public int $cfuMedia; // Somma dei CFU degli esami che concorrono alla media pesata
    public float $mediaPesata; // Media pesata dello studente

    public function __construct(string $matricola, string $cdl, string $dataLaurea)
    {
        $this->matricola = $matricola;
        $this->dataLaurea = strtotime($dataLaurea); // Converte la data della laurea in timestamp

        // Recupera i dati anagrafici dello studente
        $anagrafica = GestioneCarrieraStudente::prelevaAnagrafica($matricola);
        $esami = GestioneCarrieraStudente::prelevaCarriera($matricola);

        // Assegna le informazioni anagrafiche
        $this->nome = $anagrafica["nome"];
        $this->cognome = $anagrafica["cognome"];
        $this->email = $anagrafica["email_ate"];

        // Recupera l'elenco degli esami da escludere (filtrati tramite configurazione esterna)
        $esamiNonCarriera = FileConfigurazione::getEsamiNonCarriera($cdl, $matricola);

        foreach ($esami as $esame) {
            // Se non è ancora stata assegnata, imposta la data di iscrizione come la data del primo esame sostenuto
            if ($this->dataIscrizione === null) {
                $this->dataIscrizione = strtotime(str_replace("/", "-", $esame["INIZIO_CARRIERA"]));
            }

            // Filtra gli esami sovrannumerari e quelli presenti nei filtri di configurazione
            if (!is_string($esame["DES"]) || !is_int($esame["PESO"]) || in_array($esame["DES"], $esamiNonCarriera)) {
                continue;
            }

            // Aggiunge l'esame alla lista degli esami del laureando
            $this->esami[] = new EsameLaureando($esame, $cdl, $matricola);
        }

        // Ordina gli esami in base alla data di superamento (dal più vecchio al più recente)
        usort($this->esami, function ($e1, $e2) {
            return $e1->dataSuperamento > $e2->dataSuperamento;
        });

        // Se il corso di laurea non è "T. Ing. Informatica", calcola subito la media pesata
        // (per Ingegneria Informatica la media viene calcolata dopo la verifica del bonus)
        if ($cdl !== "T. Ing. Informatica") {
            $result = $this->calcolaMedia();
            $this->cfuMedia = $result['cfu']; // CFU validi per la media
            $this->mediaPesata = $result['media']; // Media pesata
        }
    }

    /**
     * Calcola la media pesata degli esami sostenuti dallo studente.
     * Restituisce un array con la media pesata e il totale dei CFU validi.
     */
    protected function calcolaMedia(): array
    {
        $tot = 0; // Totale dei voti pesati per i CFU
        $cfu = 0; // Totale dei CFU che concorrono alla media

        foreach ($this->esami as $esame) {
            if (!$esame->media) {
                continue; // Ignora gli esami che non fanno media
            }

            $tot += $esame->voto * $esame->cfu; // Somma il voto pesato per i CFU
            $cfu += $esame->cfu; // Somma i CFU validi
        }

        return [
            'media' => $tot / $cfu, // Calcola la media pesata
            'cfu' => $cfu, // Restituisce il totale dei CFU
        ];
    }

    /**
     * Calcola il totale dei CFU curricolari sostenuti dallo studente.
     */
    public function calcolaCfuCurricolari(): int
    {
        $cfu = 0;
        foreach ($this->esami as $esame) {
            $cfu += $esame->cfu; // Somma tutti i CFU degli esami sostenuti
        }

        return $cfu; // Restituisce il totale dei CFU curricolari
    }
}
