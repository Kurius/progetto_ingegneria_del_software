<?php

require_once 'CarrieraLaureando.php'; // Importa la classe base CarrieraLaureando

class CarrieraLaureandoInformatica extends CarrieraLaureando
{
    public bool $bonus; // Indica se lo studente ha diritto al bonus di rimozione del voto più basso
    public float $mediaInformatica; // Media pesata calcolata solo sugli esami informatici

    public function __construct($matricola, $cdl, $dataLaurea)
    {
        parent::__construct($matricola, $cdl, $dataLaurea); // Richiama il costruttore della classe base

        // Verifica se lo studente si è laureato entro 3.6 anni dall'iscrizione (bonus per laurea rapida)
        if ($this->dataLaurea - 3.6 * 365 * 86400 < $this->dataIscrizione) {
            $this->bonus = true;
        } else {
            $this->bonus = false;
        }

        // Se lo studente ha diritto al bonus, rimuove il voto più basso dalla media
        if ($this->bonus) {
            $this->togliVotoPiuBasso();
        }

        // Ora che l'eventuale esame con voto più basso è stato rimosso, si calcolano le medie
        $result = $this->calcolaMedia();
        $this->cfuMedia = $result['cfu']; // Assegna i CFU utilizzati per il calcolo della media
        $this->mediaPesata = $result['media']; // Assegna la media pesata complessiva
        $this->mediaInformatica = $this->calcolaMediaInf(); // Calcola la media pesata solo sugli esami informatici
    }

    /**
     * Rimuove l'esame con il voto più basso per migliorare la media pesata
     */
    private function togliVotoPiuBasso(): void
    {
        $minVoto = null; // Memorizza il voto più basso trovato
        $maxCfu = null; // Memorizza il numero massimo di CFU tra gli esami con voto più basso
        $esameMin = null; // Nome dell'esame da rimuovere

        // Cerca l'esame con il voto più basso tra quelli che fanno media
        foreach ($this->esami as $esame) {
            if (!$esame->media) {
                continue; // Salta gli esami che non fanno media
            }

            // Si seleziona l'esame con il voto più basso, considerando anche il suo peso in CFU
            if ($minVoto === null || $esame->voto < $minVoto || ($esame->voto == $minVoto && $esame->cfu > $maxCfu)) {
                $minVoto = $esame->voto;
                $maxCfu = $esame->cfu;
                $esameMin = $esame->nome;
            }
        }

        // Rimuove l'esame dalla lista di quelli che fanno media
        foreach ($this->esami as $esame) {
            if ($esame->nome == $esameMin) {
                $esame->media = false;
                break; // Termina il ciclo dopo aver trovato e rimosso l'esame
            }
        }
    }

    /**
     * Calcola la media pesata considerando solo gli esami informatici
     */
    private function calcolaMediaInf(): float
    {
        $tot = 0; // Totale dei voti ponderati per i CFU
        $cfu = 0; // Totale dei CFU degli esami informatici

        foreach ($this->esami as $esame) {
            if (!$esame->media || !$esame->inf) {
                continue; // Salta gli esami che non fanno media o che non sono informatici
            }

            $tot += $esame->voto * $esame->cfu; // Somma il voto pesato per i CFU
            $cfu += $esame->cfu; // Somma i CFU degli esami informatici
        }

        if ($cfu == 0) {
            return 0; // Se non ci sono esami informatici validi, la media è 0
        }

        return $tot / $cfu; // Restituisce la media pesata
    }
}
