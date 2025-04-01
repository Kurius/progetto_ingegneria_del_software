<?php

require_once __DIR__ . "/../lib/fpdf184/fpdf.php";
require_once 'CarrieraLaureando.php';
require_once 'CarrieraLaureandoInformatica.php';
require_once 'FileConfigurazione.php';

class ProspettoPDFLaureando
{
    public string $matricola; // Matricola dello studente
    public CarrieraLaureando $carriera; // Oggetto contenente la carriera dello studente
    private string $cdl; // Corso di laurea
    private string $dataLaurea; // Data della laurea

    public function __construct(string $matricola, string $cdl, string $dataLaurea)
    {
        global $carriere;

        $this->matricola = $matricola;
        $this->cdl = $cdl;
        $this->dataLaurea = $dataLaurea;

        // Istanzia il giusto tipo di carriera in base al corso di laurea
        $this->carriera = ($cdl == "T. Ing. Informatica")
            ? new CarrieraLaureandoInformatica($this->matricola, $this->cdl, $this->dataLaurea)
            : new CarrieraLaureando($this->matricola, $this->cdl, $this->dataLaurea);

        // Aggiunge la carriera all'array globale e alla sessione
        $carriere[] = $this->carriera;
        $_SESSION['carriere'] = $carriere;

        // Creazione e salvataggio del PDF
        $pdf = new FPDF();
        $this->costruisciPdf($pdf);

        // Creazione della cartella se non esiste
        $dirPath = __DIR__ . "/../prospetti/$this->cdl";
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true); // true permette la creazione ricorsiva
        }

        $pdf->Output("$dirPath/{$this->matricola}_prospetto.pdf", "F");
    }

    // Metodo per costruire il PDF con i dati della carriera
    public function costruisciPdf(FPDF $pdf): void
    {
        $info = $this->cdl == "T. Ing. Informatica"; // Flag per distinguere il corso di laurea

        $pdf->SetMargins(11, 8);
        $pdf->AddPage();
        $pdf->SetFont("Arial", "", 13);

        // Intestazione del documento
        $pdf->Cell(0, 6, $this->cdl, 0, 1, "C");
        $pdf->Cell(0, 6, "CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA", 0, 1, "C");
        $pdf->Ln(3);

        // Stampa dei dati anagrafici
        $pdf->SetFontSize(9);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - 22, $info ? 33 : 27.5);
        $pdf->Cell(45, 5.5, "Matricola:", 0, 0);
        $pdf->Cell(0, 5.5, $this->matricola, 0, 1);
        $pdf->Cell(45, 5.5, "Nome:", 0, 0);
        $pdf->Cell(0, 5.5, $this->carriera->nome, 0, 1);
        $pdf->Cell(45, 5.5, "Cognome:", 0, 0);
        $pdf->Cell(0, 5.5, $this->carriera->cognome, 0, 1);
        $pdf->Cell(45, 5.5, "Email:", 0, 0);
        $pdf->Cell(0, 5.5, $this->carriera->email, 0, 1);
        $pdf->Cell(45, 5.5, "Data:", 0, 0);
        $pdf->Cell(0, 5.5, $this->dataLaurea, 0, 1);

        if ($info) { // Solo per Ingegneria Informatica
            $pdf->Cell(45, 5.5, "Bonus:", 0, 0);
            $pdf->Cell(0, 5.5, $this->carriera->bonus ? "SI" : "NO", 0, 1);
        }
        $pdf->Ln(3);

        //stampa della tabella degli esami
        $pdf->Cell($pdf->GetPageWidth() - 22 - ($info ? 44 : 33), 5.5, "ESAME", 1, 0, "C");
        $pdf->Cell(11, 5.5, "CFU", 1, 0, "C");
        $pdf->Cell(11, 5.5, "VOT", 1, 0, "C");
        $pdf->Cell(11, 5.5, "MED", 1, 0, "C");
        //aggiunta della colonna relativa agli esami informatici
        if ($info) {
            $pdf->Cell(11, 5.5, "INF", 1, 0, "C");
        }
        $pdf->Ln();
        $pdf->SetFontSize(8);
        foreach ($this->carriera->esami as $esame) {
            $pdf->SetFillColor(255, 255, 255);
            if ($esame->sovran) {
                $pdf->SetFillColor(255, 255, 0); // Giallo
            }
            $pdf->Cell($pdf->GetPageWidth() - 22 - ($info ? 44 : 33), 4.5, $esame->nome, 1, 0);
            $pdf->Cell(11, 4.5, $esame->cfu, 1, 0, "C");
            $pdf->Cell(11, 4.5, $esame->voto, 1, 0, "C");
            $pdf->Cell(11, 4.5, $esame->media ? "X" : "", 1, 0, "C");
            if ($info) {
                $pdf->Cell(11, 4.5, $esame->inf ? "X" : "", 1, 0, "C");
            }
            $pdf->Ln();

        }
        $pdf->Ln(3);

        //stampa dei dati di carriera
        $pdf->SetFontSize(9);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - 22, $info ? 33 : 22);
        $pdf->Cell(80, 5.5, "Media Pesata (M):", 0, 0);
        $pdf->Cell(0, 5.5, round($this->carriera->mediaPesata, 3), 0, 1);
        $pdf->Cell(80, 5.5, "Crediti che fanno media (CFU):", 0, 0);
        $pdf->Cell(0, 5.5, $this->carriera->cfuMedia, 0, 1);
        $pdf->Cell(80, 5.5, "Crediti curriculari conseguiti:", 0, 0);
        $pdf->Cell(
            0,
            5.5,
            $this->carriera->calcolaCfuCurricolari()."/".FileConfigurazione::getCfuCurricolari($this->cdl),
            0,
            1
        );
        if ($info) {
            $pdf->Cell(80, 5.5, "Voto di tesi (T):", 0, 0);
            $pdf->Cell(0, 5.5, 0, 0, 1);
        }
        $pdf->Cell(80, 5.5, "Formula calcolo voto di laurea:", 0, 0);
        $pdf->Cell(0, 5.5, FileConfigurazione::getFormulaVoto($this->cdl), 0, 1);
        //stampa media degli esami informatici
        if ($info) {
            $pdf->Cell(80, 5.5, "Media pesata esami INF:", 0, 0);
            $pdf->Cell(0, 5.5, round($this->carriera->mediaInformatica, 3), 0, 1);
        }
    }

    public function aggiungiTabella(FPDF $pdf): void
    {
        $pdf->Ln(3);
        $pdf->SetFontSize(9);
        $pdf->Cell(($pdf->GetPageWidth() - 22), 5.5, "SIMULAZIONE DI VOTO DI LAUREA", 1, 1, "C");

        //gestione della formula
        $formulaVoto = FileConfigurazione::getFormulaVoto($this->cdl);
        $formulaVoto = str_replace('CFU', "A", $formulaVoto);
        $formulaVoto = str_replace(["M", 'T', 'A', 'C'], ['$M', '$T', '$A', '$C'], $formulaVoto);
        $param = FileConfigurazione::getInfoParametro($this->cdl);
        error_log("Valore di param: ".print_r($param, true));
        $nCell = (int)(($param["max"] - $param["min"]) / $param["step"] + 1);
        $M = $this->carriera->mediaPesata;
        $A = FileConfigurazione::getCfuCurricolari($this->cdl);
        $C = 0;
        $T = 0;

        //simulazioni: meno di 10 celle
        if ($nCell <= 10) {
            $pdf->Cell(
                ($pdf->GetPageWidth() - 22) / 2,
                5.5,
                $param["param"] == "T" ? "VOTO TESI" : "VOTO COMMISSIONE",
                1,
                0,
                "C"
            );
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 2, 5.5, "VOTO DI LAUREA", 1, 1, "C");
            for ($i = $param["min"]; $i <= $param["max"]; $i += $param["step"]) {
                if ($param["param"] == "T") {
                    $T = $i;
                } else {
                    $C = $i;
                }
                eval("\$voto = $formulaVoto;");

                $pdf->Cell(($pdf->GetPageWidth() - 22) / 2, 5.5, $i, 1, 0, "C");
                $pdf->Cell(($pdf->GetPageWidth() - 22) / 2, 5.5, round($voto, 3), 1, 1, "C");
            }
        } else { //simulazioni con oltre 10 celle
            $pdf->Cell(
                ($pdf->GetPageWidth() - 22) / 4,
                5.5,
                $param["param"] == "T" ? "VOTO TESI" : "VOTO COMMISSIONE",
                1,
                0,
                "C"
            );
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 5.5, "VOTO DI LAUREA", 1, 0, "C");
            $pdf->Cell(
                ($pdf->GetPageWidth() - 22) / 4,
                5.5,
                $param["param"] == "T" ? "VOTO TESI" : "VOTO COMMISSIONE",
                1,
                0,
                "C"
            );
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 5.5, "VOTO DI LAUREA", 1, 1, "C");
            $even = 0;
            for ($i = 0; $i < $nCell; $i++) {
                //colonna sinistra
                if ($even == 0) {
                    $val = $param["min"] + $param["step"] * ($i / 2);
                } //colonna destra, si aggiunge una costante
                else {
                    $val = $param["min"] + $param["step"] * (ceil($nCell / 2) + ($i - 1) / 2);
                } //ceil arrotonda in eccesso
                if ($param["param"] == "T") {
                    $T = $val;
                } else {
                    $C = $val;
                }
                eval("\$voto = $formulaVoto;"); //eval() esegue codice PHP: se formulaVoto fosse $T + 2 ==> $voto = $T + 2;
                $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 5.5, $val, 1, 0, "C");
                $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 5.5, round($voto, 3), 1, $even || ($i == $nCell - 1), "C");
                $even = $even == 0 ? 1 : 0;
            }
        }
        $pdf->Ln(3);
        $pdf->MultiCell(0, 4, "VOTO DI LAUREA FINALE: ".FileConfigurazione::getMessaggioProspetto($this->cdl));
    }
}