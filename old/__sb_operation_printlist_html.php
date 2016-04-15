<?php
  /*
  * Created on 18 feb 2009
  * by Yuri Sitskoorn
  */

  //DEZE FILE WORDT NIET RECHTSTREEKS GEBRUIKT MAAR GE-INCLUDE DOOR __SB_OPERATION

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

  //tablename             (string)
  //fields                (string   - komma-separated)
  //primaryfieldname      (string)
  //primaryfieldvalue     (string)
  //customfieldname       (string   - komma-separated)
  //distinctfieldname     (string   - komma-separated)

  //amountfieldname       (string)
  //datefieldname         (string)
  //datestartfieldname    (string)
  //dateendfieldname      (string)
  //displayfieldname      (string)
  //groupingfieldname     (string)
  //parentfieldname       (string)
  //locationfieldname     (string)
  //titlefieldname        (string)
  //valuefieldname        (string)

  //countmethod           (string   - methode om rowcount te berekenen (calc/count)
  //duplicate             (boolean  - default false)
  //evaluate              (boolean  - default false)
  //manipulate            (boolean  - default false)
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
  include_once __DIR__ . "/classes/sb/__sb_xmltemplate_report.php";
  include_once __DIR__ . "/classes/mpdf/__mpdf.php";

  //OPERATIONS: PRINTLIST
  class Gridcolumn {
    public $DataType;
    public $Fieldname;
    public $Fixed;
    public $Header;
    public $Hidden;
    public $Renderer;
    public $Summarizable;
    public $SummaryOperation;
    public $SummaryText;
    public $SummaryTotal;
    public $Width;
  }

  //TODO: op basis van een custom template , logo's enz
  //TODO: selecties tonen
  //TODO: totaaltelling onderaan

  //template aanmaken
  //$xmlreport = new SB_XMLTemplate_Report();

  //variabelen initialiseren
  $gridid           =  "";
  $gridcolumns      = array();
  $gridsummarizing  = false;
  $fixedcolumnwidth = 0;
  $imageheight      = 0;
  $imageheights     = array();
  $imagewidth       = 0;
  $imagewidths      = array();
  $totalcolumnwidth = 0;

  $resultset        = array(
    "success"    => "false",
    "message"    => "unknown error"
  );

  //template aanmaken
  //if ($resultarray
  //&&  !empty($resultarray))  {
    //amount & grouping?
    if ($amountfieldname
    &&  $groupingfieldname) {
      $resultarray = json_chart_array($resultarray, $rowcount, $amountfieldname, $groupingfieldnames[0], $groupingfieldnames[1], $totalfieldname);
      $resultarray = $resultarray["data"];
    }

    //gridcolumns samenstellen uit $xmlcolumns
    if ($xmlcolumns) {
      $gridid           = (string)$xmlcolumns->xpath("/xml/grid/@gridid");
      $gridsummarizing  = (boolean)$xmlcolumns->xpath("/xml/grid/@summarizing");

      foreach ($xmlcolumns->children() as $grid) {
        $nodename = $grid->getName();
        if (comparetext($nodename, 'grid')) {
          $count = 0;
          foreach ($grid->children() as $column) {
            $gridcolumn = new Gridcolumn();

            $gridcolumn->Align            = (string)$column["align"];
            $gridcolumn->Fieldname        = (string)$column["fieldname"];
            $gridcolumn->Fixed            = strtobool2((string)$column["fixed"]);
            $gridcolumn->Header           = (string)$column["header"];
            $gridcolumn->Hidden           = strtobool2((string)$column["hidden"]);
            $gridcolumn->Printable        = strtobool2((string)$column["printable"]);
            $gridcolumn->Renderer         = (string)$column["renderer"];
            $gridcolumn->Serverpath       = (string)$column["serverpath"];
            $gridcolumn->Summarizable     = strtobool2((string)$column["summarizable"]);
            $gridcolumn->SummaryOperation = (string)$column["summaryoperation"];
            $gridcolumn->SummaryText      = (string)$column["summarytext"];
            $gridcolumn->DataType         = (string)$column["datatype"];
            $gridcolumn->Width            = (integer)$column["width"];

            if ($gridcolumn->Hidden==true
            ||  $gridcolumn->DataType=="outline"
            ||  $gridcolumn->Width==0
            ||  $gridcolumn->Printable==false) {
              # overslaan
            } else {
              $gridcolumns[$gridcolumn->Fieldname] = $gridcolumn;
              $totalcolumnwidth += $gridcolumn->Width;

              if ($gridcolumn->Fixed) {
                $fixedcolumnwidth += $gridcolumn->Width;
              }

              $count++;
            }
          }
        }
      }
    }


    $gridwidth = 270;

    $validpdf = false;

    $use_xmltemplatereader = true;

    if ($use_xmltemplatereader) {
      $timerstart = timer_start();

      $mpdf = new SB_MPDF();

      $mpdf->ID                 = "Report";
      $mpdf->Session            = $_SESSION;
      $mpdf->Request            = $_REQUEST;
      $mpdf->Params             = "";
      //$mpdf->Filename           = $templatefilename;
      //$mpdf->RootPath           = dirname(__FILE__) . "/../";
      //$mpdf->CustomPath         = dirname(__FILE__) . "/../usersettings/" . stripouterslashes($organisatiepath);

      //$mpdf->readTemplate();

      $timer=timer_start();
      $count = 0;

      $html = '<style> body { font-family: Arial; font-size: 8pt; } </style>';

      //title
      $html .= "<div style='height: auto; background-color: #DDD; font-size: 16pt; font-weight: bold; border: 0.5px solid black; text-align: left;' >" . (($operationtitle) ? $operationtitle : $title) . "&nbsp;&nbsp;</div>\n";
      $html .= "<div style='height: 10px;' ></div>\n";

      //message
      if ($operationmessage) {
        $html .= "<div style='height: 30px;' >" . $operationmessage . "</div>\n";
        $html .= "<div style='height: 10px;' ></div>\n";
      }

      //grid
      if ($resultarray) {
        $html .= "<table border='0' cellspacing='0' cellpadding='0' style='width: 100%; border: 0.5px solid black; vertical-align: top;' >\n";

        //HEADERS
        $html .= "<tr>\n";
        foreach($gridcolumns as $gridcolumn) {
          if (!$gridcolumn->Hidden) {
            //header column
            //$columnwidth = ($gridcolumn->Width / $totalcolumnwidth) * 100 . '%';// * $gridwidth;
            $columnwidth = ($gridcolumn->Fixed) ? $gridcolumn->Width . "px" : ($gridcolumn->Width / ($totalcolumnwidth-$fixedcolumnwidth)) * 100 . '%';// * $gridwidth;

            $html .= "<td style='width: $columnwidth; font-style: italic; text-align: $gridcolumn->Align; background-color: #EEE;' >".$gridcolumn->Header."</td>\n";
          }
        }
        $html .= "</tr>\n";

        //ROWS
        foreach($resultarray as $record) {
          //TODO:pagina's aanmaken en combineren tot 1 PDF
          //if ($count<10) {
            $html .= "<tr>\n";
            foreach($gridcolumns as $gridcolumn) {
              //visible columns
              if (!$gridcolumn->Hidden) {
                //column properties
                //$columnwidth = ($gridcolumn->Width / $totalcolumnwidth) * 100 . '%';// * $gridwidth;
                $columnwidth = ($gridcolumn->Fixed) ? $gridcolumn->Width . "px" : ($gridcolumn->Width / ($totalcolumnwidth-$fixedcolumnwidth)) * 100 . '%';// * $gridwidth;
                $imagestyle  = "";

                //column datatype
                if ($gridcolumn->DataType == "image") {
                  //IMAGE
                  $text = "";

                  //width/height bepalen en bewaren
                  if ($record[$gridcolumn->Fieldname] != null
                  &&  $record[$gridcolumn->Fieldname] != "") {
                    $imagefilename = $record[$gridcolumn->Fieldname];

                    if ($imagefilename) {
                      if (!$imagewidths[$imagefilename]
                      ||  !$imageheights[$imagefilename]) {
                        list($imagewidth, $imageheight) = getimagesize(__DIR__ . "/../" . $imagefilename);

                        $imagewidth  += 10;
                        $imageheight += 10;

                        $imagewidths[$imagefilename]  = $imagewidth;
                        $imageheights[$imagefilename] = $imageheight;
                      }

                      $imagestyle = "background-image: url($imagefilename); background-repeat: no-repeat; background-position: center center; height: " . $imageheights[$imagefilename] . "px;";
                    }
                  }
                } else if ($gridcolumn->DataType == "datetime") {
                  //DATETIME
                  $text = ($record[$gridcolumn->Fieldname]) ? date("d-m-Y H:i", strtotime($record[$gridcolumn->Fieldname])) : "";
                } else if ($gridcolumn->DataType == "date") {
                  //DATE
                  $text = ($record[$gridcolumn->Fieldname]) ? date("d-m-Y", strtotime($record[$gridcolumn->Fieldname])) : "";
                } else if ($gridcolumn->DataType == "time") {
                  //TIME
                  $text = ($record[$gridcolumn->Fieldname]) ? date("H:i", strtotime($record[$gridcolumn->Fieldname])) : "";
                } else {
                  //STRING/NUMBER ETC.
                  $text= $record[$gridcolumn->Fieldname];
                }

                //show column
                $html .= "<td style='width: $columnwidth; text-align: $gridcolumn->Align; vertical-align:middle; $imagestyle;'>";
                $html .= coalesce($text, '&nbsp;');
                $html .= "</td>";
              }

              //column totaal bijhouden voor summary
              if ($gridcolumn->Summarizable) {
                $gridcolumn->SummaryTotal  =  $gridcolumn->SummaryTotal + (float)$text;
              }
            }

            $html .= "</tr>\n";
          //}
          $count++;
        }

        //FOOTER
        if ($gridsummarizing) {
          $html .= "<tr>\n";
          foreach($gridcolumns as $gridcolumn) {
            if (!$gridcolumn->Hidden) {
              //footer column
              //$columnwidth = ($gridcolumn->Width / $totalcolumnwidth) * 100 . '%';// * $gridwidth;
              $columnwidth = ($gridcolumn->Fixed) ? $gridcolumn->Width . "px" : ($gridcolumn->Width / ($totalcolumnwidth-$fixedcolumnwidth)) * 100 . '%';// * $gridwidth;

              if ($gridcolumn->Summarizable) {
                $text  =  $gridcolumn->SummaryTotal;
              } else {
                $text  =  $gridcolumn->SummaryText;
              }

              $html .= "<td style='width: $columnwidth; font-weight: bold; text-align: $gridcolumn->Align; word-wrap:break-word;' >" . $text . "</td>\n";
            }
          }
          $html .= "</tr>\n";
        }

        $html .= "</table>\n";
      }

      //haal linebreaks weg zodat deze niet als spatie worden gezien
      //$html = str_replace("\r", '', str_replace("\n", '', $html));
      $html = str_ireplace("[value:ENTER]"  ,  "<br />",  $html);
      $html = str_ireplace("[value:TAB]"    ,  chr(9) ,  $html);
      $html = str_ireplace("&lt;br /&gt;"   ,  ", "   ,  $html );

      $html=forceUTF8($html);

      //timing
      //fb_timer_end($timerstart, 0, "create html ");


      //FILE OPSLAAN
      $timer= timer_start();

      //dir en filename bepalen
      $today  = date("Ymd", time() );
      $time   = date("His", time() );
      $extension = "pdf";

      $dirname  = dirname(__FILE__) . "/../userdata/" . $_SESSION["project"]["organisatiedirectory"] . "/reports/";
      $filename = "report_" . $_SESSION["account"]["organisatienaam"] . "_" . $title . "_" . $_SESSION["project"]["gebruikerscode"] . "_" . $today . "_" . $time . "." . $extension;

      //zorg dat path bestaat
      mkpath($dirname);

      //maak pdf aan
      if ($use_xmltemplatereader) {
        $mpdf->createPDF($html);
        $mpdf->outputPDF($dirname . $filename);
      } else {
        $mpdf = new mPDF('win-1252','A4-L','','',20,20,20,20,10,10);
        $mpdf->dpi = 96;
        $mpdf->img_dpi = 96;

        //TODO:stylesheets ?!?!
        $mpdf->WriteHTML($html);
        $mpdf->Output($dirname.$filename , "F");
      }

      //timing
      //fb_timer_end($timerstart,0,"create pdf");

      //toon document
      if (file_exists($dirname . $filename)) {
        $serverfilename = $dirname . $filename;
        $clientfilename = "/userdata/" . $_SESSION['current_organisatiedirectory']."/reports/" . $filename;

        //show document
        //header('Location: '.$clientfilename . "#toolbar=1&navpanes=0");
        //download_file($serverfilename);

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

  //logging
  if ($logging) {
    $logtableid   = logtext_totable_insert($query->ConnectionObject->DB, NULL, NULL, "logs", "PHP", "PRINT", "PRINTLIST", $tablename, $primaryfieldname, $primaryfieldvalue, !$resultset["success"], 0, addquotes($resultset["message"]), 0);
  }

  //json result
  echo json_encode($resultset);
?>