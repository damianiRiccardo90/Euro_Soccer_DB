<?php

require_once "Table.php";

abstract class DBTable extends Table {
    private $query;
    private $id;
    protected $fields = [];
    protected $rows = [];

    /**
     * Costruttore di DBTable. Se ci sono più tabelle nella stessa pagina, per renderle tutte
     * ordinabili è necessario fornire un id univoco al momento della creazione dell'oggetto.
     * Se l'id non viene fornito la tabella non potrà essere ordinata.
     *
     * @param string $query La query utilizzata per creare e popolare la tabella.
     * @param int $id L'id univoco che identifica la tabella, necessario se ci sono più tabelle
     * nella stessa pagina e si vuole fare in modo che siano tutte ordinabili.
     */
    public function __construct($query, $id = null) {
        parent::__construct();
        $this->query = $query;
        $this->id = $id;
        $this->queryDB($this->query);
        $this->sort();
        $this->createDOM();
    }
    public function getID() { return $this->id; }
    protected function queryDB($query) {
        if($result = $this->mysqli->query("$query")) {
            $this->fields = $result->fetch_fields();
            $this->rows = $result->fetch_all();
            $result->free();
        }
        else {
            exit($this->mysqli->error);
        }
    }
    protected function createDOM() {
        $this->dom = new DOMDocument();
        $root = $this->dom->appendChild($this->dom->createElement("table"));
        $thead = $root->appendChild($this->dom->createElement("thead"));
        $thead_tr = $thead->appendChild($this->dom->createElement("tr"));
        foreach($this->fields as $item) {
            $field = $item->name;
            $thead_th = $this->dom->createElement("th");
            $thead_text_div = $this->dom->createElement("div", "$field");
            $thead_text_div->setAttribute("class", "text");
            $thead_th->appendChild($thead_text_div);
            $thead_tr->appendChild($thead_th);
        }
        $tbody = $root->appendChild($this->dom->createElement("tbody"));
        foreach($this->rows as $i) {
            $tbody_tr = $tbody->appendChild($this->dom->createElement("tr"));
            foreach($i as $j) {
                $tbody_tr->appendChild($this->dom->createElement("td", "$j"));
            }
        }
    }
    protected function setStyle($class_value) {
        $root = $this->dom->getElementsByTagName("table")->item(0);
        $root->setAttribute("class", "$class_value");
    }
    /**
     * Crea i link alle pagine corrispondenti al contenuto dei vari record della tabella.
     * Entrambi gli indici partono da 0.
     *
     * @param string $path Prefisso dell'URL
     * @param int $anchor_column Indice della colonna dove posizionare gli anchor
     * @param int $attr_column Indice della colonna da cui estrarre il valore dell'attributo della query string
     * @param string $param Nome dell'attributo della query string
     */
    protected function setAnchors($path, $anchor_column, $attr_column, $param) {
        $xpath = new DOMXPath($this->dom);
        $anchor_nodes = $xpath->query("//td[" . ($anchor_column + 1) . "]");
        $attr_nodes = $xpath->query("//td[" . ($attr_column + 1) . "]");
        for($i = 0; $i < $anchor_nodes->length; $i++) {
            $anchor_str = $anchor_nodes->item($i)->textContent;
            $anchor_nodes->item($i)->nodeValue = "";
            $node = $anchor_nodes->item($i)->appendChild($this->dom->createElement("a", "$anchor_str"));
            $attr_str = $attr_nodes->item($i)->textContent;
            $attr = $this->dom->createAttribute("href");
            $attr->value = $path . "index.php" . "?" . $param . "=" . $attr_str;
            $node->appendChild($attr);
        }
    }
    /**
     * Rimuove dal DOM un'intera colonna.
     *
     * @param int $index Indice della colonna da eliminare (parte da 0)
     */
    protected function removeColumn($index) {
        if($index >= count($this->fields))
            echo printf("Il valore %s di 'index' non è valido", $index);
        else {
            array_splice($this->fields, $index, 1);
            foreach($this->rows as $row)
                array_splice($row, $index, 1);

            $xpath = new DOMXPath($this->dom);
            $body_nodes = $xpath->query("//td[" . ($index + 1) . "]");
            $head_node = $xpath->query("//thead[1]/tr/th[" . ($index + 1) . "]");
            for($i = 0; $i < $body_nodes->length; $i++)
                $body_nodes->item($i)->parentNode->removeChild($body_nodes->item($i));
            $head_node->item(0)->parentNode->removeChild($head_node->item(0));
        }
    }

    /**
     * Aggiunge i tooltip per ogni elemento presente nell'header.
     *
     * @param array $tooltips_array Un array contenente le stringhe che verrano
     * utilizzate per i tooltip.
     */
    protected function createTooltips($tooltips_array) {
        if(count($tooltips_array) != count($this->fields))
            echo "Il numero dei valori contenuti in 'tooltips_array' non coincide con il 
            numero di colonne dell'header";
        else {
            $xpath = new DOMXPath($this->dom);
            /*
            $head_nodes = $xpath->query("//thead[1]/tr/th");
            for($i = 0; $i < count($tooltips_array); $i++) {
                $head_nodes[$i]->textContent = null;
                $div = $this->dom->createElement("div", $this->fields[$i]->name);
                $div->setAttribute("class", "tooltip");
                $span = $this->dom->createElement("span", $tooltips_array[$i]);
                $span->setAttribute("class", "tooltip-text");
                $div->appendChild($span);
                $head_nodes[$i]->appendChild($div);
            }
            */
            $head_text_nodes = $xpath->query("//thead[1]/tr/th/div");
            for($i = 0; $i < count($tooltips_array); $i++) {
                $class_names = $head_text_nodes[$i]->getAttribute("class");
                $head_text_nodes[$i]->setAttribute("class", $class_names . " tooltip");
                $span = $this->dom->createElement("span", $tooltips_array[$i]);
                $span->setAttribute("class", "tooltip-text");
                $head_text_nodes[$i]->appendChild($span);
            }
        }
    }

    /**
     * Se l'id è stato fornito al momento della creazione dell'oggetto ed è presente la variabile 'sort#id'
     * nella query string, contenente come valori gli indici delle colonne della tabella da ordinare, allora
     * la tabella sarà ordinata secondo le istruzioni fornite.
     */
    protected function sort() {
        if(isset($this->id) and isset($_GET["sort#$this->id"])) {


            $values = explode("-", $_GET["sort#$this->id"]);
            foreach($values as $value)
                $indexes[mb_substr($value, 0, 1)] = mb_substr($value, 1, 1);

            if($indexes[array_keys($indexes)[0]] == "a") $order = " ASC";
            elseif($indexes[array_keys($indexes)[0]] == "d") $order = " DESC";

            $replace_str = "ORDER BY " . $this->fields[$indexes[0]].name . $order;

            for($i = 1; $i < count($indexes); $i++)
                if($indexes[array_keys($indexes)[$i]] == "a") $order = " ASC";
                elseif($indexes[array_keys($indexes)[$i]] == "d") $order = " DESC";
                $replace_str .= ", " . $this->fields[array_keys($indexes)[$i]].name . $order;

            if(preg_match("/(order by)/i", $this->query))
                $this->query = preg_replace("/(order by)(\\s?\\S+(\\s(asc|desc))?,?){1,}/i",
                                            $replace_str, $this->query);
            else
                $this->query .= $replace_str;

            $this->queryDB($this->query);

            // Modifico l'header della tabella

            $xpath = new DOMXPath($this->dom);
            $number = 1;
            $query_str = "?";
            foreach($_GET as $qkey => $qvalue)
                $query_str .= $qkey . "=" . $qvalue . "&";
            $query_str = mb_substr($query_str, 0, -1);

            foreach($indexes as $key => $value) {
                $th = $xpath->query("//thead/tr/th[$key]")->item(0);

                $div = $this->dom->createElement("div", $number++);
                if($value == "a") $direction = "up";
                elseif($value == "d") $direction = "down";
                $span = $this->dom->createElement("span");
                $span->setAttribute("class", "arrow $direction");
                $div->appendChild($span);
                $th->appendChild($div);

                $text_div = $xpath->query("//thead/tr/th[$key]/div[1]")->item(0);
                $text = $text_div->textContent;
                $text_div->textContent = null;
                $a = $this->dom->createElement("a", $text);

                if($value == "d") {
                    $href_str = preg_replace("/(sort$this->id=(\\d(a|d)-?)*{$key})(d)/", "$1"."a", $query_str);
                }
                elseif($value == "a") {
                    preg_match("/sort$this->id=(\\d(a|d)-?)*/", $query_str, $subquery_str);
                    preg_match("/sort$this->id=/", $subquery_str[0], $prefix);
                    preg_match("/\\d(a|d).*(a|d)/", $subquery_str[0], $suffix);
                    $suffix_array = explode("-", $suffix[0]);
                    for($i = 0; $i < count($suffix_array); $i++)
                        if(mb_substr($suffix_array[$i], 0, 1) == $key) unset($suffix_array[$i]);
                    $new_suffix = implode("-", $suffix_array);
                    $new_subquery_str = $prefix[0] . $new_suffix;
                    $href_str = preg_replace("/sort$this->id=(\\d(a|d)-?)*/", $new_subquery_str, $query_str);
                }

                $a->setAttribute("href", $href_str);
                $text_div->appendChild($a);
            }
            //Aggiungo i link agli header le cui colonne non sono ordinate
        }
    }
}