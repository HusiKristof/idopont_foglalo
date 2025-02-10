<?php
class Request
{
    public string $method;
    private string $uri;
    private string $accept;
    private string $datetime;
    private array $body;

    private function __construct()
    {
        $this->datetime = date('Y-m-d H:i:s');
    }

    public static function getInstance( string $uri, string $rm, string $at="text/html")
    {
        $req = new Request();
        $req->uri = $uri;
        $req->method = $rm;
        $req->accept = $at;
        return $req;
    }

    public function getUri()
    {
        return $this->uri;
    }
    public function getBody()
    {
        if ($this->method !== 'POST') {
            return '';
        }

        $body = [];
        foreach ($_POST as $key => $value) {
            $body[$key] = $value;
        }
        
        return $body;
    }

    public function getTime()
    {
        return $this->datetime;
    }

    public function getQueryParams()
    {

        $queryString = parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY);

        if (!$queryString) {
            return [];
        }

        parse_str($queryString, $queryParams);
        
        return $queryParams;
    }

    public function getMethod($mode = 'value')
    {
        if ($mode == 'value') {
            return $this->method;
        } elseif ($mode == 'full') {
            return $this->method;
        } else {
            return false;
        }
    }

    public function getAcceptType($mode = 'value')
    {
        if ($mode == 'value') {
            return $this->accept;
        } elseif ($mode == 'full') {
            return $this->accept;
        } else {
            return false;
        }
    }
}
