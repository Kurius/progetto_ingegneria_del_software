<?php

require_once __DIR__."/classi/CarrieraLaureando.php";
require_once __DIR__ . "/classi/CarrieraLaureandoInformatica.php";
require_once __DIR__."/classi/GestioneCarrieraStudente.php";
require_once __DIR__."/classi/FileConfigurazione.php";

class Test
{

    public function __construct()
    {
        $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."<b><U><center>⚠️Test Anagrafica⚠️</U></center></b><br>";
        $this->test_anagrafica();
        $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."<br><U><br><b>⚠️Test Bonus⚠️</b></U><br>";
        $this->test_bonus();
        $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."<br><U><br><b>⚠️Test CdL⚠️</b></U><br>";
        $this->test_cdl();
        $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."<br><U><br><b>⚠️Test Esami⚠️</b></U><br>";
        $this->test_esami();
        $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."<br><U><br><b>⚠️Test Valori⚠️</b></U><br>";
        $this->test_valori();
    }

    //verifica che alcuni campi della classe CarrieraLaureando siano inizializzati correttamente
    private function test_anagrafica(): void
    {
        if (
            $this->anagrafica(
                "123456",
                "T. Ing. Informatica",
                "2023-01-04",
                "123456",
                "GIANLUIGI",
                "DONNARUMMA",
                "2023-01-04",
                "29-08-2016"
            ) &&
            $this->anagrafica(
                "234567",
                "M. Ing. Elettronica",
                "2023-01-04",
                "234567",
                "ALESSANDRO",
                "BASTONI",
                "2023-01-04",
                "24-10-2018"
            ) &&
            $this->anagrafica(
                "345678",
                "T. Ing. Informatica",
                "2023-01-04",
                "345678",
                "NICCOLO",
                "BARELLA",
                "2023-01-04",
                "29-07-2019"
            ) &&
            $this->anagrafica(
                "456789",
                "T. Ing. delle Telecomunicazioni",
                "2023-01-04",
                "456789",
                "MATTEO",
                "POLITANO",
                "2023-01-04",
                "25-07-2019"
            ) &&
            $this->anagrafica(
                "567890",
                "M. Cybersecurity",
                "2023-01-04",
                "567890",
                "FRANCESCO",
                "ACERBI",
                "2023-01-04",
                "17-09-2020"
            )
        ) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."Ok. ✅<br>";
        }
    }

    private function anagrafica(
        string $matricola,
        string $cdl,
        string $dataLaurea,
        string $expected_matricola,
        string $expected_nome,
        string $expected_cognome,
        string $expected_dataLaurea,
        string $expected_dataIscrizione
    ): bool {
        $ret = true;
        $laureando = new CarrieraLaureando($matricola, $cdl, $dataLaurea);

        $result = $laureando->matricola;
        if ($result != $expected_matricola) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_matricola."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        $result = $laureando->nome;
        if ($result != $expected_nome) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_nome."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        $result = $laureando->cognome;
        if ($result != $expected_cognome) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_cognome."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        $expected_dataLaurea = strtotime($expected_dataLaurea);
        $result = $laureando->dataLaurea;
        if ($result != $expected_dataLaurea) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_dataLaurea."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        $expected_dataIscrizione = strtotime($expected_dataIscrizione);
        $result = $laureando->dataIscrizione;
        if ($result != $expected_dataIscrizione) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_dataIscrizione."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        return $ret;
    }

    //verifica che il bonus dei laureandi in ingegneria informatica sia settato correttamente
    private function test_bonus(): void
    {
        if (
            $this->bonus("123456", "2023-01-04", false) &&
            $this->bonus("345678", "2023-01-04", true)
        ) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."Ok. ✅<br>";
        }
    }

    private function bonus(string $matricola, string $dataLaurea, bool $expected_bonus): bool
    {
        $laureando = new CarrieraLaureandoInformatica($matricola, "T. Ing. Informatica", $dataLaurea);

        $result = $laureando->bonus;
        if ($result != $expected_bonus) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_bonus."\" result \"".$result."\"!❌<br>";

            return false;
        }

        return true;
    }

    //verifica che il cdl sia presente all'interno del sistema GestioneCarrieraStudente
    private function test_cdl(): void
    {
        if (
            $this->cdl("T. Ing. Biomedica") &&
            $this->cdl("T. Ing. Elettronica") &&
            $this->cdl("T. Ing. Informatica") &&
            $this->cdl("T. Ing. delle Telecomunicazioni") &&
            $this->cdl("M. Ing. Biomedica, Bionics Engineering") &&
            $this->cdl("M. Ing. Elettronica") &&
            $this->cdl("M. Computer Engineering, Artificial Intelligence and Data Enginering") &&
            $this->cdl("M. Ing. Robotica e della Automazione") &&
            $this->cdl("M. Ing. delle Telecomunicazioni") &&
            $this->cdl("M. Cybersecurity")
        ) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."Ok. ✅<br>";
        }
    }

    private function cdl(string $cdl): bool
    {
        $percorso = __DIR__."/config/cdl.json";
        $json = file_get_contents($percorso);
        $cdl_json = json_decode($json, true);
        $corsoStudente = $cdl;

        if (!array_key_exists($corsoStudente, $cdl_json)) {
            $_SESSION["statoErrori"] .= '<span style="color: red;">Il software non supporta il corso di laurea "'.$corsoStudente.'"!❌</span><br>';
            return false;
        }

        return true;
    }

    //verifica che l'elenco degli esami presenti nella carriera del laureando sia corretto
    private function test_esami(): void
    {
        if (
            $this->esami("123456", "T. Ing. Informatica", "2023-01-04", 23) &&
            $this->esami("234567", "M. Ing. Elettronica", "2023-01-04", 12) &&
            $this->esami("345678", "T. Ing. Informatica", "2023-01-04", 22) &&
            $this->esami("456789", "T. Ing. delle Telecomunicazioni", "2023-01-04", 13) &&
            $this->esami("567890", "M. Cybersecurity", "2023-01-04", 13)
        ) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."Ok. ✅<br>";
        }
    }

    private function esami(string $matricola, string $cdl, string $dataLaurea, int $expected_esami): bool
    {
        $ret = true;
        $laureando = new CarrieraLaureando ($matricola, $cdl, $dataLaurea);

        $result = count($laureando->esami);
        if ($result != $expected_esami) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_esami."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        $esami = GestioneCarrieraStudente::prelevaCarriera($matricola);
        $esamiDaRimuovere = FileConfigurazione::getEsamiNonCarriera($cdl, $matricola);
        $nomiEsamiLaureando = array_map(function ($esame) {
            return $esame->nome; // Accedi al campo nome dell'oggetto
        }, $laureando->esami);

        foreach ($esami as $esame) {
            if (is_array($esame["DES"])) {
                continue;
            }
            if (!in_array($esame["DES"], $nomiEsamiLaureando) && !in_array(
                    $esame["DES"],
                    $esamiDaRimuovere
                ) && $esame["SOVRAN_FLG"] != 1) {
                $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": l'esame \"".$esame["DES"]."\" è assente dal prospetto!<br>";
                $ret = false;
            }
        }

        return $ret;
    }

    //controlla che alcuni campi inerenti alla carriera dello studente siano corretti
    private function test_valori(): void
    {
        if (
            $this->valori("123456", "T. Ing. Informatica", "2023-01-04", 23.655, 174, 177, 177, 23.667) &&
            $this->valori("234567", "M. Ing. Elettronica", "2023-01-04", 24.559, 102, 102, 102) &&
            $this->valori("345678", "T. Ing. Informatica", "2023-01-04", 25.564, 165, 177, 177, 25.75) &&
            $this->valori("456789", "M. Ing. delle Telecomunicazioni", "2023-01-04", 32.625, 96, 96, 96) &&
            $this->valori("567890", "M. Cybersecurity", "2023-01-04", 24.882, 102, 120, 102)
        ) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"]."Ok. ✅<br>";
        }
    }

    private function valori(
        string $matricola,
        string $cdl,
        string $dataLaurea,
        float $expected_media,
        int $expected_cfuMedia,
        int $expected_cfuCurr,
        int $expected_cfuCorso,
        ?float $expected_mediaInf = null
    ): bool {
        $ret = true;
        if ($cdl == "T. Ing. Informatica") {
            $laureando = new CarrieraLaureandoInformatica ($matricola, $cdl, $dataLaurea);
        } else {
            $laureando = new CarrieraLaureando ($matricola, $cdl, $dataLaurea);
        }

        $result = round($laureando->mediaPesata, 3);
        if ($result != $expected_media) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_media."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        $result = $laureando->cfuMedia;
        if ($result != $expected_cfuMedia) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_cfuMedia."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        $result = $laureando->calcolaCfuCurricolari();
        if ($result != $expected_cfuCurr) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_cfuCurr."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        $result = FileConfigurazione::getCfuCurricolari($cdl);
        if ($result != $expected_cfuCorso) {
            $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_cfuCorso."\" result \"".$result."\"!❌<br>";
            $ret = false;
        }

        if ($cdl == "T. Ing. Informatica") {
            $result = round($laureando->mediaInformatica, 3);
            if ($result != $expected_mediaInf) {
                $_SESSION["statoErrori"] = $_SESSION["statoErrori"].$matricola.": expected \"".$expected_mediaInf."\" result \"".$result."\"!❌<br>";
                $ret = false;
            }
        }

        return $ret;
    }
}
