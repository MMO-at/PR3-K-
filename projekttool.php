<?php
    /************************************************************
    *  Komplexe Übung PR3 -  Herbstsemester 2024                *
    *  Projektmanagent Tool - anzeigen, bearbeiten, hinzufügen  *
    *  Manuel Moritz                                            *
    *  1139499                                                  *
    ************************************************************/
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Projektmanagement Tool für die Komplexe Übung im Modul PR3">
    <meta name="keywords" content="HFH, PR3, Komplexe Übng">
    <meta name="author" content="Manuel Moritz">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style><?php
    require_once("styles.css");
    require_once("db_functions.inc.php");
    ?></style>
    <title>Projektmanagement Tool</title>
</head>

<body>
    <header>
        <div><h1>Projektmanagement Tool</h1></div>
    </header>
    <main>
        <?php
        $mysqli = db_login();
        
        /***************************************************
        *  Bearbeitet vorhandenen Eintrag in de Datenbank  *
        ***************************************************/
        if (isset($_POST['start_bearb'])) {
            $beschreibung_bearb = $mysqli->escape_string($_POST['beschr_bearb']);
            $name_bearb = $mysqli->escape_string($_POST['name_bearb']);

            $sql = "UPDATE projekt SET ";
            $sql .= "startdatum = '$_POST[start_bearb]', ";
            $sql .= "enddatum = '$_POST[end_bearb]', ";
            $sql .= "name = '$name_bearb', ";
            $sql .= "verantwortlich = '$_POST[verant_bearb]', ";
            $sql .= "beschreibung = '$beschreibung_bearb' ";
            $sql .= "WHERE id = '$_POST[id]'";
            $mysqli->query($sql);
        }

        /****************************************************
        *       Erstellt neuen Eintrag in de Datenbank      *
        ****************************************************/
        if (isset($_POST['start_neu'])) {
            $beschreibung_neu = $mysqli->escape_string($_POST['beschr_neu']);
            $name_neu = $mysqli->escape_string($_POST['name_neu']);

            $id_neu = substr($_POST['verant_neu'], 0, 5);
            $id_neu .= substr($name_neu, 0, 5);
            $id_neu .= substr($beschreibung_neu, 0, 5);
            $sql = "INSERT INTO projekt ";
            $sql .= "(id, startdatum, enddatum, name, verantwortlich, beschreibung) VALUES ";
            $sql .= "('$id_neu', '$_POST[start_neu]', '$_POST[ende_neu]','$name_neu', '$_POST[verant_neu]', '$beschreibung_neu')";
            $mysqli->query($sql);
        }

        /****************************************************
        *  Öffnet Fenster zum Bearbeiten eines vorhandenen  *
        *  Eintrages in der Datenbank                       *
        ****************************************************/
        if (isset($_POST['prjbear'])) {
            $sql = "SELECT * FROM projekt WHERE id='$_POST[prjbear]'";
            $sqlquery = $mysqli->query($sql);
            $result = $sqlquery->fetch_object();

            echo "<div class=\"projektbearbeiten\">
                <form method=\"POST\">
                <input type=\"hidden\" name=\"id\" value=\"$result->id\">
                <table>
                    <tr>
                        <th>
                            Zeitraum
                        </th>
                        <td>
                            <input type=\"date\" name=\"start_bearb\" value=\"$result->startdatum\" required>
                            <input type=\"date\" name=\"end_bearb\" value=\"$result->enddatum\">
                        </td>
                    </tr><tr>
                        <th>
                            Projektname
                        </th>
                        <td>
                            <input type=\"text\" maxlength=\"255\" name=\"name_bearb\" value=\"$result->name\" required>
                        </td>
                    </tr><tr>
                        <th>
                            Verantwortlicher
                        </th>
                        <td>
                            <input type=\"text\" maxlength=\"255\" name=\"verant_bearb\" value=\"$result->verantwortlich\" required>
                        </td>
                    </tr><tr>
                        <th>
                            Beschreibung
                        </td>
                        <td>
                            <textarea name=\"beschr_bearb\" value=\"$result->beschreibung\" required>$result->beschreibung</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type=\"submit\" value=\"Ändern\"></form>
                            <a href=\"\"><div>X</div></a></td>
                    </tr>
                </table>
            </div>";
        }

        /****************************************************
        *  Öffnet Fenster zum Erstellen eines neuen         *
        *  Eintrages in der Datenbank                       *
        ****************************************************/
        if (isset($_POST['prjhinzu'])) {
            echo "<div class=\"projektbearbeiten\">
            <form method=\"POST\">
            <table>
                <tr>
                    <th>Zeitraum</th>
                    <td>
                        <input type=\"date\" name=\"start_neu\" required>
                        <input type=\"date\" name=\"ende_neu\">
                    </td>
                </tr><tr>
                    <th>Projektname</th>
                    <td><input type=\"text\" maxlength=\"255\" name=\"name_neu\" required></td>
                </tr><tr>
                    <th>Verantwortlicher</th>
                    <td><input type=\"text\" maxlength=\"255\" name=\"verant_neu\" required></td>
                </tr><tr>
                    <th>Beschreibung</td>
                    <td><textarea name=\"beschr_neu\" required></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type=\"submit\" value=\"Hinzufügen\"></form>
                        <div><a href=\"\">X</a></div>
                    </td>
                </tr>
            </table>
        </div>";
        }

        /******************************************************
        *  Anzeige der vorhandenen Einträge in der Datenbank  *
        ******************************************************/
        echo "<table id=\"tabelle\">";
        echo "  <tr class=\"liste\">                        
                    <td class=\"bearbeiten\"></td>
                    <th class=\"datum\">Start</th>
                    <th class=\"datum\">Ende</th>
                    <th class=\"name\">Projektname</th>
                    <th class=\"verantwortlich\">Verantwortlicher</th>
                </tr>";

        if (isset($_POST['laufendeprj']) && ($_POST['laufendeprj'] == "Laufende Projekte")) {
            $aktuellesdatum = date("Y-m-d", time());
            $result = $mysqli->query("SELECT * FROM projekt WHERE enddatum > '$aktuellesdatum' OR enddatum = '0000-00-00' ORDER BY startdatum DESC");
        } else {
            $result = $mysqli->query("SELECT * FROM projekt ORDER BY startdatum DESC"); 
        }

        while ($projekt = $result->fetch_object()) {

            $startdatum = date('d. M Y', strtotime($projekt->startdatum));
            $enddatum = date('d. M Y', strtotime($projekt->enddatum));
            $beschreibung = nl2br($projekt->beschreibung);

            echo "<tr class=\"liste\">";
            echo "  <td class=\"bearbeiten\">
                        <form method=\"POST\">
                            <input type=\"submit\" id=\"aendern\" value=\"O\" title=\"Projekt bearbeiten\">
                            <input type=\"hidden\" name=\"prjbear\" value=\"$projekt->id\">
                        </form>
                    </td>";
            echo "  <td class=\"datum\">$startdatum</td>
                    <td class=\"datum\">"; 
                    if ($projekt->enddatum == "0000-00-00") {
                        echo "";
                    } else {
                        echo "$enddatum";
                    }
            echo "  </td>
                    <td class=\"name\">$projekt->name</td>
                    <td class=\"verantwortlich\">$projekt->verantwortlich</td>
                  </tr>
                  <tr class=\"liste\">
                    <td></td>
                    <td colspan=4><div class=\"beschreibung\">$beschreibung</div></td>
                  </tr>";
        }
        echo "<tr>
                <td class=\"bearbeiten anzahl\"></td>
                <td class=\"anzahl\">Projektanzahl</th>
                <td class=\"anzahl\">$result->num_rows</td>
                <td class=\"anzahl\">
                    <form method=\"POST\">
                        <input type=\"submit\" id=\"laufende\" name=\"laufendeprj\" value=\"Laufende Projekte\" title=\"Nur laufende Projekte anzeigen\">
                    </form>
                </td>
                <td class=\"anzahl\">
                    <form method=\"POST\">
                        <input type=\"submit\" id=\"hinzufuegen\" name=\"prjhinzu\" value=\"+\" title=\"Projekt hinzufügen\">
                    </form>
                </td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <div class=\"zurueck\"><a href=\"\">Alle Projekte anzeigen</a></div>
                </td>
               <td></td>
              </tr>
            </table>";
        ?>
    </main>
</body>
</html>