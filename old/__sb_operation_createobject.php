<?
  //TIMING
  $mtime      = microtime();
  $mtime      = explode(' ', $mtime);
  $mtime      = $mtime[1] + $mtime[0];
  $timerstart = $mtime;

  //INCLUDES  framework
  include_once __DIR__ . "/../framework/classes/sb/__sb_connection.php";
  include_once __DIR__ . "/../framework/classes/sb/__sb_query.php";
  include_once __DIR__ . "/../framework/classes/sb/__sb_operationquery.php";
  include_once __DIR__ . "/../framework/classes/sb/__sb_xmltemplate.php";
  include_once __DIR__ . "/../framework/classes/sb/__sb_operation.php";

  //INCLUDES  functions
  include_once __DIR__ . "/../framework/functions/_php_functions.php";
  include_once __DIR__ . "/../framework/functions/_string_functions.php";
  include_once __DIR__ . "/../framework/functions/_eval_functions.php";
  include_once __DIR__ . "/../framework/functions/_error_functions.php";
  include_once __DIR__ . "/../framework/functions/_date_functions.php";
  include_once __DIR__ . "/../framework/functions/_file_functions.php";
  include_once __DIR__ . "/../framework/functions/_debug_functions.php";
  include_once __DIR__ . "/../framework/functions/_log_functions.php";
  include_once __DIR__ . "/../framework/functions/_array_functions.php";
  include_once __DIR__ . "/../framework/functions/_json_functions.php";
  include_once __DIR__ . "/../framework/functions/_encryption_functions.php";
  include_once __DIR__ . "/../framework/functions/_replace_functions.php";

  //if ($_SESSION["sessionid"]) {
    //parameters valideren
    //foreach ($_REQUEST as $key=>$value) {
    //  $_REQUEST[$key] = safesqlstring($value);
    //}

    //PARAMETERS  operation
    $ajax                   = (isset($_REQUEST["ajax"])) ? strtobool($_REQUEST["ajax"], true) : true;
    $operation              = (isset($_REQUEST["operation"])) ? $_REQUEST["operation"] : "DATABASE";
    $operationtype          = (isset($_REQUEST["operationtype"])) ? $_REQUEST["operationtype"] : "SELECT";
    $operationinclude       = (isset($_REQUEST["operationinclude"])) ? $_REQUEST["operationinclude"] : "";
    $operationtitle         = (isset($_REQUEST["operationtitle"])) ? $_REQUEST["operationtitle"] : "";
    $operationmessage       = (isset($_REQUEST["operationmessage"])) ? $_REQUEST["operationmessage"] : "";
    $values                 = (isset($_REQUEST["values"])) ? (is_serialized(urldecode($_REQUEST["values"]))) ? unserialize(urldecode($_REQUEST["values"])) : explode_assoc(";", $_REQUEST["values"]) : array();
    $params                 = (isset($_REQUEST["params"])) ? (is_serialized(urldecode($_REQUEST["params"]))) ? unserialize(urldecode($_REQUEST["params"])) : explode_assoc(";", $_REQUEST["params"]) : '';

    //PARAMETERS  result
    $resulttype             = (isset($_REQUEST["resulttype"])) ? $_REQUEST["resulttype"] : "json";
    $resultformat           = (isset($_REQUEST["resultformat"])) ? $_REQUEST["resultformat"] : "grid";
    $resultload             = (isset($_REQUEST["resultload"])) ? strtobool($_REQUEST["resultload"], false) : false;
    $resultsave             = (isset($_REQUEST["resultsave"])) ? strtobool($_REQUEST["resultsave"], false) : false;
    $resultfilename         = (isset($_REQUEST["resultfilename"])) ? $_REQUEST["resultfilename"] : "";

    //PARAMETERS  query
    $connectionparameters   = (isset($_REQUEST["connection"]) && !is_empty($_REQUEST["connection"])) ? unserialize(decryptForce($_REQUEST["connection"], "crid", false)) : ""; //(isset($_SESSION["project"]["database"]) && isset($_SESSION["project"]["database"]["connectionparameters"])) ? unserialize($_SESSION["project"]["database"]["connectionparameters"]) : "";
    $connectionserver       = (isset($_REQUEST["connectionserver"]) && !is_empty($_REQUEST["connectionserver"])) ? $_REQUEST["connectionserver"] : "";
    $connectiondatabasename = (isset($_REQUEST["connectiondatabasename"]) && !is_empty($_REQUEST["connectiondatabasename"])) ? $_REQUEST["connectiondatabasename"] : "";
    $sql                    = (isset($_REQUEST["sql"])) ? decryptConvert($_REQUEST["sql"], "crid", false) : "";
    $sqlfilename            = (isset($_REQUEST["sqlfilename"]) && !is_empty($_REQUEST["sqlfilename"])) ? decryptForce($_REQUEST["sqlfilename"], "crid", false) :"";
    $sqlpath                = (isset($_REQUEST["sqlpath"]) && !is_empty($_REQUEST["sqlpath"])) ? decryptForce($_REQUEST["sqlpath"], "", false) : "";
    $sqlcustompath          = (isset($_REQUEST["sqlcustompath"]) && !is_empty($_REQUEST["sqlcustompath"])) ? decryptForce($_REQUEST["sqlcustompath"], "crid", false) : "";
    $sqlcachepath           = (isset($_REQUEST["sqlcachepath"]) && !is_empty($_REQUEST["sqlcachepath"])) ? decryptForce($_REQUEST["sqlcachepath"], "crid", false) : "";
    $tablename              = (isset($_REQUEST["tablename"])) ? $_REQUEST["tablename"] : "";
    $fields                 = (isset($_REQUEST["fields"])) ? str_ireplace('"', '', $_REQUEST["fields"]) : "";
    $primaryfieldname       = (isset($_REQUEST["primaryfieldname"])) ? $_REQUEST["primaryfieldname"] : "";
    $primaryfieldvalue      = (isset($_REQUEST["primaryfieldvalue"]) && !is_empty($_REQUEST["primaryfieldvalue"])) ? decryptConvert($_REQUEST["primaryfieldvalue"]) : null;
    $queryclass             = (isset($_REQUEST["queryclass"])) ? class_exists($_REQUEST["queryclass"]) ? $_REQUEST["queryclass"] : "SB_OperationQuery" : "SB_OperationQuery";
    $realclass              = (isset($_REQUEST["realclass"])) ? class_exists($_REQUEST["realclass"]) ? $_REQUEST["realclass"] : "" : "";

    $start                  = (isset($_REQUEST["start"])) ? $_REQUEST["start"] : 0;
    $limit                  = (isset($_REQUEST["limit"])) ? $_REQUEST["limit"] : 0;
    $sortfieldname          = (isset($_REQUEST["sortfieldname"])) ? $_REQUEST["sortfieldname"] : "";
    $sortdirection          = (isset($_REQUEST["sortdirection"])) ? $_REQUEST["sortdirection"] : "";
    $page                   = (isset($_REQUEST["page"])) ? $_REQUEST["page"] : 0;
    $rowcount               = (isset($_REQUEST["rowcount"])) ? $_REQUEST["rowcount"] : 0;

    $amountfieldname        = (isset($_REQUEST["amountfieldname"])) ? $_REQUEST["amountfieldname"] : "";
    $cellfieldname          = (isset($_REQUEST["cellfieldname"])) ? $_REQUEST["cellfieldname"] : "";
    $celldisplayfieldname   = (isset($_REQUEST["celldisplayfieldname"])) ? $_REQUEST["celldisplayfieldname"] : "";
    $cellvaluefieldname     = (isset($_REQUEST["cellvaluefieldname"])) ? $_REQUEST["cellvaluefieldname"] : "";
    $columnfieldname        = (isset($_REQUEST["columnfieldname"])) ? $_REQUEST["columnfieldname"] : "";
    $columndisplayfieldname = (isset($_REQUEST["columndisplayfieldname"])) ? $_REQUEST["columndisplayfieldname"] : "";
    $columnvaluefieldname   = (isset($_REQUEST["columnvaluefieldname"])) ? $_REQUEST["columnvaluefieldname"] : "";
    $customfieldname        = (isset($_REQUEST["customfieldname"])) ? $_REQUEST["customfieldname"] : "";
    $datefieldname          = (isset($_REQUEST["datefieldname"])) ? $_REQUEST["datefieldname"] : "";
    $datestartfieldname     = (isset($_REQUEST["datestartfieldname"])) ? $_REQUEST["datestartfieldname"] : "";
    $dateendfieldname       = (isset($_REQUEST["dateendfieldname"])) ? $_REQUEST["dateendfieldname"] : "";
    $displayfieldname       = (isset($_REQUEST["displayfieldname"])) ? $_REQUEST["displayfieldname"] : "";
    $distinctfieldname      = (isset($_REQUEST["distinctfieldname"])) ? $_REQUEST["distinctfieldname"] : "";
    $encryptfieldname       = (isset($_REQUEST["encryptfieldname"])) ? $_REQUEST["encryptfieldname"] : "";
    $groupingfieldname      = (isset($_REQUEST["groupingfieldname"])) ? $_REQUEST["groupingfieldname"] : "";
    $iconfieldname          = (isset($_REQUEST["iconfieldname"])) ? $_REQUEST["iconfieldname"] : "";
    $indexfieldname         = (isset($_REQUEST["indexfieldname"])) ? $_REQUEST["indexfieldname"] : "";
    $jsonfieldname          = (isset($_REQUEST["jsonfieldname"])) ? $_REQUEST["jsonfieldname"] : "";
    $locationfieldname      = (isset($_REQUEST["locationfieldname"])) ? $_REQUEST["locationfieldname"] : "";
    $parentfieldname        = (isset($_REQUEST["parentfieldname"])) ? $_REQUEST["parentfieldname"] : "";
    $rowfieldname           = (isset($_REQUEST["rowfieldname"])) ? $_REQUEST["rowfieldname"] : "";
    $rowdisplayfieldname    = (isset($_REQUEST["rowdisplayfieldname"])) ? $_REQUEST["rowdisplayfieldname"] : "";
    $rowvaluefieldname      = (isset($_REQUEST["rowvaluefieldname"])) ? $_REQUEST["rowvaluefieldname"] : "";
    $searchfieldname        = (isset($_REQUEST["searchfieldname"])) ? $_REQUEST["searchfieldname"] : "";
    $searchfieldvalue       = (isset($_REQUEST["searchfieldvalue"])) ? $_REQUEST["searchfieldvalue"] : "";
    $titlefieldname         = (isset($_REQUEST["titlefieldname"])) ? $_REQUEST["titlefieldname"] : "";
    $valuefieldname         = (isset($_REQUEST["valuefieldname"])) ? $_REQUEST["valuefieldname"] : "";

    $searchpath             = (isset($_REQUEST["searchpath"])) ? $_REQUEST["searchpath"] : "";
    $searchparamname        = (isset($_REQUEST["searchparamname"])) ? $_REQUEST["searchparamname"] : "";

    $datestart              = (isset($_REQUEST["datestart"]) && !is_empty($_REQUEST["datestart"])) ? $_REQUEST["datestart"] : "";
    $datestartopen          = (isset($_REQUEST["datestartopen"])) ? strtobool($_REQUEST["datestartopen"], false) : false;
    $dateend                = (isset($_REQUEST["dateend"]) && !is_empty($_REQUEST["dateend"])) ? $_REQUEST["dateend"] : "";
    $dateendopen            = (isset($_REQUEST["dateendopen"])) ? strtobool($_REQUEST["dateendopen"], false) : false;

    $columnsonly            = (isset($_REQUEST["columnsonly"])) ? strtobool($_REQUEST["columnsonly"], false) : false;
    $countmethod            = (isset($_REQUEST["countmethod"])) ? $_REQUEST["countmethod"] : "count";
    $duplicate              = (isset($_REQUEST["duplicate"])) ? strtobool($_REQUEST["duplicate"], false) : false;
    $encrypt                = (isset($_REQUEST["encrypt"])) ? strtobool($_REQUEST["encrypt"], false) : false;
    $evaluate               = (isset($_REQUEST["evaluate"])) ? strtobool($_REQUEST["evaluate"], false) : false;
    $killthread             = (isset($_REQUEST["killthread"])) ? strtobool($_REQUEST["killthread"], false) : false;
    $manipulate             = (isset($_REQUEST["manipulate"])) ? strtobool($_REQUEST["manipulate"], false) : false;
    $manipulator            = (isset($_REQUEST["manipulator"])) ? $_REQUEST["manipulator"] :"";
    $manipulatorfieldname   = (isset($_REQUEST["manipulatorfieldname"])) ? $_REQUEST["manipulatorfieldname"] : "";
    $manipulatorparams      = (isset($_REQUEST["manipulatorparams"])) ? $_REQUEST["manipulatorparams"] : "";
    $manipulatortype        = (isset($_REQUEST["manipulatortype"])) ? $_REQUEST["manipulatortype"] : "fields";
    $nocache                = (isset($_REQUEST["nocache"])) ? strtobool($_REQUEST["nocache"], false) : false;
    $splitprimary           = (isset($_REQUEST["splitprimary"])) ? strtobool($_REQUEST["splitprimary"], false) : false;
    $removecomments         = (isset($_REQUEST["removecomments"])) ? strtobool($_REQUEST["removecomments"], false) : false;
    $replaceresult          = (isset($_REQUEST["replaceresult"])) ? strtobool($_REQUEST["replaceresult"], true) : true;
    $replaceresultcodes     = (isset($_REQUEST["replaceresultcodes"])) ? strtobool($_REQUEST["replaceresultcodes"], true) : true;
    $replaceresultconditions= (isset($_REQUEST["replaceresultconditions"])) ? strtobool($_REQUEST["replaceresultconditions"], false) : false;
    $replaceresultquotes    = (isset($_REQUEST["replaceresultquotes"])) ? strtobool($_REQUEST["replaceresultquotes"], false) : true;
    $replacefieldname       = (isset($_REQUEST["replacefieldname"])) ? str_ireplace('"', '', $_REQUEST["replacefieldname"]) : "";
    $replaceresultfunction  = (isset($_REQUEST["replaceresultfunction"])) ? $_REQUEST["replaceresultfunction"] : "";

    //PARAMETERS  sender
    $containerid            = (isset($_REQUEST["containerid"]) && !is_empty($_REQUEST["containerid"])) ? $_REQUEST["containerid"] : "";

    $senderid               = (isset($_REQUEST["senderid"])) ? $_REQUEST["senderid"] : "";
    $senderjsid             = (isset($_REQUEST["senderjsid"])) ? $_REQUEST["senderjsid"] : "";
    $senderclass            = (isset($_REQUEST["senderclass"])) ? $_REQUEST["senderclass"] : "";

    //PARAMETERS  template
    $cacheload              = (isset($_REQUEST["cacheload"])) ? strtobool($_REQUEST["cacheload"], false) : (isset($_REQUEST["cachingserverload"]) ? strtobool($_REQUEST["cachingserverload"]) : false);
    $cacheloadjs            = (isset($_REQUEST["cacheloadjs"])) ? strtobool($_REQUEST["cacheloadjs"], false) : (isset($_REQUEST["cachingserverloadjs"]) ? strtobool($_REQUEST["cachingserverloadjs"]) : $cacheload);
    $cachesave              = (isset($_REQUEST["cachesave"])) ? strtobool($_REQUEST["cachesave"], false) : (isset($_REQUEST["cachingserversave"]) ? strtobool($_REQUEST["cachingserversave"]) : false);
    $cachesavejs            = (isset($_REQUEST["cachesavejs"])) ? strtobool($_REQUEST["cachesavejs"], false) : (isset($_REQUEST["cachingserversavejs"]) ? strtobool($_REQUEST["cachingserversavejs"]) : $cachesave);
    $cachecompress          = (isset($_REQUEST["cachecompress"])) ? strtobool($_REQUEST["cachecompress"], false) : (isset($_REQUEST["cachingservercompress"]) ? strtobool($_REQUEST["cachingservercompress"]) : false);
    $cachecompressjs        = (isset($_REQUEST["cachecompressjs"])) ? strtobool($_REQUEST["cachecompressjs"], false) : (isset($_REQUEST["cachingservercompressjs"]) ? strtobool($_REQUEST["cachingservercompressjs"]) : $cachecompress);
    $combinecssfiles        = (isset($_REQUEST["combinecssfiles"])) ? strtobool($_REQUEST["combinecssfiles"], false) : (isset($_SESSION["project"]["combine_cssfiles"]) ? $_SESSION["project"]["combine_cssfiles"] : false);
    $compilecssfiles        = (isset($_REQUEST["compilecssfiles"])) ? strtobool($_REQUEST["compilecssfiles"], false) : (isset($_SESSION["project"]["compile_cssfiles"]) ? $_SESSION["project"]["compile_cssfiles"] : false);
    $combinejavascriptfiles = (isset($_REQUEST["combinejavascriptfiles"])) ? strtobool($_REQUEST["combinejavascriptfiles"], false) : (isset($_SESSION["project"]["combine_javascriptfiles"]) ? $_SESSION["project"]["combine_javascriptfiles"] : false);
    $compilejavascriptfiles = (isset($_REQUEST["compilejavascriptfiles"])) ? strtobool($_REQUEST["compilejavascriptfiles"], false) : (isset($_SESSION["project"]["compile_javascriptfiles"]) ? $_SESSION["project"]["compile_javascriptfiles"] : false);
    $outputdoctype          = (isset($_REQUEST["outputdoctype"])) ? strtobool($_REQUEST["outputdoctype"], false) : false;
    $outputheaders          = (isset($_REQUEST["outputheaders"])) ? strtobool($_REQUEST["outputheaders"], false) : false;
    $outputcss              = (isset($_REQUEST["outputcss"])) ? strtobool($_REQUEST["outputcss"], false) : false;
    $outputcssfiles         = (isset($_REQUEST["outputcssfiles"])) ? strtobool($_REQUEST["outputcssfiles"], false) : false;
    $outputjavascriptfiles  = (isset($_REQUEST["outputjavascriptfiles"])) ? strtobool($_REQUEST["outputjavascriptfiles"], false) : false;
    $outputjavascript       = (isset($_REQUEST["outputjavascript"])) ? strtobool($_REQUEST["outputjavascript"], false) : false;
    $outputjavascriptasfile = (isset($_REQUEST["outputjavascript"])) ? strtobool($_REQUEST["outputjavascript"], false) : false;
    $serverload             = (isset($_REQUEST["serverload"])) ? strtobool($_REQUEST["serverload"], false) : false;

    $templatefilename       = (isset($_REQUEST["templatefilename"])) ? $_REQUEST["templatefilename"] : "";
    $templatefilenameoutput = (isset($_REQUEST["templatefilenameoutput"])) ? $_REQUEST["templatefilenameoutput"] : "";
    $templaterootpath       = (isset($_REQUEST["templaterootpath"])) ? __DIR__ . "/../" . stripfirstslash($_REQUEST["templaterootpath"]) : __DIR__ . "/..";
    $templatecustompath     = (isset($_REQUEST["templatecustompath"])) ? __DIR__ . "/../" . stripfirstslash($_REQUEST["templatecustompath"]) : ((isset($_SESSION["account"]["organisatiedirectory"])) ? (__DIR__ . "/../usersettings/" . stripouterslashes($_SESSION["account"]["organisatiedirectory"])) : "");
    $templatecachepath      = (isset($_REQUEST["templatecachepath"])) ? $_REQUEST["templatecachepath"] : "";
    $templatecachepathfull  = (isset($_REQUEST["templatecachepath"])) ? __DIR__ . "/../" . stripfirstslash($_REQUEST["templatecachepath"]) : "";
    $templatetemppath       = (isset($_REQUEST["templatetemppath"])) ? __DIR__ . "/../" . stripfirstslash($_REQUEST["templatetemppath"]) : "";
    $templateclass          = (isset($_REQUEST["templateclass"])) ? (class_exists($_REQUEST["templateclass"]) ? $_REQUEST["templateclass"] : "SB_XMLTemplate") : "SB_XMLTemplate";
    $templateprefix         = (isset($_REQUEST["templateprefix"])) ? $_REQUEST["templateprefix"] : "";
    $templatesuffix         = (isset($_REQUEST["templatesuffix"])) ? $_REQUEST["templatesuffix"] : "";

    $uploaddir              = (isset($_REQUEST["uploaddir"])) ? $_REQUEST["uploaddir"] : "";

    $url                    = (isset($_REQUEST["url"])) ? $_REQUEST["url"] : "";

    //PARAMETERS  config
    $demo                   = (isset($_REQUEST["demo"])) ? strtobool2($_REQUEST["demo"]) : ((isset($_SESSION["project"]["demo"]) && is_bool($_SESSION["project"]["demo"])) ? $_SESSION["project"]["demo"] : false);
    $debug                  = (isset($_REQUEST["debug"])) ? strtobool2($_REQUEST["debug"]) : ((isset($_SESSION["project"]["debug"]) && is_bool($_SESSION["project"]["debug"])) ? $_SESSION["project"]["debug"] : true);
    $developer              = (isset($_REQUEST["developer"])) ? strtobool2($_REQUEST["developer"]) : ((isset($_SESSION["project"]["developer"]) && is_bool($_SESSION["project"]["developer"])) ? $_SESSION["project"]["developer"] : false);
    $hashing                = (isset($_REQUEST["hashing"])) ? strtobool2($_REQUEST["hashing"]) : ((isset($_SESSION["project"]["hashing"]) && is_bool($_SESSION["project"]["hashing"])) ? $_SESSION["project"]["hashing"] : false);
    $logging                = (isset($_REQUEST["logging"])) ? strtobool2($_REQUEST["logging"]) : ((isset($_SESSION["project"]["logging"]) && is_bool($_SESSION["project"]["logging"])) ? $_SESSION["project"]["logging"] : false);
    $master                 = (isset($_SESSION["project"]["master"]) && is_bool($_SESSION["project"]["master"])) ? $_SESSION["project"]["master"] : false;
    $objecting              = (isset($_REQUEST["objecting"])) ? strtobool2($_REQUEST["objecting"]) : ((isset($_SESSION["project"]["profiling"]) && is_bool($_SESSION["project"]["objecting"])) ? $_SESSION["project"]["objecting"] : true);
    $profiling              = (isset($_REQUEST["profiling"])) ? strtobool2($_REQUEST["profiling"]) : ((isset($_SESSION["project"]["profiling"]) && is_bool($_SESSION["project"]["profiling"])) ? $_SESSION["project"]["profiling"] : false);
    $test                   = (isset($_REQUEST["test"])) ? strtobool2($_REQUEST["test"]) : ((isset($_SESSION["project"]["test"]) && is_bool($_SESSION["project"]["test"])) ? $_SESSION["project"]["test"] : true);
    $timing                 = (isset($_REQUEST["timing"])) ? strtobool2($_REQUEST["timing"]) : ((isset($_SESSION["project"]["timing"]) && is_bool($_SESSION["project"]["timing"])) ? $_SESSION["project"]["timing"] : false);
    $timinglimit            = (isset($_REQUEST["timinglimit"])) ? $_REQUEST["timinglimit"] : ((isset($_SESSION["project"]["timinglimit"]) && is_bool($_SESSION["project"]["timinglimit"])) ? $_SESSION["project"]["timinglimit"] : 0.15);;
    $timingoutput           = (isset($_REQUEST["timingoutput"])) ? strtobool2($_REQUEST["timingoutput"]) : ((isset($_SESSION["project"]["timingoutput"]) && is_bool($_SESSION["project"]["timingoutput"])) ? $_SESSION["project"]["timingoutput"] : false);

    //PARAMETERS  xml
    $xmlsubmit              =  (isset($_REQUEST["xmlsubmit"])     && !is_empty($_REQUEST["xmlsubmit"])    && strtobool($_REQUEST["xmlsubmit"])    && strtolower($_REQUEST["xmlsubmit"]) != "undefined")     ? comparetext(left($_REQUEST["xmlsubmit"],5), "<?xml")    ? simplexml_load_string($_REQUEST["xmlsubmit"])     : simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlsubmit"])     : "";
    $xmlrequest             =  (isset($_REQUEST["xmlrequest"])    && !is_empty($_REQUEST["xmlrequest"])   && strtobool($_REQUEST["xmlrequest"])   && strtolower($_REQUEST["xmlrequest"]) != "undefined")    ? comparetext(left($_REQUEST["xmlrequest"],5), "<?xml")   ? simplexml_load_string($_REQUEST["xmlrequest"])    : simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlrequest"])    : "";
    $xmlselection           =  (isset($_REQUEST["xmlselection"])  && !is_empty($_REQUEST["xmlselection"]) && strtobool($_REQUEST["xmlselection"]) && strtolower($_REQUEST["xmlselection"]) != "undefined")  ? comparetext(left($_REQUEST["xmlselection"],5), "<?xml") ? simplexml_load_string($_REQUEST["xmlselection"])  : simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlselection"])  : "";
    $xmlsearch              =  (isset($_REQUEST["xmlsearch"])     && !is_empty($_REQUEST["xmlsearch"])    && strtobool($_REQUEST["xmlsearch"])    && strtolower($_REQUEST["xmlsearch"]) != "undefined")     ? comparetext(left($_REQUEST["xmlsearch"],5), "<?xml")    ? simplexml_load_string($_REQUEST["xmlsearch"])     : simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlsearch"])     : "";
    $xmldefault             =  (isset($_REQUEST["xmldefault"])    && !is_empty($_REQUEST["xmldefault"])   && strtobool($_REQUEST["xmldefault"])   && strtolower($_REQUEST["xmldefault"]) != "undefined")    ? comparetext(left($_REQUEST["xmldefault"],5), "<?xml")   ? simplexml_load_string($_REQUEST["xmldefault"])    : simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmldefault"])    : "";
    $xmlcolumns             =  (isset($_REQUEST["xmlcolumns"])    && !is_empty($_REQUEST["xmlcolumns"])   && strtobool($_REQUEST["xmlcolumns"])   && strtolower($_REQUEST["xmlcolumns"]) != "undefined")    ? comparetext(left($_REQUEST["xmlcolumns"],5), "<?xml")   ? simplexml_load_string($_REQUEST["xmlcolumns"])    : simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlcolumns"])    : "";
    $xmlresult              =  (isset($_REQUEST["xmlresult"])     && !is_empty($_REQUEST["xmlresult"])    && strtobool($_REQUEST["xmlresult"])    && strtolower($_REQUEST["xmlresult"]) != "undefined")     ? comparetext(left($_REQUEST["xmlresult"],5), "<?xml")    ? simplexml_load_string($_REQUEST["xmlresult"])     : simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>".$_REQUEST["xmlresult"])     : "";

    //PARAMETERS  json
    $jsonsubmit             =  (isset($_REQUEST["jsonsubmit"])     && !is_empty($_REQUEST["jsonsubmit"])    && strtobool($_REQUEST["jsonsubmit"])    && strtolower($_REQUEST["jsonsubmit"]) != "undefined")     ? json_decode(fixJSON($_REQUEST["jsonsubmit"]), true) : false;
    $jsonrequest            =  (isset($_REQUEST["jsonrequest"])    && !is_empty($_REQUEST["jsonrequest"])   && strtobool($_REQUEST["jsonrequest"])   && strtolower($_REQUEST["jsonrequest"]) != "undefined")    ? json_decode(fixJSON($_REQUEST["jsonrequest"]), true) : false;
    $jsonselection          =  (isset($_REQUEST["jsonselection"])  && !is_empty($_REQUEST["jsonselection"]) && strtobool($_REQUEST["jsonselection"]) && strtolower($_REQUEST["jsonselection"]) != "undefined")  ? json_decode(fixJSON($_REQUEST["jsonselection"]), true) : false;
    $jsonsearch             =  (isset($_REQUEST["jsonsearch"])     && !is_empty($_REQUEST["jsonsearch"])    && strtobool($_REQUEST["jsonsearch"])    && strtolower($_REQUEST["jsonsearch"]) != "undefined")     ? json_decode(fixJSON($_REQUEST["jsonsearch"]), true) : false;
    $jsondefault            =  (isset($_REQUEST["jsondefault"])    && !is_empty($_REQUEST["jsondefault"])   && strtobool($_REQUEST["jsondefault"])   && strtolower($_REQUEST["jsondefault"]) != "undefined")    ? json_decode(fixJSON($_REQUEST["jsondefault"]), true) : false;
    $jsoncolumns            =  (isset($_REQUEST["jsoncolumns"])    && !is_empty($_REQUEST["jsoncolumns"])   && strtobool($_REQUEST["jsoncolumns"])   && strtolower($_REQUEST["jsoncolumns"]) != "undefined")    ? json_decode(fixJSON($_REQUEST["jsoncolumns"]), true) : false;
    $jsonresult             =  (isset($_REQUEST["jsonresult"])     && !is_empty($_REQUEST["jsonresult"])    && strtobool($_REQUEST["jsonresult"])    && strtolower($_REQUEST["jsonresult"]) != "undefined")     ? json_decode(fixJSON($_REQUEST["jsonresult"]), true) : false;

    //PARAMETERS  logging
    //$logging                = (isset($_REQUEST["logging"])) ? strtobool($_REQUEST["logging"], false) : (isset($_SESSION["project"]["logging"]) && (is_bool($_SESSION["project"]["logging"]))) ? $_SESSION["project"]["logging"] : false;
    $logfilename            = (isset($_REQUEST["logfilename"])) ? $_REQUEST["logfilename"] : "";
    $logpath                = (isset($_REQUEST["logpath"])) ? $_REQUEST["logpath"] : __DIR__ . "/../temp/log/" . date("Ymd", time());;
    $logtablename           = (isset($_REQUEST["logtablename"])) ? $_REQUEST["logtablename"] : "logs";

    //PARAMETERS  mail
    $server                 = (isset($_REQUEST["server"]) && !is_empty($_REQUEST["server"])) ? decryptConvert($_REQUEST["server"], "crid", false) : "";
    $username               = (isset($_REQUEST["username"]) &&  !is_empty($_REQUEST["username"])) ? decryptConvert($_REQUEST["username"], "crid", false) : "";
    $password               = (isset($_REQUEST["password"]) && !is_empty($_REQUEST["password"])) ? decryptConvert($_REQUEST["password"], "crid", false) : "";
    $attachment             = (isset($_REQUEST["attachment"]) && !is_empty($_REQUEST["attachment"])) ? $_REQUEST["attachment"] : "";
    $subject                = (isset($_REQUEST["subject"]) && !is_empty($_REQUEST["subject"])) ? $_REQUEST["subject"] : "";
    $from                   = (isset($_REQUEST["from"]) && !is_empty($_REQUEST["from"])) ? $_REQUEST["from"] : "";
    $fromname               = (isset($_REQUEST["fromname"]) && !is_empty($_REQUEST["fromname"])) ? $_REQUEST["fromname"] : "";
    $to                     = (isset($_REQUEST["to"]) && !is_empty($_REQUEST["to"])) ? $_REQUEST["to"] : "";
    $replyto                = (isset($_REQUEST["replyto"]) && !is_empty($_REQUEST["replyto"])) ? $_REQUEST["replyto"] : "";
    $cc                     = (isset($_REQUEST["cc"]) && !is_empty($_REQUEST["cc"])) ? $_REQUEST["cc"] : "";
    $bcc                    = (isset($_REQUEST["bcc"]) && !is_empty($_REQUEST["bcc"])) ? $_REQUEST["bcc"] : "";
    $layer                  = (isset($_REQUEST["layer"]) && !is_empty($_REQUEST["layer"])) ? $_REQUEST["layer"] : "";
    $port                   = (isset($_REQUEST["port"]) && !is_empty($_REQUEST["port"])) ? $_REQUEST["port"] : "";

    //PARAMETERS  calendar
    //$startdate              = (isset($_REQUEST["startdate"]) && !is_empty($_REQUEST["startdate"])) ? (int)$_REQUEST["startdate"] : "";
    //$enddate                = (isset($_REQUEST["enddate"]) && !is_empty($_REQUEST["enddate"])) ? (int)$_REQUEST["enddate"] : "";

    //PARAMETERS  chart
    $height                 = (isset($_REQUEST["height"]) && !is_empty($_REQUEST["height"])) ? $_REQUEST["height"] : 250;
    $width                  = (isset($_REQUEST["width"]) && !is_empty($_REQUEST["width"])) ? $_REQUEST["width"] : 750;

    //PARAMETERS  file
    $fileappend             = (isset($_REQUEST["fileappend"])) ? strtobool($_REQUEST["fileappend"], false) : false;
    $filecontent            = (isset($_REQUEST["filecontent"])) ? $_REQUEST["filecontent"] : "";
    $fileextension          = (isset($_REQUEST["fileextension"])) ? $_REQUEST["fileextension"] : "";
    $filename               = (isset($_REQUEST["filename"])) ? $_REQUEST["filename"] : "";
    $filepath               = (isset($_REQUEST["filepath"])) ? $_REQUEST["filepath"] : "";
    $filewildcard           = (isset($_REQUEST["filewildcard"])) ? $_REQUEST["filewildcard"] : "";
    $image                  = (isset($_REQUEST["image"])) ? $_REQUEST["image"] : "";
    $imagetype              = (isset($_REQUEST["imagetype"])) ? $_REQUEST["imagetype"] : "";

    //PARAMETERS  export
    $json                   = (isset($_REQUEST["json"])) ? $_REQUEST["json"] : "";

    //REQUEST adjust (ivm encryptie)
    //TODO: moet mooier!
    if (isset($_REQUEST["primaryfieldname"]))   {$_REQUEST["primaryfieldname"]  = $primaryfieldname;}
    if (isset($_REQUEST["primaryfieldvalue"]))  {$_REQUEST["primaryfieldvalue"]  = $primaryfieldvalue;}

    //HEADERS
    if ($resulttype == "json") {
      header('Content-type: application/json; charset=UTF-8');
    } else {
      header('Content-Type: text/html; charset=UTF-8');
    }
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PUT, POST, HEAD, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 1000');


    //OVERRIDE connectionparameters?
    if (is_object($connectionparameters)) {
      $connectionparameters->Server       = $connectionserver ? $connectionserver : $connectionparameters->Server;
      $connectionparameters->DatabaseName = $connectionserver ? $connectiondatabasename : $connectionparameters->DatabaseName;
    }

    //timing
    if ($timing) {
      fb_timer_end($timerstart, $timinglimit, "sb_operation_createobject.php : INLEZEN");
    }

    //INIT VARIABLES
    if (!$ajax) {
      $outputdoctype             = true;
      $outputheaders             = true;
      $outputcssfiles            = true;
      $outputjavascriptfiles     = true;
      //$outputevaljavascriptfiles = true;
    }

    //INCLUDE operationinclude
    if (isnotempty($operationinclude)) {
      $operationincludes = multi_explode(",;", $operationinclude);

      foreach ($operationincludes as $key => $operationincludefilename) {
        include_once __DIR__ . "/../" . stripfirstslash($operationincludefilename);
      }
    }

    //timing
    if ($timing) {
      fb_timer_end($timerstart, $timinglimit, "sb_operation_createobject.php : INCLUDES " . $operationinclude);
    }

    //SET session parameters
    //$_SESSION["project"]["asynchronous"]  = (!is_empty($params) && isset($params["asynchronous"]))  ? boolOrEval($params["asynchronous"], true) : true;
    //$_SESSION["project"]["moduleoptieid"] = (!is_empty($params) && isset($params["moduleoptieid"])) ? $params["moduleoptieid"] : "UNKNOWN";

    //CREATE operation object
    $operationobjectclass = "SB_Operation_" . upfirst($operation);

    if (class_exists($operationobjectclass)) {
      $operationobject  = new $operationobjectclass();

      //operation properties
      $operationobject->ID                = "Operation";
      $operationobject->ContainerID       = $containerid;
      $operationobject->Debug             = $debug;
      $operationobject->Demo              = $demo;
      $operationobject->Developer         = $developer;
      $operationobject->Logging           = $logging;
      $operationobject->LoggingFilename   = $logfilename;
      $operationobject->LoggingPath       = $logpath;
      $operationobject->LoggingTablename  = $logtablename;
      $operationobject->Message           = $operationmessage;
      $operationobject->Objecting         = $objecting;
      $operationobject->Profiling         = $profiling;
      $operationobject->ResultFormat      = $resultformat;
      $operationobject->ResultType        = $resulttype;
      $operationobject->Test              = $test;
      $operationobject->Timing            = $timing;
      $operationobject->Title             = $operationtitle;
      $operationobject->Label             = $operationtitle;
      $operationobject->Type              = $operationtype;
      $operationobject->XMLColumns        = $xmlcolumns;
      $operationobject->XMLRequest        = $xmlrequest;
      $operationobject->XMLSubmit         = $xmlsubmit;
      $operationobject->XMLSearch         = $xmlsearch;
      $operationobject->XMLSelection      = $xmlselection;
      $operationobject->XMLResult         = $xmlresult;

      //PRINT properties
      if (comparetext($operationobjectclass, "SB_Operation_Print")) {
        $operationobject->FilenameOutput  = $templatefilenameoutput;
        $operationobject->JSON            = $json;
      }

      //EXPORT properties
      if (comparetext($operationobjectclass, "SB_Operation_Export")) {
        $operationobject->JSON            = $json;
      }

      //CHART properties
      if (comparetext($operationobjectclass, "SB_Operation_Chart")) {
        $operationobject->Height          = $height;
        $operationobject->Width           = $width;
      }

      //FILE properties
      if (comparetext($operationobjectclass, "SB_Operation_File")) {
        $operationobject->Append          = $fileappend;
        $operationobject->FileContent     = $filecontent;
        $operationobject->FileExtension   = $fileextension;
        $operationobject->FileName        = $filename;
        $operationobject->FilePath        = $filepath;
        $operationobject->FileWildcard    = $filewildcard;
        $operationobject->Image           = $image;
        $operationobject->ImageType       = $imagetype;
      }

      //DATABASE properties
      if (class_exists($queryclass)) {
        //create QueryObject door _set functie in SB_Operation
        $operationobject->QueryClass  = $queryclass;

        $operationobject->QueryObject->ID                        = "Query_" . coalesce(filename_nopath($sqlfilename), uuid()) . "_" . session_id();
        $operationobject->QueryObject->AmountFieldname           = $amountfieldname;
        $operationobject->QueryObject->CellFieldname             = $cellfieldname;
        $operationobject->QueryObject->CellDisplayFieldname      = $celldisplayfieldname;
        $operationobject->QueryObject->CellValueFieldname        = $cellvaluefieldname;
        $operationobject->QueryObject->ColumnFieldname           = $columnfieldname;
        $operationobject->QueryObject->ColumnDisplayFieldname    = $columndisplayfieldname;
        $operationobject->QueryObject->ColumnValueFieldname      = $columnvaluefieldname;
        $operationobject->QueryObject->ColumnsOnly               = $columnsonly;
        $operationobject->QueryObject->ConnectionParameters      = $connectionparameters;
        $operationobject->QueryObject->Cookie                    = $_COOKIE;
        $operationobject->QueryObject->CountMethod               = $countmethod;
        $operationobject->QueryObject->CustomFieldname           = $customfieldname;
        $operationobject->QueryObject->DateFieldname             = $datefieldname;
        $operationobject->QueryObject->DateStart                 = $datestart;
        $operationobject->QueryObject->DateStartFieldname        = $datestartfieldname;
        $operationobject->QueryObject->Date                      = $dateend;
        $operationobject->QueryObject->DateEndFieldname          = $dateendfieldname;
        $operationobject->QueryObject->Debug                     = $debug;
        $operationobject->QueryObject->Developer                 = $developer;
        $operationobject->QueryObject->DistinctFieldname         = $distinctfieldname;
        $operationobject->QueryObject->Duplicate                 = $duplicate;
        $operationobject->QueryObject->Encrypt                   = $encrypt;
        $operationobject->QueryObject->EncryptFieldname          = $encryptfieldname;
        $operationobject->QueryObject->Evaluate                  = $evaluate;
        $operationobject->QueryObject->Fields                    = $fields;
        $operationobject->QueryObject->GroupingFieldname         = $groupingfieldname;
        $operationobject->QueryObject->Hashing                   = $hashing;
        $operationobject->QueryObject->IconFieldname             = $iconfieldname;
        $operationobject->QueryObject->IndexFieldname            = $indexfieldname;
        $operationobject->QueryObject->JSONFieldname             = $jsonfieldname;
        $operationobject->QueryObject->JSONColumns               = $jsoncolumns;
        $operationobject->QueryObject->JSONRequest               = $jsonrequest;
        $operationobject->QueryObject->JSONSubmit                = $jsonsubmit;
        $operationobject->QueryObject->JSONSearch                = $jsonsearch;
        $operationobject->QueryObject->JSONSelection             = $jsonselection;
        $operationobject->QueryObject->JSONResult                = $jsonresult;
        $operationobject->QueryObject->KillThread                = $killthread;
        $operationobject->QueryObject->Limit                     = $limit;
        $operationobject->QueryObject->LocationFieldname         = $locationfieldname;
        $operationobject->QueryObject->Logging                   = $logging;
        $operationobject->QueryObject->LoggingPath               = $logpath;
        $operationobject->QueryObject->LoggingFilename           = $logfilename;
        $operationobject->QueryObject->LoggingTablename          = $logtablename;
        $operationobject->QueryObject->Manipulate                = $manipulate;
        $operationobject->QueryObject->Manipulator               = $manipulator;
        $operationobject->QueryObject->ManipulatorFieldname      = $manipulatorfieldname;
        $operationobject->QueryObject->ManipulatorParams         = $manipulatorparams;
        $operationobject->QueryObject->ManipulatorType           = $manipulatortype;
        $operationobject->QueryObject->Master                    = $master;
        $operationobject->QueryObject->NoCache                   = $nocache;
        $operationobject->QueryObject->Objecting                 = $objecting;
        $operationobject->QueryObject->Operation                 = $operation;
        $operationobject->QueryObject->OperationType             = $operationtype;
        $operationobject->QueryObject->Page                      = $page;
        $operationobject->QueryObject->Params                    = $params;
        $operationobject->QueryObject->ParentFieldname           = $parentfieldname;
        $operationobject->QueryObject->PrimaryFieldName          = $primaryfieldname;
        $operationobject->QueryObject->PrimaryFieldValue         = $primaryfieldvalue;
        $operationobject->QueryObject->Profiling                 = $profiling;
        $operationobject->QueryObject->RemoveComments            = $removecomments;
        $operationobject->QueryObject->ReplaceFieldname          = $replacefieldname;
        $operationobject->QueryObject->ReplaceResult             = $replaceresult;
        $operationobject->QueryObject->ReplaceResultCodes        = $replaceresultcodes;
        $operationobject->QueryObject->ReplaceResultConditions   = $replaceresultconditions;
        $operationobject->QueryObject->ReplaceResultQuotes       = $replaceresultquotes;
        $operationobject->QueryObject->ReplaceResultFunction     = $replaceresultfunction;
        $operationobject->QueryObject->Request                   = $_REQUEST;
        $operationobject->QueryObject->ResultFilename            = $resultfilename;
        $operationobject->QueryObject->ResultFormat              = $resultformat;
        $operationobject->QueryObject->ResultLoad                = $resultload;
        $operationobject->QueryObject->ResultSave                = $resultsave;
        $operationobject->QueryObject->ResultType                = $resulttype;
        $operationobject->QueryObject->RowCount                  = $rowcount;
        $operationobject->QueryObject->RowFieldname              = $rowfieldname;
        $operationobject->QueryObject->RowDisplayFieldname       = $rowdisplayfieldname;
        $operationobject->QueryObject->RowValueFieldname         = $rowvaluefieldname;
        $operationobject->QueryObject->SearchFieldname           = $searchfieldname;
        $operationobject->QueryObject->SearchFieldValue          = $searchfieldvalue;
        $operationobject->QueryObject->SearchPath                = $searchpath;
        $operationobject->QueryObject->SearchParamName           = $searchparamname;
        $operationobject->QueryObject->Session                   = $_SESSION;
        $operationobject->QueryObject->SessionID                 = session_id();
        $operationobject->QueryObject->SortFieldname             = $sortfieldname;
        $operationobject->QueryObject->SortDirection             = $sortdirection;
        $operationobject->QueryObject->SplitPrimary              = $splitprimary;
        $operationobject->QueryObject->SQL                       = $sql;
        $operationobject->QueryObject->SQLCachePath              = $sqlcachepath;
        $operationobject->QueryObject->SQLCustomPath             = $sqlcustompath;
        $operationobject->QueryObject->SQLFilename               = $sqlfilename;
        $operationobject->QueryObject->SQLPath                   = $sqlpath;
        $operationobject->QueryObject->Start                     = $start;
        $operationobject->QueryObject->TableName                 = $tablename;
        $operationobject->QueryObject->TemplateClass             = $templateclass;
        $operationobject->QueryObject->TemplateFilename          = $templatefilename;
        $operationobject->QueryObject->Test                      = $test;
        $operationobject->QueryObject->Timing                    = $timing;
        $operationobject->QueryObject->TimingLimit               = $timinglimit;
        $operationobject->QueryObject->TitleFieldname            = $titlefieldname;
        $operationobject->QueryObject->ValueFieldname            = $valuefieldname;
        $operationobject->QueryObject->Values                    = $values;
        $operationobject->QueryObject->XMLColumns                = $xmlcolumns;
        $operationobject->QueryObject->XMLRequest                = $xmlrequest;
        $operationobject->QueryObject->XMLSubmit                 = $xmlsubmit;
        $operationobject->QueryObject->XMLSearch                 = $xmlsearch;
        $operationobject->QueryObject->XMLSelection              = $xmlselection;
        $operationobject->QueryObject->XMLResult                 = $xmlresult;
      }

      //template properties
      if (class_exists($templateclass)
      && !is_empty($templatefilename)) {
        //create TemplateObject door _set functie in SB_Operation
        $operationobject->TemplateClass  = $templateclass;

        $operationobject->TemplateObject->ID                    = "Template_" . filename_noextension(basename($templatefilename));
        $operationobject->TemplateObject->IDPrefix              = ($templateprefix) ? ($templateprefix . "_") : "";
        $operationobject->TemplateObject->CacheLoad             = $cacheload;
        $operationobject->TemplateObject->CacheLoadJS           = $cacheloadjs;
        $operationobject->TemplateObject->CacheSave             = $cachesave;
        $operationobject->TemplateObject->CacheSaveJS           = $cachesavejs;
        $operationobject->TemplateObject->CacheCompress         = $cachecompress;
        $operationobject->TemplateObject->CacheCompressJS       = $cachecompressjs;
        $operationobject->TemplateObject->CachePath             = $templatecachepath;
        $operationobject->TemplateObject->CachePathFull         = $templatecachepathfull;
        $operationobject->TemplateObject->CachePrefix           = $templateprefix;
        $operationobject->TemplateObject->CacheSuffix           = $templatesuffix;
        $operationobject->TemplateObject->CombineCSSFiles       = $combinecssfiles;
        $operationobject->TemplateObject->CompileCSSFiles       = $compilecssfiles;
        $operationobject->TemplateObject->CombineJavascriptFiles= $combinejavascriptfiles;
        $operationobject->TemplateObject->CompileJavascriptFiles= $compilejavascriptfiles;
        $operationobject->TemplateObject->ContainerID           = $senderid;
        $operationobject->TemplateObject->ContainerJSID         = $senderjsid;
        $operationobject->TemplateObject->ContainerClassID      = $senderclass;
        $operationobject->TemplateObject->Cookie                = $_COOKIE;
        $operationobject->TemplateObject->CustomPath            = $templatecustompath;
        $operationobject->TemplateObject->Debug                 = $debug;
        $operationobject->TemplateObject->Demo                  = $demo;
        $operationobject->TemplateObject->Developer             = $developer;
        $operationobject->TemplateObject->Filename              = $templatefilename;
        $operationobject->TemplateObject->FilenameOutput        = $templatefilenameoutput;
        $operationobject->TemplateObject->Format                = $resultformat;
        $operationobject->TemplateObject->Hashing               = $hashing;
        $operationobject->TemplateObject->Height                = $height;
        $operationobject->TemplateObject->JSONColumns           = $jsoncolumns;
        $operationobject->TemplateObject->JSONRequest           = $jsonrequest;
        $operationobject->TemplateObject->JSONSubmit            = $jsonsubmit;
        $operationobject->TemplateObject->JSONSearch            = $jsonsearch;
        $operationobject->TemplateObject->JSONSelection         = $jsonselection;
        $operationobject->TemplateObject->JSONResult            = $jsonresult;
        $operationobject->TemplateObject->Master                = $master;
        $operationobject->TemplateObject->Objecting             = $objecting;
        $operationobject->TemplateObject->OutputDoctype         = $outputdoctype;
        $operationobject->TemplateObject->OutputHeaders         = $outputheaders;
        $operationobject->TemplateObject->OutputCSSFiles        = $outputcssfiles;
        $operationobject->TemplateObject->OutputJavascriptFiles = $outputjavascriptfiles;
        $operationobject->TemplateObject->Params                = $params;
        $operationobject->TemplateObject->PrimaryFieldName      = $primaryfieldname;
        $operationobject->TemplateObject->PrimaryFieldValue     = $primaryfieldvalue;
        $operationobject->TemplateObject->Profiling             = $profiling;
        $operationobject->TemplateObject->RemoveComments        = true;
        $operationobject->TemplateObject->Session               = $_SESSION;
        $operationobject->TemplateObject->SessionID             = session_id();
        $operationobject->TemplateObject->Request               = $_REQUEST;
        $operationobject->TemplateObject->ResultType            = $resulttype;
        $operationobject->TemplateObject->ResultFormat          = $resultformat;
        $operationobject->TemplateObject->RootPath              = $templaterootpath;
        $operationobject->TemplateObject->ServerLoad            = $serverload;
        $operationobject->TemplateObject->Test                  = $test;
        $operationobject->TemplateObject->Timing                = $timing;
        $operationobject->TemplateObject->TimingLimit           = $timinglimit;
        $operationobject->TemplateObject->TimingOutput          = $timingoutput;
        $operationobject->TemplateObject->Values                = $values;
        $operationobject->TemplateObject->Width                 = $width;
        $operationobject->TemplateObject->XMLColumns            = $xmlcolumns;
        $operationobject->TemplateObject->XMLRequest            = $xmlrequest;
        $operationobject->TemplateObject->XMLSubmit             = $xmlsubmit;
        $operationobject->TemplateObject->XMLSearch             = $xmlsearch;
        $operationobject->TemplateObject->XMLSelection          = $xmlselection;
        $operationobject->TemplateObject->XMLResult             = $xmlresult;

        //template mail properties
        if ($operationobject->TemplateObject instanceof iSB_XMLTemplate_Mail) {
          $operationobject->TemplateObject->Attachment          = $attachment;
          $operationobject->TemplateObject->BCC                 = $bcc;
          $operationobject->TemplateObject->CC                  = $cc;
          $operationobject->TemplateObject->From                = $from;
          $operationobject->TemplateObject->FromName            = $fromname;
          $operationobject->TemplateObject->Layer               = $layer;
          $operationobject->TemplateObject->Password            = $password;
          $operationobject->TemplateObject->Port                = $port;
          $operationobject->TemplateObject->ReplyTo             = $replyto;
          $operationobject->TemplateObject->Server              = $server;
          $operationobject->TemplateObject->Subject             = $subject;
          $operationobject->TemplateObject->To                  = $to;
          $operationobject->TemplateObject->UserName            = $username;
        }
      }

      //real properties
      if (class_exists($realclass)) {
        //create RealObject door _set functie in SB_Operation
        $operationobject->RealClass  = $realclass;

        $operationobject->RealObject->ID                    = "Real_" . $realclass;
      }
    }

    if ($timing) {
      fb_timer_end($timerstart, $timinglimit, "sb_operation_createobject.php : TOTAL");
    }
  //}
?>
