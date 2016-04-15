<?php
  /*
  * Created on 24 juli 2009
  * by Louck Sitskoorn
  */

  //REQUEST PARAMETERS:
  //cacheload
  //cachesave
  //cachecompress
  //debug
  //demo
  //hashing
  //logging
  //test
  //timing
  //operation
  //params
  //templatefilename
  //templaterootpath
  //templatecustompath
  //templatecachepath
  //templatetemppath
  //templateprefix
  //primaryfieldname
  //primaryfieldvalue
  //masterfieldname
  //masterfieldvalue
  //detailfieldname
  //detailfieldvalue
  //outputdoctype
  //outputheader
  //outputcss
  //outputjavascriptfiles
  //senderid
  //senderjsid
  //senderclass
  //externoptieprofielid
  //externgebruikersprofielid
  //externtaalid
  //externorganisatiedirectory

  //session start
  session_start();

  header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i ") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");

  if ($_SESSION["vsid"]) {
    //INCLUDES autoload
    require_once __DIR__ . "/__sb_autoload.php";

	  //framework includes
	  include_once __DIR__ . "/classes/sb/__sb_xmltemplatepage.php";

	  //function includes
	  include_once __DIR__ . "/functions/_string_functions.php";
    include_once __DIR__ . "/functions/_error_functions.php";
    include_once __DIR__ . "/functions/_encryption_functions.php";
    include_once __DIR__ . "/functions/_memory_functions.php";

    //error reporting
    //error_reporting($_SESSION["error_reporting"]);
    //error_fatal($_SESSION["error_fatal"]);
    //set_error_handler('error_handler');

    //timer
    $timerstart   =timer_start();
    $memoryusage  = new MemoryUsageInformation(true);
    $memoryusage->setStart();
    $memorystart  = $memoryusage->getPeakMemoryUsage();

	  //initialisatie
	  //$modulenaam                 = "";
	  //$modulepath                 = "";
	  //$moduleprefix               = "";
	  $cacheload                  = false;
	  $cachesave                  = false;
	  $cachecompress              = false;
	  $debug                      = false;
	  $demo                       = false;
	  $hashing                    = false;
    $profiling                  = false;
	  $logging                    = false;
	  $test                       = false;
	  $timing                     = false;
	  $operation                  = "";
	  $operationinclude           = "";
	  $operationtitle             = "";
    $params                     = "";
    $values                     = "";
    $senderid                   = "";
    $senderjsid                 = "";
    $senderclass                = "";
    $primaryfieldname           = "";
    $primaryfieldvalue          = "";
    $masterfieldname            = "";
    $masterfieldvalue           = "";
    $detailfieldname            = "";
    $detailfieldvalue           = "";
    $detailfieldvalues          = array();
    $detailfieldarray           = array();
    $doctype                    = "quirk";
    $outputdoctype              = true;
    $outputheaders              = true;
    $outputcss                  = false;
    $outputcssfiles             = false;
    $outputjavascript           = true;
    $outputjavascriptfiles      = false;
    $outputevalcssfiles         = true;
    $outputevaljavascriptfiles  = true;
    $serverload                 = false;
    $templatefilename           = "";
    $templateparams             = "";
    $templateparamvalues        = "";
    $templaterootpath           = dirname(__FILE__) . "/..";
    $templatecustompath         = "";
    $templatecachepath          = "/temp/cache";
    $templatecachepathfull      = dirname(__FILE__) . "/../temp/cache";
    $templatetemppath           = dirname(__FILE__) . "/../temp";
    $templateclass              = "SB_XMLTemplatePage";
    $templateprefix             = "";
    $templatesuffix             = "template";
    $cachefilename              = "";
    $xmlsearch                  = null;
    $xmlselection               = null;
    $xmlsubmit                  = null;

	  //read parameters
    $cacheload              = (isset($_REQUEST["cacheload"])) ? strtobool2($_REQUEST["cacheload"]) : $cacheload;
    $cachesave              = (isset($_REQUEST["cachesave"])) ? strtobool2($_REQUEST["cachesave"]) : $cachesave;
    $cachecompress          = (isset($_REQUEST["cachecompress"])) ? strtobool2($_REQUEST["cachecompress"]) : $cachecompress;
    $demo                   = (isset($_REQUEST["demo"])) ? strtobool2($_REQUEST["demo"]) : $demo;
    $debug                  = (isset($_REQUEST["debug"])) ? strtobool2($_REQUEST["debug"]) : $debug;
    $hashing                = (isset($_REQUEST["hashing"])) ? strtobool2($_REQUEST["hashing"]) : $hashing;
    $logging                = (isset($_REQUEST["logging"])) ? strtobool2($_REQUEST["logging"]) : $logging;
    $test                   = (isset($_REQUEST["test"])) ? strtobool2($_REQUEST["test"]) : $test;
    $timing                 = (isset($_REQUEST["timing"])) ? strtobool2($_REQUEST["timing"]) : $timing;

    $senderid               = (isset($_REQUEST["senderid"])) ? $_REQUEST["senderid"] : $senderid;
    $senderjsid             = (isset($_REQUEST["senderjsid"])) ? $_REQUEST["senderjsid"] : $senderjsid;
    $senderclass            = (isset($_REQUEST["senderclass"])) ? $_REQUEST["senderclass"] : $senderclass;

    $templatefilename       = (isset($_REQUEST["templatefilename"]) && isnotempty($_REQUEST["templatefilename"]) && strtolower($_REQUEST["templatefilename"]) != 'false') ? $_REQUEST["templatefilename"] : $templatefilename;
    $templateparams         = (isset($_REQUEST["templateparams"]) && isnotempty($_REQUEST["templateparams"]) && strtolower($_REQUEST["templateparams"]) != 'false') ? $_REQUEST["templateparams"] : $templateparams;
    $templaterootpath       = (isset($_REQUEST["templaterootpath"]) && isnotempty($_REQUEST["templaterootpath"]) && strtolower($_REQUEST["templaterootpath"]) != 'false') ? "../" . stripouterslashes($_REQUEST["templaterootpath"]) : $templaterootpath;
    $templatecustompath     = (isset($_REQUEST["templatecustompath"]) && isnotempty($_REQUEST["templatecustompath"]) && strtolower($_REQUEST["templatecustompath"]) != 'false') ?  "../" . stripouterslashes($_REQUEST["templatecustompath"]) : $templatecustompath;
    $templatecachepath      = (isset($_REQUEST["templatecachepath"]) && isnotempty($_REQUEST["templatecachepath"]) && strtolower($_REQUEST["templatecachepath"]) != 'false') ?  $_REQUEST["templatecachepath"] : $templatecachepath;
    $templatecachepathfull  = (isset($_REQUEST["templatecachepath"]) && isnotempty($_REQUEST["templatecachepath"]) && strtolower($_REQUEST["templatecachepath"]) != 'false') ?  "../" . stripouterslashes($_REQUEST["templatecachepath"]) : $templatecachepathfull;
    $templatetemppath       = (isset($_REQUEST["templatetemppath"]) && isnotempty($_REQUEST["templatetemppath"]) && strtolower($_REQUEST["templatetemppath"]) != 'false') ?  "../" . stripouterslashes($_REQUEST["templatetemppath"]) : $templatetemppath;

	  if (isset($_REQUEST["templateclass"]) && isnotempty($_REQUEST["templateclass"])) {
	    if (isnotempty($_REQUEST["templateclass"]) && strtolower($_REQUEST["templateclass"]) != 'false') {
	      $templateclass = $_REQUEST["templateclass"];
	    }
	  }
	  if (isset($_REQUEST["templateprefix"]) && isnotempty($_REQUEST["templateprefix"])) {
	    if (isnotempty($_REQUEST["templateprefix"]) && strtolower($_REQUEST["templateprefix"]) != 'false') {
	      $templateprefix = $_REQUEST["templateprefix"];
	    }
	  }

    if (isset($_REQUEST["templatesuffix"]) && isnotempty($_REQUEST["templatesuffix"])) {
      if (isnotempty($_REQUEST["templatesuffix"]) && strtolower($_REQUEST["templatesuffix"]) != 'false') {
        $templatesuffix = $_REQUEST["templatesuffix"];
      }
    }

    $operation              = (isset($_REQUEST["operation"])) ? $_REQUEST["operation"] : $operation;
    $operationinclude       = (isset($_REQUEST["operationinclude"])) ? $_REQUEST["operationinclude"] : $operationinclude;
    $operationtitle         = (isset($_REQUEST["operationtitle"]))? $_REQUEST["operationtitle"] : $operationtitle;
    $params                 = (isset($_REQUEST["params"])) ? $_REQUEST["params"] : $params;
    $values                 = (isset($_REQUEST["values"])) ? $_REQUEST["values"] : $values;
    $primaryfieldname       = (isset($_REQUEST["primaryfieldname"])) ? $_REQUEST["primaryfieldname"] : $primaryfieldname;
    $primaryfieldvalue      = (isset($_REQUEST["primaryfieldvalue"])) ? $_REQUEST["primaryfieldvalue"] : $primaryfieldvalue;
    $masterfieldname        = (isset($_REQUEST["masterfieldname"])) ? $_REQUEST["masterfieldname"] : $masterfieldname;
    $masterfieldvalue       = (isset($_REQUEST["masterfieldvalue"])) ? $_REQUEST["masterfieldvalue"] : $masterfieldvalue;
    $detailfieldname        = (isset($_REQUEST["detailfieldname"])) ? $_REQUEST["detailfieldname"] : $detailfieldname;
    $detailfieldvalue       = (isset($_REQUEST["detailfieldvalue"])) ? $_REQUEST["detailfieldvalue"] : $detailfieldvalue;

    $outputdoctype          = (isset($_REQUEST["outputdoctype"])) ? strtobool($_REQUEST["outputdoctype"]) : $outputdoctype;
    $outputheaders          = (isset($_REQUEST["outputheaders"])) ? strtobool($_REQUEST["outputheaders"]) : $outpurheaders;
    $outputcssfiles         = (isset($_REQUEST["outputcssfiles"])) ? strtobool($_REQUEST["outputcssfiles"]) : $outputcssfiles;
    $outputjavascriptfiles  = (isset($_REQUEST["outputjavascriptfiles"])) ? strtobool($_REQUEST["outputjavascriptfiles"]) : $outputjavascriptfiles;
    $outputjavascript       = (isset($_REQUEST["outputjavascript"])) ? strtobool($_REQUEST["outputjavascript"]) : $outputjavascript;

//$_REQUEST["serverload"] = "false";
    $serverload             = (isset($_REQUEST["serverload"])) ? strtobool($_REQUEST["serverload"]) : $serverload;

    $xmlsubmit              = (isset($_REQUEST["xmlsubmit"])) ? (isnotempty($_REQUEST["xmlsubmit"]) && strtolower($_REQUEST["xmlsubmit"]) != "false" && strtolower($_REQUEST["xmlsubmit"]) != "undefined") ? simplexml_load_string($_REQUEST["xmlsubmit"]) : $xmlsubmit : $xmlsubmit;
    $xmlsearch              = (isset($_REQUEST["xmlsearch"])) ? (isnotempty($_REQUEST["xmlsearch"]) && strtolower($_REQUEST["xmlsearch"]) != "false" && strtolower($_REQUEST["xmlsearch"]) != "undefined") ? simplexml_load_string($_REQUEST["xmlsearch"]) : $xmlsearch : $xmlsearch;
    $xmlselection           = (isset($_REQUEST["xmlselection"])) ? (isnotempty($_REQUEST["xmlselection"]) && strtolower($_REQUEST["xmlselection"]) != "false" && strtolower($_REQUEST["xmlselection"]) != "undefined") ? simplexml_load_string($_REQUEST["xmlselection"]) : $xmlselection : $xmlselection;

    //decrypting
    $masterfieldname=decryptConvert(urldecode($masterfieldname));
    $masterfieldvalue=decryptConvert(urldecode($masterfieldvalue));
    $primaryfieldname=decryptConvert(urldecode($primaryfieldname));
    $primaryfieldvalue=decryptConvert(urldecode($primaryfieldvalue));

    //failback cachepath
    if (isempty($templatecachepath)) {
      $templatecachepath  = "temp/" . basename($templatecustompath);
    }

    //template paramvalues bepalen
    /*
    $parameterseparator = "";
    $parameters         = explode_assoc(";", $params);
    foreach($parameters as $key=>$value) {
      if (trim($key)=="actiesoortid"
      ||  trim($key)=="optieprofielid"
      ||  trim($key)=="gebruikersprofielid"
      ||  trim($key)=="externoptieprofielid"
      ||  trim($key)=="externgebruikersprofielid") {
        if (isnotempty($value)) {
          $templateparamvalues  .=  $parameterseparator . $value;

          $parameterseparator = "_";
        }
      }
    }
    if (isnotempty($operation)
    &&  $operation != "[request:operation]") {
      $templateparamvalues    .= $parameterseparator . $operation;
    }
    */

    if (!is_empty($params)
    &&  stripos($params, "moduleoptieid") !== false) {
      $_SESSION["project"]["moduleoptieid"]  = coalesce(getkeyvalue($params, "moduleoptieid", ";"), "MODULE_ALGEMEEN");
    }

    //INCLUDE operationinclude
    if (isnotempty($operationinclude)) {
      include_once __DIR__ . "/../" . $operationinclude;
    }

    //Nieuw Page object opbouwen
    $page  = new SB_XMLTemplatePage();

    $page->ID                         = "TemplatePage";
    $page->IDPrefix                   = ($templateprefix) ? $templateprefix . "_" : "";

    $page->Session                    = $_SESSION;
    $page->Request                    = $_REQUEST;
    $page->Params                     = $params;
    $page->PrimaryFieldName           = $primaryfieldname;
    $page->PrimaryFieldValue          = $primaryfieldvalue;
    $page->Evaluate                   = true;

    $page->ContainerID                = $senderid;
    $page->ContainerJSID              = $senderjsid;
    $page->ContainerClassID           = $senderclass;

    $page->CachePrefix                = $templateprefix;
    $page->CacheSuffix                = $templatesuffix;  //$templateparamvalues . "_" . hash("crc32", $senderid) . "_" . $templatesuffix;
    //$page->CacheSuffix                = hash("crc32", $senderid) . "_" . $templatesuffix;
    $page->CacheLoad                  = $cacheload;
    $page->CacheSave                  = $cachesave;
    $page->CacheCompress              = $cachecompress;
    $page->Filename                   = $templatefilename;
    $page->RootPath                   = $templaterootpath;
    $page->CustomPath                 = $templatecustompath;
    $page->CachePath                  = $templatecachepath;
    $page->CachePathFull              = $templatecachepathfull;

    $page->OutputDoctype              = $outputdoctype;
    $page->OutputHeaders              = $outputheaders;
    $page->OutputCSSFiles             = $outputcssfiles;
    $page->OutputEvalCSSFiles         = $outputevalcssfiles;
    $page->OutputEvalJavascriptFiles  = $outputevaljavascriptfiles;
    $page->OutputJavascriptFiles      = $outputjavascriptfiles;
    $page->OutputJavascript           = $outputjavascript;

    $page->Debug                      = $debug;
    $page->Demo                       = $demo;
    $page->Hashing                    = $hashing;
    $page->Logging                    = $logging;
    $page->Test                       = $test;
    $page->Timing                     = $timing;

    //$page->XMLRequest                 = $xmlsubmit;
    $page->XMLSubmit                  = $xmlsubmit;
    $page->XMLSearch                  = $xmlsearch;
    $page->XMLSelection               = $xmlselection;

    //template inlezen
    $page->readTemplate();

    //template parsen? (kan al door cachesave gebeurd zijn)
    if (!$page->Inited) {
      $page->init();
    }

    //template output ophalen
    $output = $page->outputAll();


    //Just-In-Time replace
    $output = jitReplace($output, $params, $xmlsubmit, $serverload, $timing);

    //Just-In-Time replace jit detailfieldvalue
    if ($detailfieldvalue) {
      $detailfieldvalues  = explode(",", $detailfieldvalue);

      foreach ($detailfieldvalues as $key=>$detailrecordvalue) {
        $detailfieldarray["detailfieldvalue"][$key+1] = trim($detailrecordvalue);
      }

      //foreach ($detailfieldvalues as $key=>$detailrecordvalue) {
      //  $keycounter  = $key + 1;
      //  $output = str_ireplace("[jit:detailfieldvalue[$keycounter]]", $detailrecordvalue, $output);
      //}
      $output = replace_variables($output, "jit", $detailfieldarray);
    }


	  //total timer
    if ($timing) {
      fb_timer_end($timerstart, 0, "__sb_templateshow.php : total template (" . $template . ")");
    }

    //get memory usage
    $memoryend  = $memoryusage->getPeakMemoryUsage();

    //total logging
    if ($logging) {
      //Connection aanmaken
      $connection = new SB_Connection();
      $connection->Server       = $_SESSION["project"]["database"]["server"];
      $connection->DatabaseName = $_SESSION["project"]["database"]["databasename"];
      $connection->User         = $_SESSION["project"]["database"]["user"];
      $connection->Password     = $_SESSION["project"]["database"]["password"];
      $connection->connect();

      $logtableid   = logtext_totable_insert($connection->DB, NULL, NULL, "logs", "PHP", "TEMPLATE", NULL, NULL, NULL, NULL, NULL, false, 0, "Memory usage : " . $memoryusage->getPeakMemoryUsage(), 0);
    }


    //OUTPUT
    echo $output;
  } else {
    //header("HTTP/1.0 500 Internal Server Error");
    echo ("<script>if (typeof success_SB_JQuery_DB == 'function') {success_SB_JQuery_DB({success:false});}</script>");
  }
?>