<?
  /*
  * Created on 1 feb 2012
  * by Louck Sitskoorn
  */

  //session_start();

  //DEZE FILE WORDT NIET RECHTSTREEKS GEBRUIKT MAAR GE-INCLUDE DOOR __SB_OPERATION

  //PARAMETERS:
  //operation             (string   - ADD/VIEW/EDIT/ etc.)
  //operationtitle        (string   - titel voor bepaalde operaties bijv. 'Verwijderen')
  //operationinclude      (string   - file die ge-include moet worden)

  //connection            (object   - SB_ConnectionParameters)
  //sql                   (string   - SQL statement)
  //sqlfilename           (string   - SQL filename)
  //sqlpath               (string   - SQL path)
  //sqlcustompath         (string   - SQL custompath)
  //sqlcachepath          (string   - SQL cachepath)

  //tablename             (string)
  //fields                (string   - komma-separated)
  //primaryfieldname      (string)
  //primaryfieldvalue     (string)

  //customfieldname       (string   - komma-separated)
  //distinctfieldname     (string   - komma-separated)
  //parentfieldname       (string)
  //amountfieldname       (string)
  //groupingfieldname     (string)
  //datefieldname         (string)
  //datestartfieldname    (string)
  //dateendfieldname      (string)
  //locationfieldname     (string)
  //titlefieldname        (string)
  //countmethod           (string   - methode om rowcount te berekenen (calc/count)
  //evaluate              (boolean  - default false)
  //nocache               (boolean  - default false)
  //replacefieldname      (string   - velden die in resultarray vervangen moeten worden)
  //replaceresult         (boolean  - default true)
  //splitprimary          (boolean  - true als query met sqlparts werkt (  /*[SQLStart]*/ etc. )
  //timing                (boolean  - default false)
  //start                 (integer)
  //limit                 (integer)
  //sortfieldname         (string   - sorteer veldnaam)
  //sortdirection         (string   - sorteer richting (ASC/DESC))
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
  //url                   (string)

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
  //cc                    (string   - mail cc emailadressen)
  //bcc                   (string   - mail bcc emailadressen)

  //startdate             (integer  - calendar startdate (unix timestamp) )
  //enddate               (integer  - calendar enddate (unix timestamp) )

  //containerid           (string)
  //height                (integer)
  //width                 (integer)

  //OPERATIONS: EXPORT


  //framework includes
  include_once __DIR__ . "/classes/sb/__sb_xmltemplate_export.php";


  //initialize variables
  $filename         = "";
  $resultset = array(
    "success"    => "false",
    "message"    => "unknown error"
  );

  //template inlezen
  //if ($resultarray
  //&&  !empty($resultarray))  {
    if (isnotempty($templatefilename)) {
      //create export object
      $exporttemplate = new SB_XMLTemplate_Export();

      $exporttemplate->ID                 = "ExportTemplate";
      //$exporttemplate->ContainerID        = $senderid;
      //$exporttemplate->ContainerJSID      = $senderjsid;
      //$exporttemplate->ContainerClass     = $senderclass;
      $exporttemplate->CustomPath         = dirname(__FILE__) . "/../usersettings/" . stripouterslashes($organisatiepath);
      $exporttemplate->Filename           = $templatefilename;
      $exporttemplate->Format             = $resultformat;
      $exporttemplate->Params             = $params;
      $exporttemplate->PrimaryFieldName   = $primaryfieldname;
      $exporttemplate->PrimaryFieldValue  = $primaryfieldvalue;
      $exporttemplate->Request            = $_REQUEST;
      $exporttemplate->RootPath           = dirname(__FILE__) . "/../";
      $exporttemplate->Session            = $_SESSION;

      //template lezen en parsen
      $exporttemplate->readTemplate();
      $exporttemplate->init();

      //aanmaken export file
      $filename =   __DIR__ . "/../" . $exporttemplate->ExportFilename;

      //download export file
      if (isnotempty($filename)
      &&  file_exists($filename)) {
        $serverfilename = $filename;
        $clientfilename = urlencode("/userdata/" . $_SESSION["project"]["organisatiedirectory"] . "/exports/" . basename($filename));

        //download file
        //download_file($serverfilename);

        $resultset = array(
          "success"    => "true",
          "message"    => "file succesfully exported",
          "filename"   => $clientfilename
        );
      } else {
        $resultset = array(
          "success"    => "false",
          "message"    => "file not found",
          "filename"   => $clientfilename
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

  // /json result
  echo json_encode($resultset);
?>