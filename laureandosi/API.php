<?php

require_once "classi/GUI.php";
session_start();

$_SESSION["stato"] = "";
$_SESSION["link"] = "";
$_SESSION["statoErrori"] = "";
$_SESSION["disabilitato"] = "disabled";
$_SESSION["test"] = "";
$_SESSION["testTitolo"] = "";
$_SESSION["sfondo"] = "";

// Gestione del test
if (isset($_REQUEST["test"])) {
    require_once "test.php";
    $_SESSION["test"] = "style=\"display: none\"";
    $_SESSION["testTitolo"] = "<p style=\"font-size: 30pt \"><b>Generatore Prospetti di Laurea - TEST 🔬</b></p>";
    $_SESSION["sfondo"] = "style=\"background-color: rgb(230, 230, 230);\"";
    $test = new Test();
}

// Creazione prospetti
if (isset($_POST["cdl"], $_POST["data"], $_POST["matricole"], $_POST["crea"])) {
    $_SESSION["gp"] = new GUI($_POST["matricole"], $_POST["cdl"], $_POST["data"]);
    if (!$_SESSION["gp"]->controlloMatricole()) {
        $_SESSION["statoErrori"] = "Errore inserimento matricole!";
    } else {
        $_SESSION["gp"]->generaProspetti();
        $_SESSION["link"] = $_SESSION["gp"]->apriProspetti();
        $_SESSION["stato"] = "Prospetti creati";
        $_SESSION["disabilitato"] = "";
    }
}

// Invio prospetti
if (isset($_POST["invia"])) {
    $_SESSION["gp"]->inviaProspetti();
    $_SESSION["link"] = $_SESSION["gp"]->apriProspetti();
}