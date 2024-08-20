<?php
    /************************************************************
    *  Komplexe Übung PR3 -  Herbstsemester 2024                *
    *  Projektmanagent Tool - anzeigen, bearbeiten, hinzufügen  *
    *  Manuel Moritz                                            *
    *  1139499                                                  *
    ************************************************************/

    
    /**********************************************************
    *                        db_login()                       *
    *  Stellt eine Verbindung zur Datenbank her und gibt ein  *
    *  ein Datenbank-Objekt zurück                            *
    **********************************************************/
    function db_login() {
        $server = "localhost";
        $user = "root";
        $password = "";
        $database = "datenbank";

        $mysqli = new mysqli($server, $user, $password, $database);
        if ($mysqli->connect_errno) {
            echo "<h1>Fehler beim Zugriff auf Datenbank</h1>";
        }
        $mysqli->set_charset("utf8");

        return $mysqli;
    }

 ?>