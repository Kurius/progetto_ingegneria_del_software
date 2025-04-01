<?php
class GestioneCarrieraStudente
{
    // Preleva i dati anagrafici di uno studente
    public static function prelevaAnagrafica(string $matricola): array
    {
        $path = __DIR__ . "/../GestioneCarrieraStudente/" . $matricola . "_anagrafica.json";

        if (!file_exists($path)) {
            // Se il file non esiste, ritorna un errore
            error_log("File non trovato: $path");
            return [];
        }

        $json = file_get_contents($path);
        if ($json === false) {
            // Errore nella lettura del file
            error_log("Errore nella lettura del file: $path");
            return [];
        }

        $anagrafica = json_decode($json, true);
        if ($anagrafica === null) {
            // Errore nella decodifica JSON
            error_log("Errore nella decodifica del file JSON: $path");
            return [];
        }

        return $anagrafica["Entries"]["Entry"] ?? [];
    }

    // Preleva la carriera di uno studente (esami)
    public static function prelevaCarriera(string $matricola): ?array
    {
        $path = __DIR__ . "/../GestioneCarrieraStudente/" . $matricola . "_esami.json";

        if (!file_exists($path)) {

            error_log("non trovato il file");
            // Se il file non esiste, ritorna null
            return null;
        }

        $json = file_get_contents($path);
        if ($json === false) {
            // Errore nella lettura del file
            error_log("Errore nella lettura del file: $path");
            return null;
        }

        $esami = json_decode($json, true);
        if ($esami === null) {
            // Errore nella decodifica JSON
            error_log("Errore nella decodifica del file JSON: $path");
            return null;
        }

        return $esami["Esami"]["Esame"] ?? null;
    }
}
