<?php
  /*
   * Created on 16 feb 2011
   * by Louck Sitskoorn
  */

  //NAMESPACE
  namespace SB\Functions;

  //USES

  //INCLUDES functions
  require_once __DIR__ . "/file_functions.php";
  require_once __DIR__ . "/string_functions.php";


    //CLASSES

  class XMLParseErrorException
  extends Exception {
    public function __construct($filename) {
      set_error_handler(array($this,"error_handler_xml"));

      $this->message .= "XML Parse Error" . PHP_EOL . PHP_EOL;

      if (is_string($filename)) {
        if (is_file($filename)) {
          $this->message .= "XML File: " . $filename . PHP_EOL;
        } else {
          $this->message .= "XML String: " . $filename . PHP_EOL;
        }
      }

      $dom = new DOMDocument();
      $dom->load($filename);

      //restore normal error handler
      restore_error_handler();

      parent::__construct();
    }


    public function error_handler_xml($errno, $errstr, $errfile, $errline) {
      $error    = libxml_get_last_error();
      $errmsg   = "";
      $errfile  = "";
      $errline  = "";
      $errcol   = "";
      $errinfo  = "";

      if ($error) {
        $errno          = $error->level;
        $errmsg         = $error->message;
        $errfile        = $error->file;
        $errline        = $error->line;
        $errcol         = $error->column;
        $errcallerinfo  = get_caller_info();
      }

      //get original message
      $pos = strpos($errstr,"]:") ;
      if ($pos) {
        $errstr = substr($errstr,$pos+ 2);
      }
      $errinfo = $errstr;

      //log error
      if (isset($_SESSION["project"]) && isset($_SESSION["project"]["error_logging"]) && $_SESSION["project"]["error_logging"])       {error_logger($errno, $errmsg, $errinfo, $errfile, $errline, $errcallerinfo);}
      if (isset($_SESSION["project"]) && isset($_SESSION["project"]["error_filing"])  && $_SESSION["project"]["error_filing"])        {error_filer($errno, $errmsg, $errinfo, $errfile, $errline, $errcallerinfo);}
      if (isset($_SESSION["project"]) && isset($_SESSION["project"]["error_mailing"]) && $_SESSION["project"]["error_mailing"])       {error_mailer($errno, $errmsg, $errinfo, $errfile, $errline, $errcallerinfo);}
      if (isset($_SESSION["project"]) && isset($_SESSION["project"]["error_displaying"]) && $_SESSION["project"]["error_displaying"]) {error_displayer($errno, $errmsg, $errinfo, $errfile, $errline, $errcallerinfo);}
    }
  }


  //FUNCTIONS
  function warning_handler($errno, $errstr) {
    fbb("WARNING!!! " . $errno , $errstr, get_caller_info());
  }


  function error_handler($errno=false, $errstr=false, $errfile=false, $errline=false, $errcontext=false, $errinfo=false) {
    global $_SESSION;

    //log
    //fbb($errno, $errstr, $errfile, $errline, $errcontext);

    //get last error
    $err            = error_get_last();
    $errcallerinfo  = get_caller_info();

    //display
    if ($errno
    || ($err && isset($err["type"]) && $err["type"]) ) {
      $errno    = ($err && isset($err["type"]) && $err["type"]) ? $err["type"] : $errno;
      $errstr   = ($err && isset($err["message"]) && $err["message"]) ? $err["message"] : $errstr;
      $errfile  = ($err && isset($err["file"]) && $err["file"]) ? $err["file"] : $errfile;
      $errline  = ($err && isset($err["line"]) && $err["line"]) ? $err["line"] : $errline;
    }

    if ($errno & ((isset($_SESSION["project"]) && isset($_SESSION["project"]["error_reporting_logging"])) ? $_SESSION["project"]["error_reporting_logging"] : error_reporting()) ) {
      //display error ?
      //if (isset($_SESSION["project"])
      //&&  $_SESSION["project"]["developer"]) {
      //  fbb("Error type:" . $errno . " message:" . $errstr . " file:" . $errfile . " line:" . $errline, get_caller_info());
      //}

      //log error
      if (isset($_SESSION["project"]) && isset($_SESSION["project"]["error_logging"]) && $_SESSION["project"]["error_logging"])       {error_logger($errno, $errstr, $errinfo, $errfile, $errline, $errcallerinfo);}
      if (isset($_SESSION["project"]) && isset($_SESSION["project"]["error_filing"])  && $_SESSION["project"]["error_filing"])        {error_filer($errno, $errstr, $errinfo, $errfile, $errline, $errcallerinfo);}
      if (isset($_SESSION["project"]) && isset($_SESSION["project"]["error_mailing"]) && $_SESSION["project"]["error_mailing"])       {error_mailer($errno, $errstr, $errinfo, $errfile, $errline, $errcallerinfo);}
      if (isset($_SESSION["project"]) && isset($_SESSION["project"]["error_displaying"]) && $_SESSION["project"]["error_displaying"]) {error_displayer($errno, $errstr, $errinfo, $errfile, $errline, $errcallerinfo);}

      //fatal error?
      if(isset($GLOBALS["error_fatal"])){
        if($GLOBALS["error_fatal"] & $err["type"]) {
          //die("Fatal error.");
        }
      }
    }

    return true;
  }


  function error_logger($errno, $errstr, $errinfo, $errfile, $errline, $errcallerinfo) {
    if($errno == 0) return;

    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);

    $errormessage   = "Error: " . $errno . PHP_EOL
                    . "Type: " . error_type($errno) . PHP_EOL
                    . "Message: " . $errstr . PHP_EOL
                    . "File: " . $errfile . PHP_EOL
                    . "Line: " . $errline . PHP_EOL
                    . "Session: " . session_id() . PHP_EOL
                    . PHP_EOL
                    . "Info: " . $errinfo . PHP_EOL
                    . PHP_EOL
                    . "Date :" . date("d-m-Y H:i") . PHP_EOL
                    . "Caller: " . $errcallerinfo . PHP_EOL
                    . PHP_EOL;

    //log to table
    $connection = new SB_Connection();
    if ($connection) {
      if (array_key_exists("project", $_SESSION)) {
        $connection->Server             = $_SESSION["project"]["database"]["server"];
        $connection->DatabaseName       = $_SESSION["project"]["database"]["databasename"];
        $connection->Driver             = $_SESSION["project"]["database"]["driver"];
        $connection->Type               = $_SESSION["project"]["database"]["type"];
        $connection->User               = $_SESSION["project"]["database"]["user"];
        $connection->Password           = $_SESSION["project"]["database"]["password"];

        $connection->connect();

        //log to database
        $logtableid   = logtext_totable_insert($connection->DB, NULL, NULL, "logs", "PHP", strtoupper(error_logtypeid($errno)), NULL, NULL, NULL, NULL, NULL, $errno != 0, $errno, $errormessage, 0);

        //display error logging ?
        if (isset($_SESSION["project"]["developer"])
        &&  $_SESSION["project"]["developer"]) {
          fbb("Logged to database:" . $connection->DatabaseName . " table:logs");
        }
      }
    }
  }


  function error_filer($errno, $errstr, $errinfo, $errfile, $errline, $errcallerinfo) {
    if($errno == 0) return;

    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);

    if ($errno == E_NOTICE
    ||  $errno == E_USER_NOTICE
    ||  $errno == E_STRICT) {
      $errorlogfile = "notices_php.log";
    } else if ($errno == E_WARNING
           ||  $errno == E_USER_WARNING
           ||  $errno == E_CORE_WARNING
           ||  $errno == E_COMPILE_WARNING) {
      $errorlogfile = "warnings_php.log";
    } else if ($errno == E_DEPRECATED
           ||  $errno == E_USER_DEPRECATED) {
      $errorlogfile = "deprecations_php.log";
    } else {
      $errorlogfile = "errors_php.log";
    }

    $errorlogstr  = PHP_EOL
                    . "Error: " . $errno . PHP_EOL
                    . "Type: " .error_type($errno) . PHP_EOL
                    . "Message:" .$errstr . PHP_EOL
                    . "File: " . $errfile . PHP_EOL
                    . "Line: " . $errline . PHP_EOL
                    . "Session: " . session_id() . PHP_EOL
                    . PHP_EOL
                    . "Info: " . $errinfo . PHP_EOL
                    . PHP_EOL
                    . "Date :" . date("d-m-Y H:i") . PHP_EOL
                    . "Caller: " .$errcallerinfo . PHP_EOL
                    . "-----------------------------------------------------" . PHP_EOL;

    //log to file
    $logfileid  = logtext_tofile(__DIR__ . "/../../temp/log/", $errorlogfile, $errorlogstr, "a+");

    //display error logging ?
    if (isset($_SESSION["project"]["developer"])
    &&  $_SESSION["project"]["developer"]) {
      fbb("Logged to file:" . __DIR__ . "/../../temp/log/" . $errorlogfile);
    }
  }


  function error_mailer($errno, $errstr, $errinfo, $errfile, $errline, $errcallerinfo) {
    if($errno == 0) return;

    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);

    $errormessage   = "Error: " . $errno . PHP_EOL
                    . "Type: " . error_type($errno) . PHP_EOL
                    . "Message: " . $errstr . PHP_EOL
                    . "File: " . $errfile . PHP_EOL
                    . "Line: " . $errline . PHP_EOL
                    . "Session: " . session_id() . PHP_EOL
                    . PHP_EOL
                    . "Info: " . $errinfo
                    . PHP_EOL
                    . "Date :" . date("d-m-Y H:i") . PHP_EOL
                    . "Caller: " . $errcallerinfo . PHP_EOL
                    . PHP_EOL;

    $name       = "Service Beheer " . $_SESSION["project"]["config"]["type"];
    $email      = $_SESSION["project"]["email_noreply"];
    $recipient  = $_SESSION["project"]["email_error"];
    $mail_body  = $errstr . "<br />Type: " . error_type($errno) . "<br />File: " . $errfile . "<br />Line: " . $errline ."<br />Info: " . $errinfo;
    $subject    = "Service Beheer - Error";
    $header     = "From: ". $name . " <" . $email . ">\r\n"; //optional headerfields

    //log to mail
    mail($recipient, $subject, $mail_body, $header); //mail command :)

    //display error logging ?
    if (isset($_SESSION["project"]["developer"])
    &&  $_SESSION["project"]["developer"]) {
      fbb("Logged to mail: " . $recipient);
    }
  }


  function error_displayer($errno, $errstr, $errinfo, $errfile, $errline, $errcallerinfo) {
    if($errno == 0) return;

    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);

    $errormessage   = "<br />"
                    . "Error: " . $errno . "<br />"
                    . "Type: " . error_type($errno) . "<br />"
                    . "Message: " . $errstr . "<br />"
                    . "File: " . $errfile . "<br />"
                    . "Line: " . $errline . "<br />"
                    . "Session: " . session_id() . "<br />"
                    . "<br />"
                    . "Info: " . $errinfo . "<br />"
                    . "<br />"
                    . "Date :" . date("d-m-Y H:i") . "<br />"
                    . "Caller: " . $errcallerinfo . "<br />"
                    . "<br />";

    echo $errormessage;
  }

  /*
  function error_mailer($errno, $errstr, $errinfo, $errfile, $errline) {
    //create mailer object
    $mailer = new PHPMailer();

    if ($mailer) {
      if (array_key_exists("current_smtp", $_SESSION)) {
        //mailer properties
        $mailer->Host     = $_SESSION["project"]["smtp"]["server"];
        $mailer->IsSMTP();
        $mailer->SMTPAuth = true;
        $mailer->SMTPDebug= false;
        $mailer->SMTPSecure = ($_SESSION["project"]["smtp"]["layer"]) ? $_SESSION["project"]["smtp"]["layer"] : "";
        $mailer->Port = ($_SESSION["project"]["smtp"]["port"]) ? $_SESSION["project"]["smtp"]["port"] : 25;
        $mailer->Username = $_SESSION["project"]["smtp"]["user"];
        $mailer->Password = $_SESSION["project"]["smtp"]["password"];
        $mailer->CharSet  = "UTF-8";

        //FROM
        $mailer->SetFrom($_SESSION["project"]["email_noreply"], "Service Beheer");
        $mailer->AddReplyTo($_SESSION["project"]["email_noreply"]);

        //SUBJECT
        $mailer->Subject  = "Service Beheer - Error";
        $mailer->AltBody  = "To view the message, please use an HTML compatible e-mail client.";
        $mailer->MsgHTML($errstr . "<br />Type: " . error_type($errno) . "<br />File: " . $errfile . "<br />Line: " . $errline ."<br />Info: " . $errinfo);

        //TO
        if (isnotempty($_SESSION["project"]["email_error"])) {
          //spaties en komma's omzetten in puntkomma's
          $to = $_SESSION["project"]["email_error"];
          $to = str_ireplace(" ", ";", $to);
          $to = str_ireplace(",", ";", $to);

          //geadresseerden bepalen
          $tos = explode(";", $to);
          foreach ($tos as $torecipient) {
            $mailer->AddAddress($torecipient);
          }
        }

        //CC
        //if (isnotempty($cc)) {
        //  $ccs = explode(";", $cc);
	      //  foreach ($ccs as $ccrecipient) {
        //    $mailer->AddCC($ccrecipient);
        //  }
        //}

        //BCC
        if (isnotempty($_SESSION["project"]["email_bcc"])) {
          $bccs = explode(";", $_SESSION["project"]["email_bcc"]);
          foreach ($bccs as $bccrecipient) {
            $mailer->AddBCC($bccrecipient);
	        }
        }

        //display error mailing?
        if (isset($_SESSION["project"]["developer"])
        &&  $_SESSION["project"]["developer"]) {
          fbb("Mail error to " . $to);
        }

        //SEND MAIL
        try {
          if (!isempty($to)) {
            $mailer->Send();
          }
        } catch (phpmailerException $e) {
          $resultset = array(
            "success"    => false,
            "message"    => "{$mailer->ErrorInfo}",
            "mailerid"   => $senderid
          );
        }
      }
    }
  }
  */

  function error_fatal($mask = NULL){
    if(!is_null($mask)){
        $GLOBALS['error_fatal'] = $mask;
    }elseif(!isset($GLOBALS['die_on'])){
        $GLOBALS['error_fatal'] = 0;
    }
    return $GLOBALS['error_fatal'];
  }


  function error_type($errno) {
    $returnvalue= "Unknown error";

    switch($errno){
        case E_ERROR:               $returnvalue="Error";                   break;
        case E_WARNING:             $returnvalue="Warning";                 break;
        case E_PARSE:               $returnvalue="Parse Error";             break;
        case E_NOTICE:              $returnvalue="Notice";                  break;
        case E_CORE_ERROR:          $returnvalue="Core Error";              break;
        case E_CORE_WARNING:        $returnvalue="Core Warning";            break;
        case E_COMPILE_ERROR:       $returnvalue="Compile Error";           break;
        case E_COMPILE_WARNING:     $returnvalue="Compile Warning";         break;
        case E_USER_ERROR:          $returnvalue="User Error";              break;
        case E_USER_WARNING:        $returnvalue="User Warning";            break;
        case E_USER_NOTICE:         $returnvalue="User Notice";             break;
        case E_STRICT:              $returnvalue="Strict Notice";           break;
        case E_RECOVERABLE_ERROR:   $returnvalue="Recoverable Error";       break;
        case E_DEPRECATED:          $returnvalue="Deprecated";              break;

        case LIBXML_ERR_WARNING:    $returnvalue="XML Warning";             break;
        case LIBXML_ERR_ERROR:      $returnvalue="XML Error";               break;
        case LIBXML_ERR_FATAL:      $returnvalue="XML FatalError";          break;

        default:                    $returnvalue="Unknown error ($errno)";  break;
    }

    return $returnvalue;
  }


  function error_logtypeid($errno) {
    $returnvalue= "Unknown error";

    switch($errno){
        case E_ERROR:               $returnvalue="ERROR";                   break;
        case E_WARNING:             $returnvalue="WARNING";                 break;
        case E_PARSE:               $returnvalue="ERROR";                   break;
        case E_NOTICE:              $returnvalue="NOTICE";                  break;
        case E_CORE_ERROR:          $returnvalue="ERROR";                   break;
        case E_CORE_WARNING:        $returnvalue="WARNING";                 break;
        case E_COMPILE_ERROR:       $returnvalue="ERROR";                   break;
        case E_COMPILE_WARNING:     $returnvalue="WARNING";                 break;
        case E_USER_ERROR:          $returnvalue="ERROR";                   break;
        case E_USER_WARNING:        $returnvalue="WARNING";                 break;
        case E_USER_NOTICE:         $returnvalue="NOTICE";                  break;
        case E_STRICT:              $returnvalue="NOTICE";                  break;
        case E_RECOVERABLE_ERROR:   $returnvalue="ERROR";                   break;
        case E_DEPRECATED:          $returnvalue="WARNING";                 break;

        case LIBXML_ERR_WARNING:    $returnvalue="WARNING";                 break;
        case LIBXML_ERR_ERROR:      $returnvalue="ERROR";                   break;
        case LIBXML_ERR_FATAL:      $returnvalue="ERROR";                   break;

        default:                    $returnvalue="ERROR";                   break;
    }

    return $returnvalue;
  }

  /*
  function error_handler($errno, $errstr, $errfile, $errline){
    $errno = $errno & error_reporting();
    if($errno == 0) return;

    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);

    print "<pre>\n<b>";
    switch($errno){
        case E_ERROR:               print "Error";                  break;
        case E_WARNING:             print "Warning";                break;
        case E_PARSE:               print "Parse Error";            break;
        case E_NOTICE:              print "Notice";                 break;
        case E_CORE_ERROR:          print "Core Error";             break;
        case E_CORE_WARNING:        print "Core Warning";           break;
        case E_COMPILE_ERROR:       print "Compile Error";          break;
        case E_COMPILE_WARNING:     print "Compile Warning";        break;
        case E_USER_ERROR:          print "User Error";             break;
        case E_USER_WARNING:        print "User Warning";           break;
        case E_USER_NOTICE:         print "User Notice";            break;
        case E_STRICT:              print "Strict Notice";          break;
        case E_RECOVERABLE_ERROR:   print "Recoverable Error";      break;
        default:                    print "Unknown error ($errno)"; break;
    }
    print ":</b> <i>$errstr</i> in <b>$errfile</b> on line <b>$errline</b>\n";
    if(function_exists('debug_backtrace')){
        //print "backtrace:\n";
        $backtrace = debug_backtrace();
        array_shift($backtrace);
        foreach($backtrace as $i=>$l){
            print "[$i] in function <b>{$l['class']}{$l['type']}{$l['function']}</b>";
            if($l['file']) print " in <b>{$l['file']}</b>";
            if($l['line']) print " on line <b>{$l['line']}</b>";
            print "\n";
        }
    }
    print "\n</pre>";
    if(isset($GLOBALS['error_fatal'])){
        if($GLOBALS['error_fatal'] & $errno) die('fatal');
    }
  }
  */


  /**
   * Check the syntax of some PHP code.
   * @param string $code PHP code to check.
   * @return boolean|array If false, then check was successful, otherwise an array(message,line) of errors is returned.
   */
  function error_php_syntax($code){
      $erlevel = error_reporting(0);

      $braces = 0;
      $inString = 0;

      // First of all, we need to know if braces are correctly balanced.
      // This is not trivial due to variable interpolation which
      // occurs in heredoc, backticked and double quoted strings
      foreach (token_get_all('<?php ' . $code) as $token)
      {
          if (is_array($token))
          {
              switch ($token[0])
              {
              case T_CURLY_OPEN:
              case T_DOLLAR_OPEN_CURLY_BRACES:
              case T_START_HEREDOC: ++$inString; break;
              case T_END_HEREDOC:   --$inString; break;
              }
          }
          else if ($inString & 1)
          {
              switch ($token)
              {
              case '`':
              case '"': --$inString; break;
              }
          }
          else
          {
              switch ($token)
              {
              case '`':
              case '"': ++$inString; break;

              case '{': ++$braces; break;
              case '}':
                  if ($inString) --$inString;
                  else
                  {
                      --$braces;
                      if ($braces < 0) break 2;
                  }

                  break;
              }
          }
      }

      // Display parse error messages and use output buffering to catch them
      $inString = @ini_set('log_errors', false);
      $token = @ini_set('display_errors', true);
      ob_start();

      // If $braces is not zero, then we are sure that $code is broken.
      // We run it anyway in order to catch the error message and line number.

      // Else, if $braces are correctly balanced, then we can safely put
      // $code in a dead code sandbox to prevent its execution.
      // Note that without this sandbox, a function or class declaration inside
      // $code could throw a "Cannot redeclare" fatal error.

      $braces || $code = "if(0){{$code}\n}";

      if (false === eval($code))
      {
          if ($braces) $braces = PHP_INT_MAX;
          else
          {
              // Get the maximum number of lines in $code to fix a border case
              false !== strpos($code, "\r") && $code = strtr(str_replace("\r\n", "\n", $code), "\r", "\n");
              $braces = substr_count($code, "\n");
          }

          $code = ob_get_clean();
          $code = strip_tags($code);

          // Get the error message and line number
          if (preg_match("'syntax error, (.+) in .+ on line (\d+)$'s", $code, $code))
          {
              $code[2] = (int) $code[2];
              $code = $code[2] <= $braces
                  ? array($code[1], $code[2])
                  : array('unexpected $end' . substr($code[1], 14), $braces);
          }
          else $code = array('syntax error', 0);
      }
      else
      {
          ob_end_clean();
          $code = false;
      }

      @ini_set('display_errors', $token);
      @ini_set('log_errors', $inString);

      error_reporting($erlevel);

      return $code;
  }

?>