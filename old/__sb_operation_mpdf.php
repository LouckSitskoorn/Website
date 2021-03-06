<?

  //DEZE FILE WORDT NIET RECHTSTREEKS GEBRUIKT MAAR GE-INCLUDE DOOR __SB_OPERATION

  //PARAMETERS:
  //operation             (string   - ADD/VIEW/EDIT/ etc.)
  //operationtitle        (string   - titel voor bepaalde operaties bijv. 'Verwijderen')
  //connection            (object   - SB_ConnectionParameters)
  //tablename             (string)
  //sql                   (string   - SQL statement)
  //sqlfilename           (string   - SQL filename)
  //primaryfieldname      (string)
  //primaryfieldvalue     (string)
  //customfieldnames      (string   - komma-separated)
  //distinctfieldnames    (string   - komma-separated)
  //parentfieldname       (string)
  //fields                (string   - komma-separated)
  //start                 (integer)
  //limit                 (integer)
  //sort                  (string   - sorteer veldnaam)
  //dir                   (string   - sorteer richting (ASC/DESC))
  //rowcount              (integer  - default -1)
  //template              (string   - path en filename van template (voor mail))
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
  //server                (string   - mail server)
  //username              (string   - mail username)
  //password              (string   - mail password)
  //xmltemplate           (string   - path en filename van xmltemplate (voor mail))
  //htmltemplate          (string   - path en filename van htmltemplate (voor mail))
  //subject               (string   - mail subject)
  //from                  (string   - mail from emailadressen)
  //to                    (string   - mail to emailadressen)
  //replyto               (string   - mail replyto emailadressen)
  //cc                    (string   - mail cc emailadressen
  //bcc                   (string   - mail bcc emailadressen

//includes
  //include_once __DIR__ . "/functions/_rtf_functions.php";
  //include_once __DIR__ . "/functions/_json_functions.php";
  //include_once __DIR__ . "/functions/_xml_classes.php";
//  include_once __DIR__ . "/classes/sb/__sb_docx.php";
  include_once __DIR__ . "/classes/mpdf/__mpdf.php";
  //include_once __DIR__ . "/classes/sb/__sb_xmltemplate_report.php";
//  include_once __DIR__ . "/libraries/mpdf/mpdf.php";

  //ini sets
  set_time_limit(60);
  ini_set("display_errors", $_SESSION["display_errors"]); 
  error_reporting($_SESSION['error_reporting']);

  //dir en filename bepalen
  $today  = date("Ymd", time() );
  $time   = date("His", time() );

  //TODO: session variabelen vervangen door parameters
  $extension = "pdf";
  $dirname  = dirname(__FILE__) . "/../userdata/".$_SESSION['current_organisatiedirectory']."/reports/";
  $filename = $title."_".$today."_".$time.".".$extension;

  //bestaat directory "usersettings/organisatienaam" al ?
  if (!file_exists($dirname)) {
    //nee, dus directory aanmaken
    mkpath($dirname);
  }

//  $templatefilenamefullpath = dirname(__file__). "/../" . trimstringleft($templatefilename, "/");

//  echo $templatefilename;
  
  $validpdf = false;
//  if (file_exists($templatefilenamefullpath)) { 
    $use_xmltemplatereader = true;

    if ($use_xmltemplatereader) {
      $mpdf = new SB_MPDF();

      $mpdf->ID                 = "Report";
      $mpdf->Session            = $_SESSION;
      $mpdf->Request            = $_REQUEST;
      $mpdf->Params             = "";
      $mpdf->Evaluate           = true;
      $mpdf->Filename           = $templatefilename;
      $mpdf->RootPath           = dirname(__FILE__) . "/../";
      $mpdf->CustomPath         = dirname(__FILE__) . "/../usersettings/" . stripouterslashes($organisatiepath);

      $mpdf->readTemplate();

      //TODO:stylesheets ?!?!
      $html = $mpdf->outputHTML();

    } else {
      $filehandle   = fopen($templatefilename, "r");
      $html = fread($filehandle, filesize($templatefilename));
      fclose($filehandle);
    }

    if ($resultarray) {
      foreach($resultarray as $record) {
        //TODO:pagina's aanmaken en combineren tot 1 PDF

        //vervang all [%...] door velden uit de database
        foreach($record as $key=>$value) {
          $value=str_ireplace("'", "&quot;",$value);
          $value=str_ireplace("\n", "&return;",$value);

          $html = str_ireplace('[%'.trim($key).']', $value, $html);
        }

        //vervang overige %
//        $html = str_ireplace('[%'.trim($key).']', $value, $html);
        $html = preg_replace("/\[\%[^\]]*?\]/", "", $html);
       
        //haal linebreaks weg zodat deze niet als spatie worden gezien
        $html =  str_replace("\r", '', str_replace("\n", '', $html));
/*        
        $search = array();
        $replace = array();

        //alle tussenliggende tags verwijderen om de texten goed op elkaar te laten aansluiten
        //Word maakt nog weleens verschillende tags aan middenin woorden namelijk
        preg_match_all("/\[[^\[\]]*?\]/i", $html, $matches, PREG_PATTERN_ORDER);
        foreach ($matches[0] as $val) {
          $strippedval = strip_tags(str_replace(array(" ", "\r", "\n", "\t"), '', $val));
          $html = str_ireplace( $val, $strippedval , $html);
        }

        //simpele matches
        foreach($record as $key=>$value) {
          $search[]  = "/\[".$key."\]/i";
          $replace[] = $value;
        }
        
        //complexe matches met tags tussen de haken en de replacetags
        foreach($record as $key=>$value) {
          $search[]  = "/\[[^\[\]]*?\>[^\]a-zA-Z]*?".$key."[^\[a-zA-Z]*?\<[^\[\]]*?\]/i";
          $replace[] = $value;
        }
        
        $html = preg_replace($search, $replace, $html);
*/
        if ($use_xmltemplatereader) {
          $mpdf->createPDF($html);
          $mpdf->outputPDF($dirname.$filename);
        } else {
          $mpdf = new mPDF('win-1252','A4','','',20,20,20,20,10,10);
          $mpdf->dpi = 96;
          $mpdf->img_dpi = 96;
          //TODO:stylesheets ?!?!
          $mpdf->WriteHTML($html);
          $mpdf->Output($dirname.$filename , "F");
        }
      }
      
      //toon document
      if (file_exists($dirname . $filename)) {
        $clientfilename = "/userdata/".$_SESSION['current_organisatiedirectory']."/reports/" . $filename;
    
        $validpdf = true;
        header('Location: '.$clientfilename. "#toolbar=1&navpanes=0");
      }
    }
//  }

  if ($validpdf==false) {   
    echo "PDF bestand niet gevonden.";
  }

  //TODO: resultset message/success
?>