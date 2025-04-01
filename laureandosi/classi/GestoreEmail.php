<?php

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__."/../lib/PHPMailer/src/PHPMailer.php";
require __DIR__."/../lib/PHPMailer/src/SMTP.php";
require_once "CarrieraLaureando.php";
require_once "FileConfigurazione.php";

class GestoreEmail
{
    public function __construct(string $cdl)
    {
        //i dati anagrafici e gli indirizzi mail vengono prelevati dall'array di sessione
        $carriere = $_SESSION['carriere'];
        $totali = count($carriere);
        $inviate = 0;
        echo "Aggiornamento Live su invio prospetti<br>";
        echo "Prospetti inviati: $inviate/$totali<br>";
        $_SESSION["stato"] = "Prospetti inviati: ".$inviate."/".$totali;

        $email = new PHPMailer();
        $email->IsSMTP();
        $email->Host = "" #host;
        $email->SMTPSecure = "tls";
        $email->Port = 25;
        $email->SMTPAuth = false;
        $email->addCustomHeader('Content-Type', 'text/plain; windows-1252');
        $email->setFrom("email", "Laureandosi");
        $email->Subject = "Appello di laurea in ".$cdl."- indicatori per voto di laurea";
        $email->Body = mb_convert_encoding(FileConfigurazione::getEmailBody($cdl), 'Windows-1252', 'UTF-8');;


        foreach ($carriere as $destinatario) {
            $email->clearAddresses();
            $email->clearAttachments();
            $email->AddAddress($destinatario->email);
            $email->addAttachment(__DIR__."/../prospetti/$cdl/".$destinatario->matricola."_prospetto.pdf");

            if (!$email->Send()) {
                $_SESSION["statoErrori"] .= "Errore nell'invio a {$destinatario->email}: " . $email->ErrorInfo . "<br>"; # questo errore non nascerà quando non sono connesso alla rete Unipi, perchè ci pensa PHPMailer
                echo "Errore nell'invio!<br>";
                error_log("Errore nell'invio per matricola {$destinatario->matricola}: " . $email->ErrorInfo);
                break;
            } else {
                $inviate++;
                error_log("Prospetto inviato per matricola: {$destinatario->matricola}");
                $_SESSION["stato"] = "Prospetti inviati: ".$inviate."/".$totali;
                // Stampa l'aggiornamento immediato
                echo "Prospetti inviati: $inviate/$totali<br>";#da specifiche era chiesto di mostrare dinamicamente l'aggiornamento delle mail inviate, ma si può fare solo con ajax
            }
        }

// Chiude la connessione SMTP
        $email->SmtpClose();

    }
}