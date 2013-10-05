<? 
  # script: ultramail.config.php 
   
  $MailBoxs = array(); 
       
  function AddMBox( $StName, 
                    $StEMail, 
                    $StUser, 
                    $StPassword, 
                    $StSMTPServer ) 
  { 
    global $MailBoxs; 

    $MailBoxs[] = 
      array 
      ( 
        'StName'       => $StName, 
        'StEMail'      => $StEMail, 
        'StUser'       => $StUser, 
        'StPassword'   => $StPassword, 
        'StSMTPServer' => $StSMTPServer 
      ); 
  }     

  AddMBox( 'TesteNet Suporte', 
           'testenet@nowakis.com', 
           'testenet@nowakis.com', 
           'teste112233', 
           'smtp.nowakis.com' ); 
            
# Caso deseje configure outros e-mails para envio a partir do site.            
#  AddMBox( 'Nome apresentado no e-mail',   
#           'segundo_email@mariliaonline.com',   
#           'segundo_email=mariliaonline.com',   
#           'senha_do_segundo_email', 
#           'smtp.mariliaonline.com' ); 
?> 
