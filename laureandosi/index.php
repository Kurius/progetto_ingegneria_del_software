<?php
require_once "API.php";
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Laureandosi</title>
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/style.css">
    </head>

    <body <?php echo $_SESSION["sfondo"]  ?>>
        <form method="post" action="index.php">
            <table <?php echo $_SESSION["test"]?>>
                <tr id="riga1">
                    <td colspan="3"><b>Gestione Prospetti di Laurea<?php echo $_SESSION["test"]?></b></td>
                </tr>
                <tr id="riga2">
                    <td>
                        <label class="info"><b>CdL:</b></label><br>
                        <select name="cdl" style="font-size: 16px;" required>
                            <option value="">Seleziona il CdL</option>
                            <option value="T. Ing. Biomedica">T. Ing. Biomedica</option>
                            <option value="T. Ing. Elettronica">T. Ing. Elettronica</option>
                            <option value="T. Ing. Informatica">T. Ing. Informatica</option>
                            <option value="T. Ing. delle Telecomunicazioni">T. Ing. delle Telecomunicazioni</option>
                            <option value="M. Ing. Biomedica, Bionics Engineering">M. Ing. Biomedica, Bionics Engineering</option>
                            <option value="M. Ing. Elettronica">M. Ing. Elettronica</option>
                            <option value="M. Computer Engineering, Artificial Intelligence and Data Enginering">M. Computer Engineering, Artificial Intelligence and Data Enginering</option>
                            <option value="M. Ing. Robotica e della Automazione">M. Ing. Robotica e della Automazione</option>
                            <option value="M. Ing. delle Telecomunicazioni">M. Ing. delle Telecomunicazioni</option>
                        </select><br><br><br><br>
                        <label class="info"><b>Data di laurea: </b></label>
                        <input type="date" name="data" required>
                    </td>
                    <td>
                        <label class="info"><b>Matricole:</b></label><br>
                        <textarea title="matricole" name="matricole"  style="font-size: 16px;" required></textarea>
                    </td>
                    <td>
                        <button type="submit" name="crea">Crea Prospetti</button><br><br></form>
                        <a href=<?php echo $_SESSION["link"];?> target="blank"><p>Visualizza Prospetti</p><br><br></a>
                        <form method="post" action="index.php">
                            <button type="submit" name="invia" <?php echo $_SESSION["disabilitato"] ?>>Invia Prospetti</button>
                        </form>
                        <p style="color: red;"><?php echo $_SESSION["stato"] ?></p><br><br>
                    </td>
            </table>
            <table style="margin-top: 0; border: none; background-color: transparent;">
                <tr>
                    <td style="width:700pt;">
                        <?php echo $_SESSION["testTitolo"] ?>
                        <p style="color: black; text-align: left;"><?php echo $_SESSION["statoErrori"]?><p>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>