<?php
class Response
{
    private string $accept;
    private string $datetime;
    private int $htmlStatus;
    private string $message;
    private array $body;

    private function __construct()
    {
        $this->datetime = date('Y-m-d H:i:s');
    }

    public static function getInstance(string $at="text/html")
    {
        $res = new Response();
        $res->accept = $at;
        //Setting server error as default
        $res->htmlStatus = 500;
        $res->message = "Internal Server Error";
        $res->body = [];

        return $res;
    }
    public function getAccept(): string {return $this->accept;}

	public function getDatetime(): string {return $this->datetime;}

	public function getHtmlStatus(): int {return $this->htmlStatus;}

	public function getMessage(): string {return $this->message;}

	public function getBody(): array {return $this->body;}

	public function setAccept(string $accept): void {$this->accept = $accept;}

	public function setDatetime(string $datetime): void {$this->datetime = $datetime;}

	public function setHtmlStatus(int $htmlStatus): void {$this->htmlStatus = $htmlStatus;}

	public function setMessage(string $message): void {$this->message = $message;}

	public function setBody(array $body): void {$this->body = $body;}

	
    
}
