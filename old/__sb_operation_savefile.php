<?php
  /*
  * Created on 17 mar 2012
  * by Louck Sitskoorn
  */

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
  //filename              (string)
  //filecontent           (string)

  //OPERATIONS: SAVEFILE

  //framework includes

  //function includes
  include_once __DIR__ . "/../framework/functions/_array_functions.php";
  include_once __DIR__ . "/../framework/functions/_string_functions.php";
  include_once __DIR__ . "/../framework/functions/_file_functions.php";

  //initialize variables
  $resultset = array(
    "success"    => "false",
    "message"    => "unknown error"
  );

  if (isnotempty($filename)) {
    //full filename
    $filefull = dirname(__FILE__) . "/../" . stripfirstslash($filename);

    //make path
    mkpath(filename_path($filefull));

    //save content
    $filehandle = fopen($filefull, "w+");

    if (is_writable($filefull)) {
      fwrite($filehandle, $filecontent);
      fclose($filehandle);

      $resultset = array(
        "success"   => "true",
        "message"   => "file saved successfully",
      );
    } else {
      $resultset = array(
        "success"    => "false",
        "message"    => "file not writable"
      );
    }
  } else {
    $resultset = array(
      "success"    => "false",
      "message"    => "filename empty"
    );
  }

  //json result
  echo json_encode($resultset);
?>