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

  //logging               (boolean  - true als queries gelogd moeten worden)
  //logfilename           (string   - log filename)
  //logpath               (string   - log path)

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

  //OPERATIONS: DOCX

  //ini sets
  set_time_limit(60);
  ini_set("display_errors", $_SESSION["display_errors"]); 
  error_reporting($_SESSION['error_reporting']);

  //dir en filename bepalen
  $today  = date("Ymd", time() );
  $time   = date("His", time() );

  //TODO: session variabelen vervangen door parameters
  $extension = "rtf";
  $dirname  = dirname(__FILE__) . "/../userdata/".$_SESSION['current_organisatiedirectory']."/reports/";
  $filename = $_SESSION['current_gebruikerscode']."_".$today."_".$time.".".$extension;

  //bestaat directory "usersettings/organisatienaam" al ?
  if (!file_exists($dirname)) {
    //nee, dus directory aanmaken
    mkpath($dirname);
  }

  $sourceTemplate = 'c:/xampp/htdocs/ServiceBeheer/temp/gebruikerscode.docx';
  $outputDocument = $dirname . $filename;
  
  if (copy($sourceTemplate, $outputDocument)) {  
    $zipArchive = new ZipArchive();
    $zipArchive->open($outputDocument);

    $newContent = $zipArchive->getFromName("word/document.xml");

    //$newContent = str_ireplace("#", 'BlaBlaBla', $newContent);
    //$newContent = preg_replace("/\%(.*?)\%/", 'BlaBlaBla', $newContent);
    
    preg_match_all("/%(.*?)%/", $newContent, $matches, PREG_PATTERN_ORDER);
//  print_r($matches);

    $count = 0;
    foreach ($matches[0] as $val) {
      $replacestr = strip_tags($val);
      
      $newContent = str_ireplace( $val, $count, $newContent);

      $count++;
    }
    
    //Replace the content with the new content created above.
    //In the Open XML 2.0 format Wordprocessing content is 
    //  in a file named document.xml located in the word directory.
    $zipArchive->addFromString("word/document.xml", $newContent);
    $zipArchive->close();
  }


  //toon document
  if (file_exists($outputDocument)) {
    $clientfilename = "/userdata/" . $_SESSION['current_organisatiedirectory'] . "/reports/" . $filename;

    header('Location: '.$clientfilename);
  } else {
    echo "PDF bestand niet gevonden.";
  }

  //TODO: resultset message/success
?>