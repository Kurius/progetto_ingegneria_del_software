<?php

require_once 'FileConfigurazione.php'; // Importa la configurazione per i voti e altre impostazioni

class EsameLaureando
{
    public string $nome; // Nome dell'esame
    public string $dataSuperamento; // Data di superamento dell'esame
    public int $voto; // Voto ottenuto all'esame
    public int $cfu; // Crediti formativi universitari dell'esame
    public bool $media; // Indica se l'esame contribuisce alla media dei voti
    public bool $inf; // Indica se l'esame è di natura informatica
    public bool $sovran;
    public function __construct(array $rawData, string $cdl, string $matricola)
    {
        $this->nome = $rawData["DES"]; // Assegna il nome dell'esame
        $this->cfu = $rawData["PESO"]; // Assegna i CFU
        $this->sovran= $rawData["SOVRAN_FLG"];
        $voto = $rawData["VOTO"]; // Recupera il voto come stringa
        $this->dataSuperamento = strtotime(str_replace("/", "-", $rawData["DATA_ESAME"])); // Converte la data nel formato timestamp

        // Se il voto non è stato ancora assegnato, lo imposta a 0
        if ($voto == null) {
            $voto = 0;
        } else {
            // Se il voto è "30 e lode", lo converte nel valore configurato nel sistema
            if ($voto == "30  e lode") {
                $voto = FileConfigurazione::getValoreLode($cdl);
            }
        }
        $this->voto = (int)$voto; // Converte il voto in intero

        $this->media = true; // Di default l'esame fa media
        $this->inf = false; // Di default l'esame non è informatico

        // Verifica se l'esame fa media: se il voto è 0 o l'esame è in una lista di esclusione, non lo conteggia
        if ($this->voto == 0 || in_array($this->nome, FileConfigurazione::getEsamiNonMedia($cdl, $matricola))) {
            $this->media = false;
        }

        // Verifica se l'esame è informatico: solo se il corso di laurea è "T. Ing. Informatica" e il nome dell'esame è in una lista predefinita
        if ($cdl == "T. Ing. Informatica" && in_array($this->nome, FileConfigurazione::getEsamiInformatici())) {
            $this->inf = true;
        }
    }
}
