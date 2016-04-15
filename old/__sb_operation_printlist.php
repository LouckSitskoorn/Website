<?php
  /*
  * Created on 18 feb 2009
  * by Yuri Sitskoorn
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

  //OPERATIONS: PRINTLIST

  //framework includes
  include_once __DIR__ . '/classes/sb/__sb_xmltemplate_report.php';

  //OPERATIONS: PRINTLIST
  class Gridcolumn {
    public $Header;
    public $Serverpath;
    public $Fieldname;
    public $Type;
    public $Width;
    public $Renderer;
    public $Hidden;
  }

  //TODO: op basis van een custom template , logo's enz
  //TODO: selecties tonen
  //TODO: totaaltelling onderaan

  //variabelen initialiseren
  $resultset        = array(
    "success"    => "false",
    "message"    => "unknown error"
  );

  //template aanmaken
  $xmlreport = new SB_XMLTemplate_Report();

  //gridcolumns samenstellen uit $xmlcolumns
  $gridcolumns = array();
  $totalcolumnwidth = 0;
  foreach ($xmlcolumns->children() as $grid) {
    $nodename = $grid->getName();
    if (comparetext($nodename, 'grid')) {
      $count = 0;
      foreach ($grid->children() as $column) {
        $gridcolumn = new Gridcolumn();

        $gridcolumn->Align      = (string)$column['align'];
        $gridcolumn->Fieldname  = (string)$column['fieldname'];
        $gridcolumn->Header     = (string)$column['header'];
        $gridcolumn->Hidden     = strtobool2((string)$column['hidden']);
        $gridcolumn->Width      = (integer)$column['width'];
        $gridcolumn->Renderer   = (string)$column['renderer'];
        $gridcolumn->Serverpath = (string)$column['serverpath'];
        $gridcolumn->Type       = (string)$column['type'];

        if ($gridcolumn->Hidden==true || $gridcolumn->Type=='outline' || $gridcolumn->Width==0) {
          # overslaan
        } else {
          $gridcolumns[$gridcolumn->Fieldname] = $gridcolumn;
          $totalcolumnwidth += $gridcolumn->Width;

          $count++;
        }
      }
    }
  }

  $gridwidth = 270;


  //template xml aanmaken adhv $resultarray
  $templatexml = "<report id='report' orientation='L' unit='mm' format='A4' topmargin='10' leftmargin='10' rightmargin='10' displaypages='false'>
                    <page id='contract' fontname='Arial' fontsize='10'>
                    <panel id='header' border='true' text='$title ' float='right' align='right' valign='middle' width='100%' height='14' fontsize='16' bold='true' backgroundcolor='#EEEEEE' transparent='false' /><br/>
                    <br/>
                    <br rowheight='5'/>
                 ";


  //headers
  $templatexml .= "<table border='false' float='left' cangrow='true' fontsize='8' bold='true'>
                  ";

  foreach($gridcolumns as $gridcolumn) {
    if (!$gridcolumn->Hidden) {
      //header column
      $columnwidth = ($gridcolumn->Width / $totalcolumnwidth) * 100 . '%';// * $gridwidth;
      $templatexml .= "<cell text='$gridcolumn->Header' cangrow='true' border='true' align='$gridcolumn->Align' valign='top' width='$columnwidth'  backgroundcolor='#EEEEEE' transparent='false' />";
    }
  }
  $templatexml .= "</table>";

  //records
  if ($resultarray) {
    $count = 0;
    foreach($resultarray as $record) {
      $templatexml .= "
        <table id='tbl$count' border='false' float='left' cangrow='true' fontsize='8' bold='false' >
      ";

      foreach($gridcolumns as $gridcolumn) {
        if (!$gridcolumn->Hidden) {
          //record column
          $columnwidth = ($gridcolumn->Width / $totalcolumnwidth) * 100 . '%';// * $gridwidth;
          if ($gridcolumn->Type == 'image') {
            $text = '';
            //$filename = addlastslash($gridcolumn->Serverpath).stripfirstslash($record[$gridcolumn->Fieldname]);

            if ($record[$gridcolumn->Fieldname] != null
            &&  $record[$gridcolumn->Fieldname] != "") {
              $filename = addlastslash(dirname(__FILE__) . "/../") . stripfirstslash($record[$gridcolumn->Fieldname]);
            } else {
              $filename = "";
            }
          } else {
            $text= $record[$gridcolumn->Fieldname];
            $text=str_ireplace("'", "&quot;",$text);
            $text=strip_tags($text);

            $filename = '';
          }

          $templatexml .= "<cell header='$gridcolumn->Header' type='$gridcolumn->Type' filename='$filename' text='$text' cangrow='true' border='true' align='$gridcolumn->Align' valign='top' width='$columnwidth' fontsize='8' bold='false' transparent='true'/>";
        }
      }
      $count++;

    $templatexml .= "</table>";
    }
  }

  $templatexml .= "
  </page>
  </report>
  ";

  //van templatexml een pdf maken en die tonen
  if ($templatexml) {
    //TODO:pagina's aanmaken en combineren tot 1 PDF
    $xmlreport->readFromString($templatexml);

    $xmlreport->parsePDF();
    $xmlreport->createPDF();

    //dir en filename bepalen
    $today  = date("Ymd", time() );
    $time   = date("His", time() );

    //TODO: session variabelen vervangen door parameters
    $dirname  = dirname(__FILE__) . "/../userdata/".$_SESSION['current_organisatiedirectory']."/reports/";
    $filename = "report_".$_SESSION["account"]["organisatienaam"]."_".$_SESSION['current_gebruikerscode']."_".$today."_".$time.".pdf";

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
      $clientfilename = "/userdata/" . $_SESSION['current_organisatiedirectory']."/reports/" . $filename;

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

  //logging
  if ($logging) {
    $logtableid   = logtext_totable_insert($query->ConnectionObject->DB, NULL, NULL, "logs", "PHP", "PRINT", "PRINTLIST", $tablename, $primaryfieldname, $primaryfieldvalue, !$resultset["success"], 0, addquotes($resultset["message"]), 0);
  }

  //json result
  echo json_encode($resultset);
?>