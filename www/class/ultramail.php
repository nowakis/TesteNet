<? 
  # Script: ultramail.php 

  include_once('Mail.php'); 
   
  include_once('ultramail.config.php'); 
   
  $UltraMailError = ''; 
   
  function UltraMail( $to, 
                      $subject, 
                      $message, 
                      $additional_headers = '', 
                      $additional_parameters = '') 
  { 
    global $MailBoxs, $UltraMailError; 

    $UM_StFrom = ''; 

    ##---------------------------------------     
    ## Converte o HEADER de STRING para ARRAY 
    ##---------------------------------------     
    $Aux_headers = 
      str_replace("\r", '', $additional_headers); 
    $Aux_headers = split("\n", $Aux_headers); 
    $headers = array(); 
    foreach ($Aux_headers as $Aux_header) 
    { 
      if ( ereg( '^([^:]+):([^:]+)$', $Aux_header, $regs ) ) 
      { 
        $headers[ $regs[1] ] = $regs[2]; 
      } 
    } 
    ##---------------------------------------     

     
    ##--------------------------------------------------     
    ## Localiza os dados do MailBox que enviará o e-mail 
    ##--------------------------------------------------     
     
    # Caso o usuário não tenha configurado um FROM 
    # utilizar sempre o primeiro 
    if ( empty( $headers['From'] ) ) 
    { 
      if ( !Empty($MailBox['StName']) ) 
      { 
        $headers['From'] = 
          $MailBoxs[0]['StName'] . 
          " <{$MailBoxs[0]['StEMail']}>"; 
      } 
      else 
      { 
        $headers['From'] = $MailBoxs[0]['StEMail']; 
      } 
      $UM_StName     = $MailBoxs[0]['StName']; 
      $UM_StFrom     = $MailBoxs[0]['StEMail']; 
      $UM_StUser     = $MailBoxs[0]['StUser']; 
      $UM_StPassword = $MailBoxs[0]['StPassword']; 
      $UM_StServer   = $MailBoxs[0]['StSMTPServer']; 
    } 

    # Caso exista o FROM procurar os 
    # dados do MailBox do mesmo 
    else 
    { 
      if ( eregi( '<?([^<>, ]+\@[^<>, ]+)>?', 
                  $headers['From'], $regs ) ) 
      { 
        $EMailDeEnvio = $regs[1]; 
      } 
      else 
      { 
        print "E-mail inválido: $to<BR>\n"; 
        exit; 
      } 
     
      foreach ($MailBoxs as $MailBox) 
      { 
        if ($MailBox['StEMail'] == $EMailDeEnvio) 
        { 
          $UM_StName     = $MailBox['StName']; 
          $UM_StFrom     = $MailBox['StEMail']; 
          $UM_StUser     = $MailBox['StUser']; 
          $UM_StPassword = $MailBox['StPassword']; 
          $UM_StServer   = $MailBox['StSMTPServer']; 
        } 
      } 
       
      if ( Empty($UM_StFrom) ) 
      { 

        if ( !Empty($MailBox['StName']) ) 
        { 
          $headers['From'] = $MailBoxs[0]['StName'] . " <{$MailBoxs[0]['StEMail']}>"; 
        } 
        else 
        { 
          $headers['From'] = $MailBoxs[0]['StEMail']; 
        } 

        $UM_StName     = $MailBoxs[0]['StName']; 
        $UM_StFrom     = $MailBoxs[0]['StEMail']; 
        $UM_StUser     = $MailBoxs[0]['StUser']; 
        $UM_StPassword = $MailBoxs[0]['StPassword']; 
        $UM_StServer   = $MailBoxs[0]['StSMTPServer']; 
      } 
    }   
    ##--------------------------------------------------     
     

    ##------------------------------------------------ 
    ## Configura as varíaveis necessárias para o envio    
    ##------------------------------------------------ 
    $headers['To']      = $to; 
    $headers['Subject'] = $subject; 

    $recipients[0] = $to; 

    if ($headers['Cc']) 
    { 
      array_push($recipients, $headers['Cc']); 
    } 
       
    if ($headers['Bcc']) 
    { 
      array_push($recipients, $headers['Bcc']); 
    } 
       
    $params = 
      array ( 
        'auth' => true, # SMTP requer autenticação. 
        'host' => $UM_StServer, # Servidor SMTP 
        'username' => $UM_StUser, # Usuário do SMTP 
        'password' => $UM_StPassword # Senha do seu MailBox. 
      ); 
    ##------------------------------------------------ 


    ##------------------------------------ 
    ## Envio o e-mail de forma autenticada    
    ##------------------------------------ 

    # Define o método de envio. 
    # Queremos 'smtp'. OBRIGATÓRIO. 
    $mail_object =& Mail::factory('smtp', $params); 
    if (PEAR::IsError($mail_object)) 
    { 
      $UltraMailError = $mail_object->getMessage(); 
      return FALSE; 
    }    
     

    # Envia o email. Se não ocorrer erro retorna TRUE, 
    # caso contrário retorna um objeto PEAR_Error. 
    # Para ler a mensagem de erro use o método getMessage(). 
    $result = 
      $mail_object->send($recipients, $headers, $message); 
    if (PEAR::IsError($result)) 
    { 
      $UltraMailError = $result->getMessage(); 
      return FALSE; 
    }    
    else 
    { 
      return TRUE; 
    }    
    ##------------------------------------ 

  }   
?> 
