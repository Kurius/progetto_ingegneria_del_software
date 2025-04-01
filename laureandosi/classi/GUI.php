<?php
// Includo i file necessari al funzionamento della classe
require_once "ProspettoPDFCommissione.php";
require_once "GestoreEmail.php";
require_once "GestioneCarrieraStudente.php";

$carriere = [];

class GUI //dichiaro la classe
{
    private array $matricole; // dichiaro le proprietà della classe
    private string $cdl;
    private string $dataLaurea;

    public function __construct(string $matricole, string $cdl, string $dataLaurea)
    {
        $this->matricole = preg_split('/\s+/', $matricole); //creo vettore, preg split serve per a capo
        $this->cdl = $cdl;
        $this->dataLaurea = $dataLaurea;
    }

    public function controlloMatricole(): bool
    {
        if (empty($this->matricole)) {
            return false; // Nessuna matricola fornita
        }

        $cdl = null; //aggiornerò cdl con il primo cdl trovato per poi comparare con gli altri

        foreach ($this->matricole as $m) {
            $esami = GestioneCarrieraStudente::prelevaCarriera($m);

            if (empty($esami)) {
                error_log("Matricola $m non trovata o senza carriera."); // Debug logs -> php -> error.log
                return false;
            }

            $corsoDiLaurea = $esami[0]['CORSO'] ?? null;

            if ($corsoDiLaurea === null) {
                error_log("Matricola $m ha dati incompleti.");
                return false;
            }

            if ($cdl === null) {
                $cdl = $corsoDiLaurea;
            } elseif ($cdl !== $corsoDiLaurea) {
                error_log("Matricola $m appartiene a un corso diverso ($corsoDiLaurea invece di $cdl).");
                return false;
            }
        }

        return true;
    }



    public function generaProspetti(): void
    {
        new ProspettoPDFCommissione($this->matricole, $this->cdl, $this->dataLaurea);
    }

    public function apriProspetti(): string
    {
        return str_replace(
            " ",
            "%20", // sostituisco gli spazi con %20 (rappresentazione di " " in URL)
            site_url("/wp-content/themes/laureandosi/prospetti/$this->cdl/commissione_prospetto.pdf")
        );
    }

    public function inviaProspetti(): void
    {
        new GestoreEmail($this->cdl);
    }
}