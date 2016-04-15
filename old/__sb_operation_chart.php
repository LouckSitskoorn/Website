<?php
  /*
   * Created on 23 mrt 2011
   * by Louck Sitskoorn
  */

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

  //framework includes
  include_once __DIR__ . "/../framework/classes/sb/__sb_chart.php";

  //function includes
  include_once __DIR__ . "/../framework/functions/_array_functions.php";
  include_once __DIR__ . "/../framework/functions/_string_functions.php";

  //initialize variables
  $dirname          = "";
  $filename         = "";
  $serverfilename   = "";
  $clientfilename   = "";
  $resultset = array(
    "success"    => "false",
    "message"    => "unknown error"
  );

  if ($resultarray
  && !empty($resultarray)) {
    $datasoortarray               = $xmlsubmit->xpath('/xml/record/field[@fieldname="DatasoortID"]');
    $datasoortid                  = (string)$datasoortarray[0];
    $datasoorttitle               = (string)$datasoortarray[0]["displaytext"];

    $grafieksoortarray            = $xmlsubmit->xpath('/xml/record/field[@fieldname="GrafieksoortID"]');
    $grafieksoortid               = (string)$grafieksoortarray[0];

    $grafiektypearray             = $xmlsubmit->xpath('/xml/record/field[@fieldname="GrafiektypeID"]');
    $grafiektypetid               = (string)$grafiektypearray[0];

    $groeperingsoortarray1        = $xmlsubmit->xpath('/xml/record/field[@fieldname="GroeperingsoortID1"]');
    $groeperingsoortid1           = (string)$groeperingsoortarray1[0];
    $groeperingsoorttitle1        = (string)$groeperingsoortarray1[0]["displaytext"];

    $groeperingsoortarray2        = $xmlsubmit->xpath('/xml/record/field[@fieldname="GroeperingsoortID2"]');
    $groeperingsoortid2           = (isnotempty((string)$groeperingsoortarray2[0])) ? (string)$groeperingsoortarray2[0] : ((isnotempty((string)$groeperingsoortarray2[1])) ? (string)$groeperingsoortarray2[1] : '');
    $groeperingsoorttitle2        = (string)$groeperingsoortarray2[0]["displaytext"];

    $legendimagearray             = $xmlsubmit->xpath('/xml/record/field[@fieldname="Legenda"]');
    $legendimage                  = strtobool2((string)$legendimagearray[0]);

    $displaylegendarray           = $xmlsubmit->xpath('/xml/record/field[@fieldname="Legenda"]');
    $displaylegend                = strtobool2((string)$displaylegendarray[0]);

    $displayvaluesarray           = $xmlsubmit->xpath('/xml/record/field[@fieldname="Waarden"]');;
    $displayvalues                = strtobool2((string)$displayvaluesarray[0]);


    switch ($grafieksoortid)  {
      case "GRAFIEK_STAAF" :
        $charttype      = "bar";
        $normalize      = false;
        break;
      case "GRAFIEK_STAPEL" :
        $charttype      = "stackedbar";
        $normalize      = false;
        break;
      case "GRAFIEK_STAPEL_GENORMALISEERD" :
        $charttype      = "stackedbar_normalized";
        $normalize      = true;
        break;
      case "GRAFIEK_LIJN"  :
        $charttype      = "line";
        $normalize      = false;
        break;
      case "GRAFIEK_TAART" :
        $charttype      = "pie";
        $normalize      = false;

        //pChart bug in pie Charts voor 0 values
        foreach ($result as $key=>$row) {
          if ($row["Waarde"] == 0) {
            $result = array_remove($result, $key);
          }
        }
        break;
    }


    if ($grafieksoortid
    &&  $datasoortid
    &&  $groeperingsoortid1) {
      $chart = new SB_Chart();

      //dir en filename bepalen
      $today  = date("Ymd", time() );
      $time   = date("His", time() );

      //TODO: session variabelen vervangen door parameters
      $dirname        = dirname(__FILE__) . "/../userdata/" . $_SESSION["project"]["organisatiedirectory"] . "/charts/";
      $filename       = "chart_"  .$_SESSION["account"]["organisatienaam"] . "_" . $_SESSION["project"]["gebruikerscode"] . "_" . $today . "_" . $time . ".png";
      $filenamelegend = "legend_" .$_SESSION["account"]["organisatienaam"] . "_" . $_SESSION["project"]["gebruikerscode"] . "_" . $today . "_" . $time . ".png";

      //bestaat directory "usersettings/organisatienaam" al ?
      if (!file_exists($dirname)) {
        //nee, dus directory aanmaken
        mkpath($dirname);
      }

      $chart->ID                  = $containerid . "_chart";
      $chart->ChartType           = $charttype;
      $chart->AmountFieldname     = $amountfieldname;
      $chart->AmountTitle         = ""; //$datasoorttitle;
      $chart->AutoRotation        = true;
      $chart->DisplayLegend       = $displaylegend;
      $chart->DisplayValues       = $displayvalues;
      $chart->Filename            = $dirname . $filename;
      $chart->GroupingFieldname1  = isnotempty($groeperingsoortid1) ? $groupingfieldnames[0] : NULL;
      $chart->GroupingFieldname2  = isnotempty($groeperingsoortid2) ? $groupingfieldnames[1] : NULL;
      $chart->Height              = $height;
      $chart->LegendFilename      = ($legendimage) ? $dirname . $filenamelegend : "";
      $chart->LegendImage         = $legendimage;
      $chart->Normalize           = $normalize;
      $chart->NullValue           = "Geen";
      $chart->Result              = $result;
      $chart->Scaling             = true;
      $chart->Label               = $datasoorttitle . " " . (($groeperingsoorttitle1) ? "per " . strtolower($groeperingsoorttitle1) : "");
      $chart->Width               = $width;
      $chart->WriteValues         = ($displayvalues) ? PIE_VALUE_NATURAL : false;

      $chart->createImageFile();

      //result
      if (file_exists($dirname . $filename)) {
        $serverfilename       = $dirname . $filename;
        $clientfilename       = "/userdata/".$_SESSION['current_organisatiedirectory']."/charts/" . $filename;
        $clientfilenamelegend = "/userdata/".$_SESSION['current_organisatiedirectory']."/charts/" . $filenamelegend;

        $resultset = array(
          "success"       => "true",
          "message"       => "chart created successfully",
          "filename"      => $clientfilename, 
          "filenamelegend"=> ($legendimage) ? $clientfilenamelegend : "", 
          "height"        => $chart->getHeight(),
          "width"         => $chart->getWidth()
        );

      } else {
        $resultset = array(
          "success"   => "false",
          "message"   => "chart not created",
          "filename"  => $clientfilename 
        );
      }
    }
  }

  //logging
  if ($logging) {
    $logtableid   = logtext_totable_insert($query->ConnectionObject->DB, NULL, NULL, "logs", "PHP", "CHART", "CHART", $tablename, $primaryfieldname, $primaryfieldvalue, !$resultset["success"], 0, $resultset["message"] . "\nFile: " . $resultset["filename"] , 0);
  }

  //json result
  echo json_encode($resultset);
?>