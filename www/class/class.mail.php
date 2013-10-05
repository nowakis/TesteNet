<?php
/*
 * Classe Mail
 * para envio de emails
 */
class Mail
{
    private $parts;

    /*
     * Método construtor
     */
    function __construct()
    {
        $this->parts = array();
        $this->boundary = md5(time());
    }

    /*
     * Adiciona HTML
     */
    function addHtml($body)
    {
        $body = stripslashes($body);
        $msg  = "--{$this->mime_boundary}\\n";
        $msg .= "Content-Type: text/html; charset=ISO-8859-1\\n\\n";
        $msg .= $body;

        $this->parts[] = $msg;
    }

    /*
     * Adiciona Texto
     */
    function addText($body)
    {
        $body = stripslashes($body);
        $msg  = "--{$this->mime_boundary}\\n";
        $msg .= "Content-Type: text/plain; charset=ISO-8859-1\\n\\n";
        $msg .= $body;

        $this->parts[] = $msg;
    }

    /*
     * Adiciona Imagem
     */
    function addPng($filename, $download)
    {
        $fd=fopen($filename, 'rb');
        $contents=fread($fd, filesize($filename));
        $contents=chunk_split(base64_encode($contents),68,"\\n");
        fclose($fd);

        $msg  = "--{$this->mime_boundary}\\n";
        $msg .= "Content-Type: image/png; name={$download}\\n";
        $msg .= "Content-Transfer-Encoding: base64\\n";
        $msg .= "Content-Disposition: attachment; filename={$download}\\n\\n";
        $msg .= "{$contents}";

        $this->parts[] = $msg;
    }

    /*
     * Envia Email
     */
    function send($from, $to, $subject)
    {
        $headers  = "From: {$from}\\n";
        $headers .= 'Content-Type: multipart/mixed; boundary="'.$this->mime_boundary."\"\\n";
        $headers .= 'X-Mailer: PHP-' . phpversion() . "\\n";
        $headers .= "Mime-Version: 1.0\\n\\n";

        $msg = implode("\\n", $this->parts);

        mail($to, $subject, $msg, $headers);
    }
}
?>