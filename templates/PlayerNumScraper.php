<?php

require_once "DBConnection.php";

class PlayerNumScraper {
    private $dom;
    private $player_names;

    private function formatName($name) {
        return implode("_", explode(" ", $name));
    }
    private function getHttpResponseCode_using_getheaders($url, $followredirects = true){
        // returns string responsecode, or false if no responsecode found in headers (or url does not exist)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if(! $url || ! is_string($url)){
            return false;
        }
        $headers = @get_headers($url);
        if($headers && is_array($headers)){
            if($followredirects){
                // we want the the last errorcode, reverse array so we start at the end:
                $headers = array_reverse($headers);
            }
            foreach($headers as $hline){
                // search for things like "HTTP/1.1 200 OK" , "HTTP/1.0 200 OK" , "HTTP/1.1 301 PERMANENTLY MOVED" , "HTTP/1.1 400 Not Found" , etc.
                // note that the exact syntax/version/output differs, so there is some string magic involved here
                if(preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches) ){// "HTTP/*** ### ***"
                    $code = $matches[1];
                    return $code;
                }
            }
            // no HTTP/xxx found in headers:
            return false;
        }
        // no headers :
        return false;
    }
    public function run() {
        $query = "SELECT Nome FROM Membro WHERE Ruolo = 'Giocatore'";
        $this->player_names = DBConnection::getInstance()->fetch($query);
        $handle = fopen("diomerdo.txt", "a");
        foreach($this->player_names as $player_row) {
            foreach($player_row as $player_name) {
                echo $player_name;
                $url = "https://en.wikipedia.org/wiki/" . $this->formatName($player_name);
                if($this->getHttpResponseCode_using_getheaders($url) == 200) {
                    $this->dom = new DOMDocument();
                    $this->dom->loadHtml(file_get_contents($url));
                    $xpath = new DOMXPath($this->dom);
                    $player_num = $xpath->query("//th[text() = 'Number']/following-sibling::td");
                    $vaffanjesu = $player_num->item(0)->nodeValue;
                    if(!$vaffanjesu) $vaffanjesu = "\N";
                    $string = $player_name . "," . $vaffanjesu . PHP_EOL;
                    fwrite($handle, $string);
                    echo $string;
                }
            }
        }
    }
}

(new PlayerNumScraper())->run();