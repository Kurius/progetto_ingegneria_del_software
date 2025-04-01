<?php

require_once __DIR__."/../lib/fpdf184/fpdf.php"; // Importazione della libreria FPDF per generare i PDF
require_once 'ProspettoPDFLaureando.php'; // Importazione della classe che genera i prospetti per singoli laureandi

class ProspettoPDFCommissione
{
    private array $matricole; // Array contenente le matricole degli studenti
    private array $prospettiLaureandi; // Array di oggetti ProspettoPDFLaureando
    private string $cdl; // Nome del corso di laurea
    private string $dataLaurea; // Data della laurea

    public function __construct(array $matricole, string $cdl, string $dataLaurea)
    {
        $this->matricole = $matricole; // Assegna le matricole
        $this->prospettiLaureandi = []; // Inizializza l'array dei prospetti
        $this->cdl = $cdl; // Assegna il corso di laurea
        $this->dataLaurea = $dataLaurea; // Assegna la data della laurea
        $this->genera(); // Avvia la generazione del PDF
    }

    private function genera(): void
    {
        // Genera i prospetti per ogni laureando
        foreach ($this->matricole as $m) {
            $prospettoLaureando = new ProspettoPDFLaureando($m, $this->cdl, $this->dataLaurea);
            $this->prospettiLaureandi[] = $prospettoLaureando; // Memorizza il prospetto nel vettore
        }

        $pdf = new FPDF(); // Crea un nuovo oggetto PDF
        $pdf->SetMargins(11, 8); // Imposta i margini del documento
        $pdf->AddPage(); // Aggiunge una nuova pagina
        $font = "Arial"; // Font da usare

        // Generazione della tabella dei laureandi
        $pdf->SetFont($font, "", 13);
        $pdf->Cell(0, 6, $this->cdl, 0, 1, "C"); // Titolo con il nome del corso di laurea
        $pdf->Cell(0, 6, "LISTA LAUREANDI", 0, 1, "C"); // Titolo lista laureandi
        $pdf->Ln(3);

        $pdf->SetFontSize(11);
        // Intestazione della tabella
        $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 7, "COGNOME", 1, 0, "C");
        $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 7, "NOME", 1, 0, "C");
        $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 7, "CDL", 1, 0, "C");
        $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 7, "VOTO LAUREA", 1, 1, "C");

        $pdf->SetFontSize(10);
        // Popolamento della tabella con i dati dei laureandi
        foreach ($this->prospettiLaureandi as $prospetto) {
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 6, $prospetto->carriera->cognome, 1, 0, "C");
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 6, $prospetto->carriera->nome, 1, 0, "C");
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 6, "", 1, 0, "C"); // Campo vuoto per eventuali informazioni aggiuntive
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 6, "/110", 1, 1, "C"); // Placeholder per il voto di laurea
        }

        // Aggiunta dei prospetti individuali e delle relative tabelle al PDF
        foreach ($this->prospettiLaureandi as $prospetto) {
            $prospetto->costruisciPdf($pdf); // Genera il prospetto completo del laureando
            $prospetto->aggiungiTabella($pdf); // Aggiunge la tabella con gli esami sostenuti
        }

        // La cartella del corso di laurea è già stata creata nella generazione del primo prospetto
        $pdf->Output(__DIR__."/../prospetti/$this->cdl/commissione_prospetto.pdf", "F"); // Salva il PDF finale
    }
}
