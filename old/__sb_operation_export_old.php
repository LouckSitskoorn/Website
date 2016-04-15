<?
  //session_start();

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

  //OPERATIONS: EXPORT

  //SETTINGS
  set_time_limit(1000);

  //INCLUDES
  include_once __DIR__ . "/classes/sb/__sb_excel.php";

  //CLASSES
  class Gridcolumn {
    public $Header;
    public $Serverpath;
    public $Fieldname;
    public $DataType;
    public $Width;
    public $Renderer;
    public $Hidden;
  }

  //initialize variables
  $filename         = "";
  $resultset = array(
    "success"    => "false",
    "message"    => "unknown error"
  );

  //template inlezen
  //if ($resultarray
  //&&  !empty($resultarray))  {fbb($templatefilename);
    //excel aanmaken
    $excel = new SB_Excel();

    if (isnotempty($templatefilename)) {
      $excel->Template->ID                = "ExcelTemplate";
      $excel->Template->Session           = $_SESSION;
      $excel->Template->Request           = $_REQUEST;
      $excel->Template->Params            = $params;
      $excel->Template->PrimaryFieldName  = $primaryfieldname;
      $excel->Template->PrimaryFieldValue = $primaryfieldvalue;
      //$excel->Template->IDPrefix  = $moduleprefix . "_";
      //$excel->Template->ContainerID       = $senderid;
      //$excel->Template->ContainerJSID     = $senderjsid;
      //$excel->Template->ContainerClass    = $senderclass;
      $excel->Template->Filename          = $templatefilename;
      $excel->Template->RootPath          = dirname(__FILE__) . "/../";
      $excel->Template->CustomPath        = dirname(__FILE__) . "/../usersettings/" . stripouterslashes($organisatiepath);

      //template lezen en parsen
      $excel->Template->readTemplate();
      $excel->Template->init();

      $excelobjects = $excel->Template->getObjectsByType("SB_Excel");

      if (count($excelobjects) == 1) {
        foreach($excelobjects[0]->Objects as $column) {
          if ($column instanceof SB_ExcelColumn) {
  //          $excel->Columns[] = $column;
            $excelcolumn = new SB_ExcelColumn();

            $excelcolumn->Fieldname = $column->Fieldname;
            $excelcolumn->Fieldtype = $column->Fieldtype;
            $excelcolumn->Label     = $column->Label;
            $excelcolumn->Width     = $column->Width;

            $excel->Columns[] = $excelcolumn;
          }
        }
      }
    } else {
      //gridcolumns samenstellen uit $xmlcolumns
      $gridcolumns = array();
      $totalcolumnwidth = 0;
      foreach ($xmlcolumns->children() as $grid) {
        $nodename = $grid->getName();

        if (comparetext($nodename, 'grid')) {
          $count = 0;
          foreach ($grid->children() as $column) {
            $gridcolumn = new Gridcolumn();

            $gridcolumn->Serverpath = (string)$column['serverpath'];
            $gridcolumn->Fieldname  = (string)$column['fieldname'];
            $gridcolumn->Header     = (string)$column['header'];
            $gridcolumn->Width      = (integer)$column['width'];
            $gridcolumn->Renderer   = (string)$column['renderer'];
            $gridcolumn->DataType   = (string)$column['datatype'];
            $gridcolumn->Align      = (string)$column['align'];
            $gridcolumn->Hidden     = (string)$column['hidden'];

            if ($gridcolumn->Hidden===true
            || $gridcolumn->Hidden==="true"
            || $gridcolumn->DataType=='outline'
            || $gridcolumn->DataType=='image'
            || $gridcolumn->Width==0) {
              # overslaan
            } else {
              $gridcolumns[$gridcolumn->Fieldname] = $gridcolumn;
              $totalcolumnwidth += $gridcolumn->Width;

              $count++;
            }
          }
        }
      }

      //header
      foreach($gridcolumns as $gridcolumn) {
        if ($gridcolumn) {
          //header column
          $columnwidth = ($gridcolumn->Width / $totalcolumnwidth) * 10 . '%';// * $gridwidth;

          $excelcolumn = new SB_ExcelColumn();

          $excelcolumn->Fieldname = $gridcolumn->Fieldname;
          $excelcolumn->Fieldtype = $gridcolumn->Dataype;
          $excelcolumn->Label     = $gridcolumn->Header;
          $excelcolumn->Width     = $gridcolumn->Width/5;

          $excel->Columns[] = $excelcolumn;
        }
      }
    }

    //ouwe result manier
    //foreach($resultarray as $record) {
    //  $excel->Records[] = $record;
    //}

    //nieuwe fetch manier :
    $excel->QueryObject=  $query;

    //dir en filename bepalen
    $today  = date("Ymd", time() );
    $time   = date("His", time() );

    //TODO: session variabelen vervangen door parameters
    $dirname  = dirname(__FILE__) . "/../userdata/".$_SESSION['current_organisatiedirectory']."/exports/";
    $filename = str_ireplace(" ", "_", "export_".$_SESSION["account"]["organisatienaam"]."_".$_SESSION['current_gebruikerscode']."_".$today."_".$time.".xls");

    //bestaat directory "usersettings/organisatienaam" al ?
    if (!file_exists($dirname)) {
      //nee, dus directory aanmaken
      mkpath($dirname);
    }

    //schrijf excel weg naar dirname/filename
    $excel->createExcel($dirname . $filename);

    //toon excel
    if (isnotempty($filename)
    &&  file_exists($dirname . $filename)) {
      $serverfilename = $dirname . $filename;
      $clientfilename = "/userdata/".$_SESSION['current_organisatiedirectory']."/exports/" . $filename;

      //download file
      //download_file($serverfilename);
      //header('Location: ' . $clientfilename);

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

  //} else {
  //  $resultset = array(
  //    "success"    => "false",
  //    "message"    => "result empty"
  //  );
  //}

  //json result
  echo json_encode($resultset);
 ?>