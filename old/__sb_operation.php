<?php
	/*
	* Created on 8 dec 2008
	* by Louck Sitskoorn
	*/

  //PARAMETERS:
  //operation             (string   - ADD/VIEW/EDIT/ etc.)
  //operationtitle        (string   - titel van bepaalde operaties bijv. 'Verwijderen')
  //operationmessage      (string   - boodschap voor bepaalde operaties)
  //operationdescription  (string   - beschrijving van bepaalde operaties)
  //operationinclude      (string   - file die ge-include moet worden)

  //connection            (object   - SB_ConnectionParameters)
  //sql                   (string   - SQL statement)
  //sqlfilename           (string   - SQL filename)
  //sqlpath               (string   - SQL path)
  //sqlcustompath         (string   - SQL custompath)
  //sqlcachepath          (string   - SQL cachepath)
  //queryclass            (string   - Query Classname)

  //tablename             (string)
  //fields                (string   - komma-separated)
  //primaryfieldname      (string)
  //primaryfieldvalue     (string)

  //amountfieldname       (string   - komma-separated)
  //customfieldname       (string   - komma-separated)
  //datefieldname         (string   - komma-separated)
  //datestartfieldname    (string   - komma-separated)
  //dateendfieldname      (string   - komma-separated)
  //displayfieldname      (string   - komma-separated)
  //distinctfieldname     (string   - komma-separated)
  //encryptfieldname      (string   - komma-separated)
  //groupingfieldname     (string   - komma-separated)
  //parentfieldname       (string   - komma-separated)
  //locationfieldname     (string   - komma-separated)
  //titlefieldname        (string   - komma-separated)
  //valuefieldname        (string   - komma-separated)

  //countmethod           (string   - methode om rowcount te berekenen (calc/count)
  //duplicate             (boolean  - default false)
  //evaluate              (boolean  - default false)
  //encrypt               (boolean  - default false)
  //manipulate            (boolean  - default false)
  //nocache               (boolean  - default false)
  //replacefieldname      (string   - velden die in resultarray vervangen moeten worden)
  //replaceresult         (boolean  - default true)
  //replaceresultfunction (string   - custom result replace functienaam)
  //splitprimary          (boolean  - true als query met sqlparts werkt (  /*[SQLStart]*/ etc. )
  //timing                (boolean  - default false)
  //start                 (integer)
  //limit                 (integer)
  //sortfieldname         (string   - sorteer veldnaam)
  //sortdirection         (string   - sorteer richting (ASC/DESC))
  //page                  (integer  - default  0)
  //rowcount              (integer  - default -1)
  //templateclass         (string   - class van template )
  //templatecustompath    (string   - custom path van template )
  //templatefilename      (string   - filename van template )
  //templaterootpath      (string   - rootpath van template )
  //templateprefix        (string   - prefix van template )
  //templatesuffix        (string   - prefix van template )
  //uploaddir             (string   - upload-directory voor file uploads)
  //resulttype            (string   - default 'json')
  //resultformat          (string   - default 'record')
  //formload              (boolean  - true als form ingeladen wordt (vereist andere JSON))
  //treeload              (boolean  - true als tree ingeladen wordt (vereist andere JSON))
  //xmlsubmit             (object   - simplexml object van gesubmitte velden
  //xmlselection          (object   - simplexml object van selectie velden)
  //xmlsearch             (object   - simplexml object van search velden)
  //xmldefault            (object   - simplexml object van default waardes)
  //xmlcolumns            (object   - simplexml object van grid column structuur)
  //values                (array    - array van bepaalde values uit het template)
  //params                (array    - array van bepaalde params waarmee het template werd opgeroepen)
  //title                 (string)
  //url                   (string)

  //logging               (boolean  - true als queries gelogd moeten worden)
  //logfilename           (string   - log filename)
  //logpath               (string   - log path)
  //logtablename          (string   - log tablename)

  //server                (string   - mail server)
  //port                  (number   - mail server port)
  //layer                 (string   - mail transport layer)
  //username              (string   - mail username)
  //password              (string   - mail password)
  //subject               (string   - mail subject)
  //from                  (string   - mail from emailadressen)
  //to                    (string   - mail to emailadressen)
  //replyto               (string   - mail replyto emailadressen)
  //cc                    (string   - mail cc emailadressen)
  //bcc                   (string   - mail bcc emailadressen)

  //startdate             (integer  - calendar startdate (unix timestamp) )
  //enddate               (integer  - calendar enddate (unix timestamp) )

  //containerid           (string)
  //height                (integer)
  //width                 (integer)
  //filename              (string)
  //filecontent           (string)

  //session start
  session_start();

  header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i ") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");

  //framework includes
  include_once __DIR__ . "/classes/sb/__sb_framework.php";
  include_once __DIR__ . "/classes/sb/__sb_query.php";
  include_once __DIR__ . "/classes/sb/__sb_operationquery.php";

  //function includes
  include_once __DIR__ . "/functions/_php_functions.php";
  include_once __DIR__ . "/functions/_string_functions.php";
  include_once __DIR__ . "/functions/_eval_functions.php";
  include_once __DIR__ . "/functions/_error_functions.php";
  include_once __DIR__ . "/functions/_date_functions.php";
  include_once __DIR__ . "/functions/_file_functions.php";
  include_once __DIR__ . "/functions/_debug_functions.php";
  include_once __DIR__ . "/functions/_log_functions.php";
  include_once __DIR__ . "/functions/_array_functions.php";
  include_once __DIR__ . "/functions/_json_functions.php";
  include_once __DIR__ . "/functions/_encryption_functions.php";
  include_once __DIR__ . "/functions/_replace_functions.php";

  //if ($_SESSION["vsid"]) {
    //timer
    $mtime = microtime();
    $mtime = explode(' ', $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $totaltimer = $mtime;

    //In PHP 5.2 or higher we don"t need to bring this in
    if (!function_exists("json_encode")) {
      require_once dirname(__FILE__) . "/functions/_json_wrappers.php";
    }

    //error reporting
    //error_reporting($_SESSION["error_reporting"]);
    //error_reporting(0);
    //error_fatal($_SESSION["error_fatal"]);
    set_error_handler('error_handler');

    //initialization parameters
    $operation            = "";
    $operationinclude     = "";
    $operationtitle       = "";
    $operationmessage     = "";
    $connectionparameters = null;
    $sql                  = "";
    $sqlfilename          = "";
    $sqlpath              = "";
    $sqlcustompath        = "";
    $sqlcachepath         = "";
    $tablename            = "";
    $fields               = "*";
    $queryclass           = "SB_OperationQuery";
    $primaryfieldname     = "";
    $primaryfieldvalue    = "";
    $primaryfieldvalues   = array();
    $customfieldname      = "";
    $distinctfieldname    = "";
    $parentfieldname      = "";
    $amountfieldname      = "";
    $datefieldname        = "";
    $datestartfieldname   = "";
    $dateendfieldname     = "";
    $displayfieldname     = "";
    $encryptfieldname     = "";
    $groupingfieldname    = "";
    $locationfieldname    = "";
    $titlefieldname       = "";
    $valuefieldname       = "";
    $countmethod          = "calc";
    $duplicate            = false;
    $encrypt              = false;
    $evaluate             = false;
    $manipulate           = false;
	  $nocache              = false;
	  $replacefieldname     = "";
	  $replaceresult        = true;
	  $replaceresultfunction= "";
	  $splitprimary         = false;
    $timing               = false;
	  $start                = 0;
	  $limit                = 0;
	  $sortfieldname        = "";
	  $sortdirection        = "";
	  $page                 = 0;
	  $rowcount             = -1;
	  $templateclass        = "SB_XMLTemplate";
	  $templatefilename     = "";
	  $templaterootpath     = "";
	  $templatecustompath   = "";
	  $templatecachepath    = "";
	  $templatetemppath     = "";
	  $templateprefix       = "";
	  $templatesuffix       = "";
	  $uploaddir            = "";
	  $resulttype           = "json";
	  $resultformat         = "grid";
    $resultload           = false;
    $resultsave           = false;
    $resultfilename       = "";
	  $formload             = false;
	  $treeload             = false;
	  $columnsonly          = false;
	  $xmlrequest           = false;
	  $xmlsubmit            = false;
	  $xmlselection         = false;
	  $xmlsearch            = false;
	  $xmldefault           = false;
	  $values               = array();
	  $params               = array();
    $senderid             = "";
    $senderjsid           = "";
    $senderclass          = "";
	  $title                = "";
	  $url                  = "";

	  $logging              = $_SESSION["project"]["logging"];
	  $logfilename          = "";
	  $logpath              = dirname(__FILE__) . "/../temp/log/" . date("Ymd", time());
	  $logtablename         = "logs";

	  $server               = "";
	  $username             = "";
	  $password             = "";
	  //$xmltemplate          = "";
	  //$htmltemplate         = "";
	  $subject              = "";
	  $from                 = "";
	  $fromname             = "";
	  $to                   = "";
	  $replyto              = "";
	  $cc                   = "";
	  $bcc                  = "";

    $containerid          = "";
	  $height               = 250;
	  $width                = 750;
    $filename             = "";
    $filecontent          = "";

	  $organisatiepath      = "";

	  //private variables
	  $sqlstart             = "";
	  $sqlselect            = "";
	  $sqlfrom              = "";
	  $sqlwhere             = "";
	  $sqlend               = "";
	  $sqlorder             = "";
	  $sqlnolimit           = "";
	  $xmlresult            = false;
	  $json                 = "";

    //parameters valideren
    foreach ($_REQUEST as $key=>$value) {
      //$_REQUEST[$key] = safesqlstring($value);
    }

    //parameters inlezen
    $operation            = (isset($_REQUEST["operation"])) ? $_REQUEST["operation"] : "VIEW";
    $operationinclude     = (isset($_REQUEST["operationinclude"])) ? $_REQUEST["operationinclude"] : $operationinclude;
    $operationtitle       = (isset($_REQUEST["operationtitle"])) ? $_REQUEST["operationtitle"] : $operationtitle;
    $operationmessage     = (isset($_REQUEST["operationmessage"])) ? $_REQUEST["operationmessage"] : $operationmessage;

    $manipulator          = (isset($_REQUEST["manipulator"])) ? $_REQUEST["manipulator"] :$manipulator;
    $manipulatortype      = (isset($_REQUEST["manipulatortype"])) ? $_REQUEST["manipulatortype"] : "fields";

    $connectionparameters = (isset($_REQUEST["connection"])) ? jitReplace(unserialize(cryptConvertCheck($_REQUEST["connection"], "crid", false))) : $connectionparameters;
    $sql                  = (isset($_REQUEST["sql"])) ? cryptConvertCheck($_REQUEST["sql"], "crid", false) : $sql;
    $sqlfilename          = (isset($_REQUEST["sqlfilename"])) ? cryptConvertCheck($_REQUEST["sqlfilename"], "crid", false) :$sqlfilename;
    $sqlpath              = (isset($_REQUEST["sqlpath"])) ? cryptConvertCheck($_REQUEST["sqlpath"], "", false) : $sqlpath;
    $sqlcustompath        = (isset($_REQUEST["sqlcustompath"])) ? cryptConvertCheck($_REQUEST["sqlcustompath"], "crid", false) : $sqlcustompath;
    $sqlcachepath         = (isset($_REQUEST["sqlcachepath"])) ? cryptConvertCheck($_REQUEST["sqlcachepath"], "crid", false) : $sqlcachepath;
    $tablename            = (isset($_REQUEST["tablename"])) ? $_REQUEST["tablename"] : $tablename;
    $fields               = (isset($_REQUEST["fields"])) ? str_ireplace('"', '', $_REQUEST["fields"]) : $fields;
    $primaryfieldname     = (isset($_REQUEST["primaryfieldname"])) ? $_REQUEST["primaryfieldname"] : $primaryfieldname;
    $primaryfieldvalue    = (isset($_REQUEST["primaryfieldvalue"])) ? $_REQUEST["primaryfieldvalue"] : $primaryfieldvalue;
    $queryclass           = (isset($_REQUEST["queryclass"])) ? $_REQUEST["queryclass"] : $queryclass;

    $start                = (isset($_REQUEST["start"])) ? $_REQUEST["start"] : $start;
    $limit                = (isset($_REQUEST["limit"])) ? $_REQUEST["limit"] : $limit;
    $sortfieldname        = (isset($_REQUEST["sortfieldname"])) ? $_REQUEST["sortfieldname"] : $sortfieldname;
    $sortdirection        = (isset($_REQUEST["sortdirection"])) ? $_REQUEST["sortdirection"] : $sortdirection;
    $page                 = (isset($_REQUEST["page"])) ? $_REQUEST["page"] : $page;
    $rowcount             = (isset($_REQUEST["rowcount"])) ? $_REQUEST["rowcount"] : $rowcount;

    $amountfieldname      = (isset($_REQUEST["amountfieldname"])) ? $_REQUEST["amountfieldname"] : $amountfieldname;
    $customfieldname      = (isset($_REQUEST["customfieldname"])) ? $_REQUEST["customfieldname"] : $customfieldname;
    $datefieldname        = (isset($_REQUEST["datefieldname"])) ? $_REQUEST["datefieldname"] : $datefieldname;
    $datestartfieldname   = (isset($_REQUEST["datestartfieldname"])) ? $_REQUEST["datestartfieldname"] : $datestartfieldname;
    $dateendfieldname     = (isset($_REQUEST["dateendfieldname"])) ? $_REQUEST["dateendfieldname"] : $dateendfieldname;
    $displayfieldname     = (isset($_REQUEST["displayfieldname"])) ? $_REQUEST["displayfieldname"] : $displayfieldname;
    $distinctfieldname    = (isset($_REQUEST["distinctfieldname"])) ? $_REQUEST["distinctfieldname"] : $distinctfieldname;
    $encryptfieldname     = (isset($_REQUEST["encryptfieldname"])) ? $_REQUEST["encryptfieldname"] : $encryptfieldname;
    $groupingfieldname    = (isset($_REQUEST["groupingfieldname"])) ? $_REQUEST["groupingfieldname"] : $groupingfieldname;
    $locationfieldname    = (isset($_REQUEST["locationfieldname"])) ? $_REQUEST["locationfieldname"] : $locationfieldname;
    $parentfieldname      = (isset($_REQUEST["parentfieldname"])) ? $_REQUEST["parentfieldname"] : $parentfieldname;
    $titlefieldname       = (isset($_REQUEST["titlefieldname"])) ? $_REQUEST["titlefieldname"] : $titlefieldname;
    $valuefieldname       = (isset($_REQUEST["valuefieldname"])) ? $_REQUEST["valuefieldname"] : $valuefieldname;

    $countmethod          = (isset($_REQUEST["countmethod"])) ? $_REQUEST["countmethod"] : $countmethod;

    $duplicate            = (isset($_REQUEST["duplicate"])) ? strtobool2($_REQUEST["duplicate"]) : $duplicate;
    $encrypt              = (isset($_REQUEST["encrypt"])) ? strtobool2($_REQUEST["encrypt"]) : $encrypt;
    $evaluate             = (isset($_REQUEST["evaluate"])) ? strtobool2($_REQUEST["evaluate"]) : $evaluate;
    $manipulate           = (isset($_REQUEST["manipulate"])) ? strtobool2($_REQUEST["manipulate"]) : $manipulate;
    $nocache              = (isset($_REQUEST["nocache"])) ? strtobool2($_REQUEST["nocache"]) : $nocache;
    $splitprimary         = (isset($_REQUEST["splitprimary"])) ? strtobool2($_REQUEST["splitprimary"]) : $splitprimary;
    $replaceresult        = (isset($_REQUEST["replaceresult"])) ? strtobool2($_REQUEST["replaceresult"]) : $replaceresult;
    $replacefieldname     = (isset($_REQUEST["replacefieldname"])) ? str_ireplace('"', '', $_REQUEST["replacefieldname"]) : $replacefieldname;
    $replaceresultfunction= (isset($_REQUEST["replaceresultfunction"])) ? $_REQUEST["replaceresultfunction"] : $replaceresultfunction;
    $timing               = (isset($_REQUEST["timing"])) ? strtobool2($_REQUEST["timing"]) : $timing;

    $senderid             = (isset($_REQUEST["senderid"])) ? $_REQUEST["senderid"] : $senderid;
    $senderjsid           = (isset($_REQUEST["senderjsid"])) ? $_REQUEST["senderjsid"] : $senderjsid;
    $senderclass          = (isset($_REQUEST["senderclass"])) ? $_REQUEST["senderclass"] : $senderclass;

    $templatefilename     = (isset($_REQUEST["templatefilename"])) ? $_REQUEST["templatefilename"] : $templatefilename;
    $templaterootpath     = (isset($_REQUEST["templaterootpath"])) ? $_REQUEST["templaterootpath"] : $templaterootpath;
    $templatecustompath   = (isset($_REQUEST["templatecustompath"])) ? $_REQUEST["templatecustompath"] : $templatecustompath;
    $templatecachepath    = (isset($_REQUEST["templatecachepath"])) ? $_REQUEST["templatecachepath"] : $templatecachepath;
    $templatetemppath     = (isset($_REQUEST["templatetemppath"])) ? $_REQUEST["templatetemppath"] : $templatetemppath;
    $templateclass        = (isset($_REQUEST["templateclass"])) ? $_REQUEST["templateclass"] : $templateclass;
    $templateprefix       = (isset($_REQUEST["templateprefix"])) ? $_REQUEST["templateprefix"] : $templateprefix;
    $templatesuffix       = (isset($_REQUEST["templatesuffix"])) ? $_REQUEST["templatesuffix"] : $templatesuffix;

    $uploaddir            = (isset($_REQUEST["uploaddir"])) ? $_REQUEST["uploaddir"] : $uploaddir;

    $resulttype           = (isset($_REQUEST["resulttype"])) ? $_REQUEST["resulttype"] : $resulttype;
    $resultformat         = (isset($_REQUEST["resultformat"])) ? $_REQUEST["resultformat"] : $resultformat;
    $resultload           = (isset($_REQUEST["resultload"])) ? strtobool2($_REQUEST["resultload"]) : $resultload;
    $resultsave           = (isset($_REQUEST["resultsave"])) ? strtobool2($_REQUEST["resultsave"]) : $resultsave;
    $resultfilename       = (isset($_REQUEST["resultfilename"])) ? $_REQUEST["resultfilename"] : $resultfilename;

    $formload             = (isset($_REQUEST["formload"])) ? strtobool2($_REQUEST["formload"]) : $formload;
    $treeload             = (isset($_REQUEST["treeload"])) ? strtobool2($_REQUEST["treeload"]) : $treeload;

    $columnsonly          = (isset($_REQUEST["columnsonly"])) ? strtobool2($_REQUEST["columnsonly"]) : $columnsonly;

    $values               = (isset($_REQUEST["values"])) ? (unserialize(urldecode($_REQUEST["values"]))) ? unserialize(urldecode($_REQUEST["values"])) : explode_assoc(";", $_REQUEST["values"]) : $values;
    $params               = (isset($_REQUEST["params"])) ? (unserialize(urldecode($_REQUEST["params"]))) ? unserialize(urldecode($_REQUEST["params"])) : explode_assoc(";", $_REQUEST["params"]) : $params;

    $title                = (isset($_REQUEST["title"])) ? $_REQUEST["title"] : $title;
    $url                  = (isset($_REQUEST["url"])) ? $_REQUEST["url"] : $url;

    //XML Parameters
    if (isset($_REQUEST["xmlsubmit"])) {
      if (isnotempty($_REQUEST["xmlsubmit"]) && strtolower($_REQUEST["xmlsubmit"]) != "false" && strtolower($_REQUEST["xmlsubmit"]) != "undefined") {
        /*$xmlsubmit =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".utf8_encode($_REQUEST["xmlsubmit"]));*/
	      /*$xmlsubmit =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlsubmit"]);*/
	      $xmlsubmit =  simplexml_load_string($_REQUEST["xmlsubmit"]);
	    }
	  }
	  if (isset($_REQUEST["xmlrequest"])) {
	    if (isnotempty($_REQUEST["xmlrequest"]) && strtolower($_REQUEST["xmlrequest"]) != "false" && strtolower($_REQUEST["xmlrequest"]) != "undefined") {
	      /*$xmlrequest =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".sb_utf8_encode($_REQUEST["xmlrequest"]));*/
	      $xmlrequest =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlrequest"]);
	    }
	  }
	  if (isset($_REQUEST["xmlselection"])) {
	    if (isnotempty($_REQUEST["xmlselection"]) && strtolower($_REQUEST["xmlselection"]) != "false" && strtolower($_REQUEST["xmlselection"]) != "undefined") {
	      /*$xmlselection =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".sb_utf8_encode($_REQUEST["xmlselection"]));*/
	      $xmlselection =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlselection"]);
	    }
	  }
	  if (isset($_REQUEST["xmlsearch"])) {
	    if (isnotempty($_REQUEST["xmlsearch"]) && strtolower($_REQUEST["xmlsearch"]) != "false" && strtolower($_REQUEST["xmlsearch"]) != "undefined") {
	      /*$xmlsearch =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".sb_utf8_encode($_REQUEST["xmlsearch"]));*/
	      $xmlsearch =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlsearch"]);
	    }
	  }
	  if (isset($_REQUEST["xmldefault"])) {
	    if (isnotempty($_REQUEST["xmldefault"]) && strtolower($_REQUEST["xmldefault"]) != "false" && strtolower($_REQUEST["xmldefault"]) != "undefined") {
	      /*$xmldefault =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".sb_utf8_encode($_REQUEST["xmldefault"]));*/
	      $xmldefault =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmldefault"]);
	    }
	  }
	  if (isset($_REQUEST["xmlcolumns"])) {
	    if (isnotempty($_REQUEST["xmlcolumns"]) && strtolower($_REQUEST["xmlcolumns"]) != "false" && strtolower($_REQUEST["xmlcolumns"]) != "undefined") {
	      /*$xmlcolumns =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".sb_utf8_encode($_REQUEST["xmlcolumns"]));*/
	      $xmlcolumns =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlcolumns"]);
	    }
	  }
	  if (isset($_REQUEST["xmlresult"])) {
	    if (isnotempty($_REQUEST["xmlresult"]) && strtolower($_REQUEST["xmlresult"]) != "false" && strtolower($_REQUEST["xmlresult"]) != "undefined") {
	      /*$xmlresult =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".sb_utf8_encode($_REQUEST["xmlresult"]));*/
	      $xmlresult =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlresult"]);
	    }
	  }

    //Logging parameters
    $logging               = (isset($_REQUEST["logging"])) ? strtobool2($_REQUEST["logging"]) : $logging;
    $logfilename           = (isset($_REQUEST["logfilename"])) ? $_REQUEST["logfilename"] : $logfilename;
    $logpath               = (isset($_REQUEST["logpath"])) ? $_REQUEST["logpath"] : $logpath;
    $logtablename          = (isset($_REQUEST["logtablename"])) ? $_REQUEST["logtablename"] : $logtablename;

    //Mail Parameters
    $server                = (isset($_REQUEST["server"]) && !is_empty($_REQUEST["server"])) ? jitReplace(cryptConvertCheck($_REQUEST["server"])) : $server;
    $username              = (isset($_REQUEST["username"]) &  !is_empty($_REQUEST["username"])) ? jitReplace(cryptConvertCheck($_REQUEST["username"])) : $username;
    $password              = (isset($_REQUEST["password"]) && !is_empty($_REQUEST["password"])) ? jitReplace(cryptConvertCheck($_REQUEST["password"])) : $password;
    $attachment            = (isset($_REQUEST["attachment"]) && !is_empty($_REQUEST["attachment"])) ? $_REQUEST["attachment"] : $attachment;
    $original_subject      = (isset($_REQUEST["subject"]) && !is_empty($_REQUEST["subject"])) ? jitReplace($_REQUEST["subject"]) : $subject;
    $original_from         = (isset($_REQUEST["from"]) && !is_empty($_REQUEST["from"])) ? jitReplace($_REQUEST["from"]) : $from;
    $original_fromname     = (isset($_REQUEST["fromname"]) && !is_empty($_REQUEST["fromname"])) ? jitReplace($_REQUEST["fromname"]) : $fromname;
    $original_to           = (isset($_REQUEST["to"]) && !is_empty($_REQUEST["to"])) ? jitReplace($_REQUEST["to"]) : $to;
    $original_replyto      = (isset($_REQUEST["replyto"]) && !is_empty($_REQUEST["replyto"])) ? jitReplace($_REQUEST["replyto"]) : $replyto;
    $original_cc           = (isset($_REQUEST["cc"]) && !is_empty($_REQUEST["cc"])) ? jitReplace($_REQUEST["cc"]) : $cc;
    $original_bcc          = (isset($_REQUEST["bcc"]) && !is_empty($_REQUEST["bcc"])) ? jitReplace($_REQUEST["bcc"]) : $bcc;

    $startdate             = (isset($_REQUEST["startdate"]) && !is_empty($_REQUEST["startdate"])) ? (int)$_REQUEST["startdate"] : $startdate;
    $enddate               = (isset($_REQUEST["enddate"]) && !is_empty($_REQUEST["enddate"])) ? (int)$_REQUEST["enddate"] : $enddate;

    $containerid           = (isset($_REQUEST["containerid"]) && !is_empty($_REQUEST["containerid"])) ? $_REQUEST["containerid"] : $containerid;
    $height                = (isset($_REQUEST["height"]) && !is_empty($_REQUEST["height"])) ? (int)$_REQUEST["height"] : $height;
    $width                 = (isset($_REQUEST["width"]) && !is_empty($_REQUEST["width"])) ? (int)$_REQUEST["width"] : $width;

    $filename              = (isset($_REQUEST["filename"])) ? cryptConvertCheck($_REQUEST["filename"]) : $filename;
    $filecontent           = (isset($_REQUEST["filecontent"])) ? $_REQUEST["filecontent"] : $filecontent;

    //HEADERS
    if ($resulttype == "json") {
      header('Content-type: application/json; charset=UTF-8');
    } else {
      header('Content-Type: text/html; charset=UTF-8');
    }

    //INCLUDE operationinclude
    if (isnotempty($operationinclude)) {
      $operationincludes = multi_explode(",;", $operationinclude);

      foreach ($operationincludes as $key => $operationincludefilename) {
        include_once __DIR__ . "/../" . stripfirstslash($operationincludefilename);
      }
    }

    //xmldefaults gebruiken?
	  if ($operation == "EDIT"
	  ||  $operation == "VIEW"
	  ||  $operation == "FILL"
	  ||  $operation == "FILLGRID"
	  ||  $operation == "FILLTREE"
	  ||  $operation == "FILLCOMBO") {
	    $xmldefault = false;
	  }


    //primaryfieldvalue
    if (!$primaryfieldvalue
    ||   $primaryfieldvalue == ""
    ||   $primaryfieldvalue == "null"
    ||   $primaryfieldvalue == "{null}") {
      if ($xmlresult) {
        $primaryfieldvalues = $xmlresult->xpath('/xml/record/field[@fieldname="' . $primaryfieldname . '"]');
        if (count($primaryfieldvalues) > 0 ) {
          $primaryfieldvalue  = (string)$primaryfieldvalues[0];
        }
      }
    }


    //operationquery aanmaken
    $query = new $queryclass();

    //query parameters zetten adhv $_REQUEST parameters
    $query->AmountFieldname       = $amountfieldname;
    $query->ColumnsOnly           = $columnsonly;
    $query->ConnectionParameters  = $connectionparameters;
    $query->CustomFieldname       = $customfieldname;
    $query->CountMethod           = $countmethod;
    $query->DistinctFieldname     = $distinctfieldname;
    $query->Duplicate             = $duplicate;
    $query->Encrypt               = $encrypt;
    $query->EncryptFieldname      = $encryptfieldname;
    $query->Evaluate              = $evaluate;
    $query->Fields                = $fields;
    $query->FormLoad              = $formload;
    $query->Limit                 = $limit;
    $query->Logging               = $logging;
    $query->LoggingPath           = $logpath;
    $query->LoggingFilename       = $logfilename;
    $query->LoggingTablename      = $logtablename;
    $query->Manipulate            = $manipulate;
    $query->Manipulator           = $manipulator;
    $query->ManipulatorType       = $manipulatortype;
    $query->NoCache               = $nocache;
    $query->Operation             = $operation;
    $query->Page                  = $page;
    $query->Params                = $params;
    $query->ParentFieldname       = $parentfieldname;
    $query->PrimaryFieldName      = $primaryfieldname;
    $query->PrimaryFieldValue     = $primaryfieldvalue;
    $query->ReplaceFieldname      = $replacefieldname;
    $query->ReplaceResult         = $replaceresult;
    $query->ReplaceResultFunction = $replaceresultfunction;
    $query->Request               = $_REQUEST;
    $query->ResultFilename        = $resultfilename;
    $query->ResultFormat          = $resultformat;
    $query->ResultLoad            = $resultload;
    $query->ResultSave            = $resultsave;
    $query->ResultType            = $resulttype;
    $query->RowCount              = $rowcount;
    $query->Session               = $_SESSION;
    $query->SortFieldname         = $sortfieldname;
    $query->SortDirection         = $sortdirection;
    $query->SplitPrimary          = $splitprimary;
    $query->SQL                   = $sql;
    $query->SQLCachePath          = $sqlcachepath;
    $query->SQLCustomPath         = $sqlcustompath;
    $query->SQLFileName           = $sqlfilename;
    $query->SQLPath               = $sqlpath;
    $query->Start                 = $start;
    $query->TableName             = $tablename;
    $query->Timing                = $timing;
    $query->Values                = $values;
    $query->XMLColumns            = $xmlcolumns;
    $query->XMLRequest            = $xmlrequest;
    $query->XMLSubmit             = $xmlsubmit;
    $query->XMLSearch             = $xmlsearch;
    $query->XMLSelection          = $xmlselection;
    $query->XMLResult             = $xmlresult;

    //query initialiseren
    $query->init();

    //QUERY INTERPRETEREN EN UITVOEREN  (HOEFT NIET BIJ EEN ADD OPERATION!!)
    if ($operation != "ADD"
    &&  $operation != "SUBMIT"
    &&  $operation != "DELETE"
    &&  $operation != "DISABLE"
    &&  $operation != "EXPORT"
    &&  $operation != "EXPORTOLD"
    &&  $operation != "TEMPLATE") {
      //query uitvoeren
      $query->execute();

      //result bewerken
      if ($operation == "COPY") {
        foreach ($query->Result as $key=>$row) {
          $query->Result[$key][$primaryfieldname] = null;
          $query->ResultArray[$key][$primaryfieldname] = null;
        }
      }

      //result bewaren
      $result       = $query->Result;
      $resultarray  = $query->ResultArray;
      $rowcount     = $query->RowCount;
    }


    //organisatiepath
    if ($xmlsubmit) {
      $organisatiepath = $xmlsubmit->xpath('/xml/record/field[@fieldname="OrganisatieDirectory"]');

      if (count($organisatiepath) >= 1) {
        $organisatiepath = (string)$organisatiepath[0];
      } else {
        $organisatiepath = $_SESSION["project"]["organisatiedirectory"];
      }
    } else {
      if (is_array($resultarray)) {
        //TODO: Dit gaat mis bij eventuele multi-select
        $organisatiepath = $resultarray[0]["OrganisatieDirectory"];
      }

      if (isempty($organisatiepath)) {
        $organisatiepath = $_SESSION["project"]["organisatiedirectory"];
      }
    }

    //grouping fields
    if (isnotempty($groupingfieldname)) {
      $groupingfieldnames  = explode(",", $groupingfieldname);
    }

    //INCLUDE operationinclude
    if ($operation == "PRINTITEM") {
      if (isnotempty($operationinclude)) {
        $operationincludes = explode(",", $operationinclude);
        foreach ($operationincludes as $key => $operationincludefilename) {
          include __DIR__ . "/../" . stripfirstslash($operationincludefilename);
        }
      }
    }

    //INCLUDE file adhv operation
	  if ($operation == "ADD"
	  ||  $operation == "EDIT"
	  ||  $operation == "VIEW"
	  ||  $operation == "STACK"
	  ||  $operation == "COPY"
	  ||  $operation == "FILL"
	  ||  $operation == "FILLGRID"
	  ||  $operation == "FILLTREE"
    ||  $operation == "FILLCHART"
	  ||  $operation == "FILLCALENDAR"
	  ||  $operation == "FILLCOMBO") {
	    //database (JSON) actie
	    include_once  dirname(__FILE__) . "/__sb_operation_select.php";

	  } elseif ($operation == "SUBMIT") {
	    //submit actie
	    include_once  dirname(__FILE__) . "/__sb_operation_submit.php";

	  } elseif ($operation == "DELETE") {
	    //delete actie
	    include_once  dirname(__FILE__) . "/__sb_operation_delete.php";

	  } elseif ($operation == "DISABLE") {
	    //disable actie
	    include_once  dirname(__FILE__) . "/__sb_operation_disable.php";

	  } elseif ($operation == "TEMPLATE") {
	    //print actie
	    include_once  dirname(__FILE__) . "/__sb_operation_template.php";

	  } elseif ($operation == "PRINTITEM") {
	    //print actie
	    include_once  dirname(__FILE__) . "/__sb_operation_printitem.php";

	  } elseif ($operation == "PRINTCHART") {
	    //print actie
	    include_once  dirname(__FILE__) . "/__sb_operation_printchart.php";

    } elseif ($operation == "PRINTLIST") {
      //print actie
      include_once  dirname(__FILE__) . "/__sb_operation_printlist_html.php";

	  } elseif ($operation == "PRINTLISTOLD") {
	    //print actie
	    include_once  dirname(__FILE__) . "/__sb_operation_printlist.php";

	  } elseif ($operation == "MAIL") {
	    //mail actie
	    include_once  dirname(__FILE__) . "/__sb_operation_mail.php";

	  } elseif ($operation == "SCHEDULE") {
	    //schedule actie
	    include_once  dirname(__FILE__) . "/__sb_operation_schedule.php";

	  } elseif ($operation == "CHART") {
      //chart actie
      include_once  dirname(__FILE__) . "/__sb_operation_chart.php";

	  } elseif ($operation == "MPDF") {
	    //mpdf actie
	    include_once  dirname(__FILE__) . "/__sb_operation_mpdf.php";

	  } elseif ($operation == "DOCX2MPDF") {
	    //docx2mpdf actie
	    include_once  dirname(__FILE__) . "/__sb_operation_docx2mpdf.php";

	  } elseif ($operation == "DOCX2PDF") {
	    //docx2pdf actie
	    include_once  dirname(__FILE__) . "/__sb_operation_docx2pdf.php";

	  } elseif ($operation == "DOCX") {
	    //docx actie
	    include_once  dirname(__FILE__) . "/__sb_operation_docx2.php";

	  } elseif ($operation == "RTF") {
	    //rtf actie
	    include_once  dirname(__FILE__) . "/__sb_operation_rtf.php";

	  } elseif ($operation == "EXPORT") {
	    //export actie
	    include_once  dirname(__FILE__) . "/__sb_operation_export.php";

    } elseif ($operation == "EXPORTOLD") {
      //export actie
      include_once  dirname(__FILE__) . "/__sb_operation_export_old.php";

	  } elseif ($operation == "UPLOAD") {
	    //upload actie
	    include_once  dirname(__FILE__) . "/__sb_operation_upload.php";

	  } elseif ($operation == "REDIRECT") {
	    //redirect actie
	    include_once  dirname(__FILE__) . "/__sb_operation_redirect.php";

    } elseif ($operation == "SAVEFILE") {
      //redirect actie
      include_once  dirname(__FILE__) . "/__sb_operation_savefile.php";

    } elseif ($operation == "LOADFILE") {
      //redirect actie
      include_once  dirname(__FILE__) . "/__sb_operation_loadfile.php";
    }

	  //fb_timer_end($totaltimer);
  //} else {
  //  $resultset = array(
  //    "success"    => false,
  //    "message"    => "session expired"
  //  );

  //  echo json_encode($resultset);
  //}
?>