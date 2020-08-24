<?php

class Front
{
    public $sitekey = "";
    public $callback_url = "";
    public $host = "";
    public $ip = "";
    public $validation_url = "";

    public function generate()
    {
        $header = $this->get_header();
        $body = $this->get_body();
        $res = <<<body
<!DOCTYPE html>
<html lang="en">
$header
$body
</html>
body;
        return $res;
    }

    private function get_header()
    {
        $res = <<<body
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=max-width, initial-scale=1.0"/>
    <meta name="description" content="APSoft DDOS Protection"/>
    <title>APDdos protection | {$this->host}</title>
    <link rel="stylesheet" href="style.css"/>
    <script src="https://www.google.com/recaptcha/api.js?render={$this->sitekey}"></script>
</head>
body;
        return $res;
    }

    private function get_body()
    {
        $scripts = $this->get_javascript();
        $res = <<<body
<body>
<input type="hidden" id="callback-url" value="{$this->callback_url}"/>
<input type="hidden" id="site-key" value="{$this->sitekey}"/>
<input type="hidden" id="validation-url" value="{$this->validation_url}"/>
<div class="div-main">
    <h2>APSoft anti-ddos protection</h2>
    <h3>ip address : <span class="success">{$this->ip}</span></h3>
    <h3>status : <span class="checking" id="status">-</span></h3>
    <h3>callback url : <span class="host-span">{$this->callback_url}</span></h3>
    <br>
    <h3 id="final-text">You will be redirected in <span id="num-counter">3s</span></h3>
</div>
$scripts
</body>
body;
        return $res;
    }

    private function get_javascript()
    {
        $array =
            [
                "main.js",
                "jquery.min.js"
            ];
        $res = '';
        $time = strtotime(date("Y/m/d H:i:s"));
        foreach ($array as $item) {
            $res .= <<<body
<script src="$item?v={$time}"></script>
body;
        }
        return $res;

    }
}