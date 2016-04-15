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
  include_once __DIR__ . "/functions/_rtf_functions.php";

  //ini sets
  set_time_limit(60);
  ini_set("display_errors", $_SESSION["display_errors"]); 
  error_reporting($_SESSION['error_reporting']);

  //dir en filename bepalen
  $today  = date("Ymd", time() );
  $time   = date("His", time() );

  //TODO: session variabelen vervangen door parameters
  $extension = "doc";
  $dirname  = dirname(__FILE__) . "/../userdata/".$_SESSION['current_organisatiedirectory']."/reports/";
  $filename = $title."_".$today."_".$time.".".$extension;

  //bestaat directory "usersettings/organisatienaam" al ?
  if (!file_exists($dirname)) {
    //nee, dus directory aanmaken
    mkpath($dirname);
  }

//  $sourceTemplate = $template;//'c:/xampp/htdocs/ServiceBeheer/temp/gebruikerscode.rtf';
//  $outputDocument = $dirname . $filename;
  
  $search = array();
  $replace = array();

  if ($resultarray) {

    foreach($resultarray as $record) {
      //vervang all [%...] door velden uit de database
      foreach($record as $key=>$value) {
        $search[$key]  = trim($key);
        $replace[$key] = $value;
//        $value=str_ireplace("'", "&quot;",$value);
//        $recordxml = str_ireplace('[%'.trim($key).']', $value, $recordxml);
      }
    }
  }
  
  $templatefilename = dirname(__file__). "/../" . trimstringleft($template, "/");
  
  if (file_exists($templatefilename)) { 
  	$rtf = new RTF();
    $rtf->read($templatefilename);
    $rtf->replace($search, $replace);
    $rtf->save($dirname . $filename);
  
/*  
//  if (file_exists($dirname . $filename). '.docx') {
    $objDocument = new cTransformDoc();
//    $objDocument->setStrFile($dirname . $filename. '.docx');
    $objDocument->setStrFile('c:\test.docx');
    $objDocument->fGeneratePDF();
//  }  
*/  

  //toon document

//    if (file_exists($dirname . $filename)) {
    $clientfilename = "/userdata/".$_SESSION['current_organisatiedirectory']."/reports/" . $filename;
/*
    header('Content-Type: application/msword');
    header('Pragma: public');
    header('Cache-Control: must-revalidate; post-check=0; pre-check=0');
    header("Content-Disposition: inline; filename=$title.doc");

    echo $rtf->get();
*/    
//        header("Content-type: application/vnd.ms-word"); 
//        header("Content-Disposition: attachment;Filename=".$title.".".$extension); 
//        echo "<html>"; 
//        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=print-1252\">"; 
//        echo "<body>"; 
//        print $rtf->get(); //$this->Text; 
//        echo "</body>"; 
//        echo "</html>"; 

        
    
      
/*

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<title><? echo $title; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>

<script type="text/javascript" src="/framework/libraries/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">
  tinyMCE.init({
    mode : "textareas"
  });
</script>
</head>
<body>
  <textarea name="content" style='width: 100%; height: 100%;' rows="25">
<?
  echo $rtf->get();
?>
  </textarea>

</body>
</html>

*/
    
    header('Location: '.$clientfilename);
  } else {
    echo "PDF bestand niet gevonden.";
  }

  //TODO: resultset message/success
?>