<?php
	/*
	* Created on 8 dec 2008
	* by Louck Sitskoorn
	*/

  //DEZE FILE WORDT NIET RECHTSTREEKS GEBRUIKT MAAR GE-INCLUDE DOOR __SB_OPERATION

  //PARAMETERS:
  //operation             (string   - ADD/VIEW/EDIT/ etc.)
  //operationtitle        (string   - titel voor bepaalde operaties bijv. 'Verwijderen')
  //connection            (object   - SB_ConnectionParameters)
  //sql                   (string   - SQL statement)
  //sqlfilename           (string   - SQL filename)
  //tablename             (string)
  //fields                (string   - komma-separated)
  //primaryfieldname      (string)
  //primaryfieldvalue     (string)
  //customfieldnames      (string   - komma-separated)
  //distinctfieldnames    (string   - komma-separated)
  //parentfieldname       (string)
  //nocache               (boolean  - default false)
  //replacefieldname      (string   - velden die in resultarray vervangen moeten worden)
  //replaceresult         (boolean  - default true)
  //splitprimary          (boolean  - true als query met sqlparts werkt (  /*[SQLStart]*/ etc. )
  //start                 (integer)
  //limit                 (integer)
  //sort                  (string   - sorteer veldnaam)
  //dir                   (string   - sorteer richting (ASC/DESC))
  //page                  (integer  - default  0)
  //rowcount              (integer  - default -1)
  //templateclass         (string   - class van template (voor mail))
  //templatecustompath    (string   - custom path van template (voor mail))
  //templatefilename      (string   - filename van template (voor mail))
  //templaterootpath      (string   - rootpath van template (voor mail))
  //templateprefix        (string   - prefix van template (voor mail))
  //templatesuffix        (string   - prefix van template (voor mail))
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

  //logging               (boolean  - true als queries gelogd moeten worden)
  //logfilename           (string   - log filename)
  //logpath               (string   - log path)
  //logtablename          (string   - log tablename)

  //server                (string   - mail server)
  //username              (string   - mail username)
  //password              (string   - mail password)
  //subject               (string   - mail subject)
  //from                  (string   - mail from emailadressen)
  //to                    (string   - mail to emailadressen)
  //replyto               (string   - mail replyto emailadressen)
  //cc                    (string   - mail cc emailadressen
  //bcc                   (string   - mail bcc emailadressen

  //OPERATIONS: PRINTITEM

  //framework includes
  include_once __DIR__ . "/classes/sb/__sb_xmltemplate_report.php";

  //initialize variables
  $filename         = "";
  $resultset        = array(
    "success"    => "false",
    "message"    => "unknown error"
  );

  //template aanmaken
  //if ($resultarray
  //&&  !empty($resultarray))  {
    if (isnotempty($templatefilename)) {
      //template aanmaken
      $xmlfilenames = explode("," , $template);
      $xmlreport = new SB_XMLTemplate_Report();

      $xmlreport->ID                = "Report";
      //$xmlreport->IDPrefix  = $moduleprefix . "_";
      //$xmlreport->ContainerID       = $senderid;
      //$xmlreport->ContainerJSID     = $senderjsid;
      //$xmlreport->ContainerClass    = $senderclass;
      $xmlreport->CustomPath        = __DIR__ . "/../usersettings/" . stripouterslashes($organisatiepath);
      $xmlreport->Filename          = $templatefilename;
      $xmlreport->Params            = $params;
      $xmlreport->Result            = $result;
      $xmlreport->ResultArray       = $resultarray;
      $xmlreport->Request           = $_REQUEST;
      $xmlreport->RootPath          = __DIR__ . "/../";
      $xmlreport->Session           = $_SESSION;
      $xmlreport->Values            = $values;

      $xmlreport->readTemplate();
      $xmlreport->init();

      //parse template
      $xmlreport->parsePDF();

      //create PDF
      $xmlreport->createPDF();

      //dir en filename bepalen
      $today  = date("Ymd", time() );
      $time   = date("His", time() );

      //TODO: session variabelen vervangen door parameters
      $dirname  = dirname(__FILE__) . "/../userdata/" . $_SESSION["project"]["organisatiedirectory"]."/reports/";
      $filename = "report_".$_SESSION["account"]["organisatienaam"] . "_" . $_SESSION["project"]["gebruikerscode"] . "_" . $today . "_" . $time . ".pdf";

      //bestaat directory "usersettings/organisatienaam" al ?
      if (!file_exists($dirname)) {
        //nee, dus directory aanmaken
          mkpath($dirname);
      }

      //schrijf report weg naar dirname/filename
      $xmlreport->outputPDF($dirname . $filename);
      $xmlreport->close();

      unset($xmlreport->PDF);

      //toon report
      if (file_exists($dirname . $filename)) {
        $serverfilename = $dirname . $filename;
        $clientfilename = "/userdata/".$_SESSION['current_organisatiedirectory']."/reports/" . $filename;

        $resultset = array(
          "success"   => "true",
          "message"   => "report created successfully",
          "filename"  => $clientfilename
        );
      } else {
        $resultset = array(
          "success"   => "false",
          "message"   => "report not created",
          "filename"  => $clientfilename
        );
      }
    } else {
      $resultset = array(
        "success"    => "false",
        "message"    => "template empty"
      );
    }
  //} else {
  //  $resultset = array(
  //    "success"    => "false",
  //    "message"    => "result empty"
  //  );
  //}

  //json result
  echo json_encode($resultset);
?>