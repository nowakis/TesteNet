<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 fdm=marker: */

/**
 * Make easy to send email like html email and attachment email
 *
 * Php versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    Email
 * @package     Easy_Mail.class.php
 * @author      Dolly Aswin Hrp <dolly.aswin@gmail.com>
 * @copyright   2006
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Easy_Mail.class.php,v 1.1 2006/04/12 03:07:11 mynusa Exp $ 
 * @link        http://office.nusa.net.id/~dolly/File/Easy_Mail.zip
 */

// {{{ Easy_Email

class Easy_Email
{
    // {{{ properties

    /**
     * This is destination email address
     * @var string
     */
    private $to ;
    
    /**
     * This is source email address
     * @var string
     */
    private $from ;

    /**
     * This is return path email address
     * @var string
     */
    private $return  ;

    /**
     * This is subject for email
     * @var string
     */
    private $subject ;
    
    /**
     * This is header for email
     * @var string
     */
    private $header ;
    
    // }}}

    // {{{ __construct()

    /**
     * Pass Variable
     * @param   string  $from    Content from email address
     * @param   string  $to      Content to email address
     * @param   string  $subject Content subject email
     * @param   string  $return  Content return path email address
     * No return value. Pass variable into this class 
     */
    function __construct($from, $to, $subject, $return)
    {
        $this->from     = $from ;
        $this->to       = $to   ;
        $this->subject  = $subject ;        
        $this->return   = $return  ;
    }

    // }}}

    // {{{ simpleMail()

    /**
     * Send simple email
     * @param   string  $message    Content for this email 
     * No return value. Send email 
     * @access  public
     * @access  static
     */

    public function simpleMail($message) 
    {
        $this->header   = "From: "      .$this->from. "\n" ;
        $this->header  .= "Reply-To: "  .$this->from. "\n" ;
        $this->header  .= "Return-Path: " .$this->return. "\n" ;
        $this->header  .= "X-Mailer: PHP/"  .phpversion(). "\n" ;
        $this->send($message) ;
    }
    // }}}

    // {{{ htmlMail()

    /**
     * Send html email
     * @param   string  $html       Message with html tag 
     * @param   string  $plainText  Message in plain text format
     * No return value. Send html email 
     * @access  public
     * @access  static
     */

    public function htmlMail($html, $plainText='') 
    {
        $boundary = md5(uniqid(time()));
        $this->header    = "From: "      .$this->from. "\n" ;
        $this->header   .= "To: "        .$this->to. "\n" ;
        $this->header   .= "Return-Path: " .$this->return. "\n" ;
        $this->header   .= "MIME-Version: 1.0\n" ;
        $this->header   .= "Content-Type: multipart/alternative; boundary=\"".$boundary."\"\n";

        // Text Version
        $msgPlainText    = "--" . $boundary . "\n" ;
        $msgPlainText   .= "Content-Type: text/plain; charset=iso-8589-1\n" ;
        $msgPlainText   .= "Content-Transfer-Encoding: 8bit\n" ;
        $msgPlainText   .= "If you are seeing this is because you may need to change your\n" ;
        $msgPlainText   .= "preferred message format from HTML to plain text.\n\n" ;
        if ($plainText == '') {
            // $msgPlainText .= $plainText . "\n" ;
            $plainText   = strip_tags($html) ; 
        }
        $msgPlainText   .= $plainText . "\n" ;

        // HTML Version
        $msgHtml     = "--" . $boundary . "\n" ;
        $msgHtml    .= "Content-Type: text/html; charset=iso-8589-1\n" ;
        $msgHtml    .= "Content-Transfer-Encoding: 8bit\n" ;
        $msgHtml    .= $html ;
        
        $message    = $msgPlainText . $msgHtml ."\n\n" ;
        // Send Email
        $this->send($message) ;
    }
    // }}}

    // {{{ simpleAttachment()

    /**
     * Send simple email with attachment
     * @param   string  $file       File for attachment (file name or path of file)
     * @param   string  $plainText  Message in plain text format
     * No return value. Send simple email with attachment
     * @access  public
     * @access  static
     */

    public function simpleAttachment($file, $plainText='')
    {
        $handle      = fopen($file, 'rb') ;    
        $data        = fread($handle,filesize($file)) ;
        $data        = chunk_split(base64_encode($data))  ;
        $filetype    = mime_content_type($file) ;
        
        $boundary    = md5(uniqid(time()));
        $this->header    = "From: "      .$this->from. "\n" ;
        $this->header   .= "To: "        .$this->to. "\n" ;
        $this->header   .= "Return-Path: " .$this->return. "\n" ;
        $this->header   .= "MIME-Version: 1.0\n" ;
        $this->header   .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"";

        // Text Version
        $msgPlainText    = "--" . $boundary . "\n" ;
        $msgPlainText   .= "Content-Type: text/plain; charset=iso-8589-1\n" ;
        $msgPlainText   .= "Content-Transfer-Encoding: 8bit\n" ;
        if ($plainText != '') {
            $msgPlainText .= $plainText . "\n" ;
        }

        // Attachment Version
        $attach      = "--" . $boundary ."\n" ;
        $attach     .= "Content-Type: " . $filetype . "; name=\"" . $file . "\"\n" ;
        $attach     .= "Content-Transfer-Encoding: base64 \n" ;
        // Need two end of lines
        $attach     .= "Content-Disposition: attachment; filename=\"" .$file. "\"\n\n" ;
        $attach     .= $data   . "\n\n" ;

        $message     = $msgPlainText . $attach ;
        // Send Email
        $this->send($message) ;
    }
    // }}}

    // {{{ htmlAttachment()

    /**
     * Send html email with attachment
     * @param   string  $file       File for attachment (file name or path of file)
     * @param   string  $html       Message in HTML format
     * @param   string  $plainText  Message in plain text format
     * No return value. Send html email with attachment
     * @access  public
     * @access  static
     */

    public function htmlAttachment($file, $html, $plainText='') 
    {
        $handle      = fopen($file, 'rb') ;    
        $data        = fread($handle,filesize($file)) ;
        $data        = chunk_split(base64_encode($data))  ;
        $filetype    = mime_content_type($file) ;
        
        $boundary    = md5(uniqid(time()));
        $this->header    = "From: "      .$this->from. "\n" ;
        $this->header   .= "To: "        .$this->to. "\n" ;
        $this->header   .= "Return-Path: " .$this->return. "\n" ;
        $this->header   .= "MIME-Version: 1.0\n" ;
        $this->header   .= "Content-Type: multipart/related; boundary=\"".$boundary."\"\n";

        // Text Version
        $msgPlainText    = "--" . $boundary . "\n" ;
        $msgPlainText   .= "Content-Type: text/plain; charset=iso-8589-1\n" ;
        $msgPlainText   .= "Content-Transfer-Encoding: 8bit\n" ;
        $msgPlainText   .= "If you are seeing this is because you may need to change your\n" ;
        $msgPlainText   .= "preferred message format from HTML to plain text.\n\n" ;
        if ($plainText == '') {
            $plainText   = strip_tags($html) ; 
        }
        $msgPlainText   .= $plainText . "\n" ;

        // HTML Version
        $msgHtml     = "--" . $boundary . "\n" ;
        $msgHtml    .= "Content-Type: text/html; charset=iso-8589-1\n" ;
        $msgHtml    .= "Content-Transfer-Encoding: 8bit\n" ;
        $msgHtml    .= $html ."\n"  ;

        // Attachment Version
        $attach      = "--" . $boundary ."\n" ;
        $attach     .= "Content-Type: " . $filetype . "; name=\"" . $file . "\"\n" ;
        $attach     .= "Content-Transfer-Encoding: base64 \n" ;
        // Need two end of lines
        $attach     .= "Content-Disposition: attachment; filename=\"" .$file. "\"\n\n" ;
        $attach     .= $data   . "\n\n" ;

        $message     = "Content-Type: multipart/alternative; boundary=\"".$boundary."\"";
        $message    .= $msgPlainText . $msgHtml . $attach ;
        // Send Email
        $this->send($message) ;
    }
    // }}}

    // {{{ send()

    /**
     * return function mail() 
     * @param   string  $message    Content for this email 
     * return function mail() 
     * @access  private
     * @access  static
     */

    private function send($message)
    {
        return @mail($this->to, $this->subject, $message, $this->header) ;
    }
    
    // }}}
}

// }}}
?>
