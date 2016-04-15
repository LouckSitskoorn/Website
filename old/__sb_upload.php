<?php
  /*
  * Created on 23 aug 2013
  * by Louck Sitskoorn
  */

  //INCLUDES  autoload
  require_once __DIR__ . "/__sb_autoload.php";

  //PARAMETERS
  $uploaddir              = (isset($_REQUEST["uploaddir"])) ? $_REQUEST["uploaddir"] : "";

  //SUBMIT FILES
  $xmloutput          = new SimpleXMLElement("<xml></xml>");

  if (!empty($_FILES)) {
    if (!is_dir($_SERVER["DOCUMENT_ROOT"] . $uploaddir)) {
      mkpath($_SERVER["DOCUMENT_ROOT"] . "/" . stripouterslashes($uploaddir));
    }

    foreach ($_FILES as $key=>$file) {
      //determine upload path and file
      $uploadpath     = $_SERVER["DOCUMENT_ROOT"] . "/" . stripouterslashes($uploaddir);
      $uploadfilename = preg_replace( '/^.+[\\\\\\/]/', '', $_FILES[$key]["name"]);
      $uploadtempname = $_FILES[$key]["tmp_name"];

      $uploadfilename = str_ireplace(" ", "_", $uploadfilename);
      $uploadfilename = str_ireplace("`", "_", $uploadfilename);
      $uploadfilename = str_ireplace("'", "_", $uploadfilename);
      $uploadfilename = str_ireplace('"', "_", $uploadfilename);
      $uploadfilename = str_ireplace("#", "_", $uploadfilename);

      //make sure uploaddirectory exists
      mkpath($uploadpath);

      //move uploaded file to uploaddirectory
      if (move_uploaded_file($uploadtempname, $uploadpath . "/" . $uploadfilename)) {
        chmod($uploadpath . "/" . $uploadfilename, 0777);

        $record = $xmloutput->addChild("file");
        $record->addAttribute("path", $uploadpath);
        $record->addAttribute("filename", $uploadfilename);
        $record->addAttribute("tempname", $uploadtempname);
        $record->addAttribute("type", $_FILES[$key]["type"]);
        $record->addAttribute("size", $_FILES[$key]["size"]);
        $record->addAttribute("displaysize", display_filesize($_FILES[$key]["size"]));

        echo '{"success"    : true
               , "filename"     : "' . $uploadfilename . '"
               , "path"         : "' . addslashes($uploadpath) . '"
               , "tempname"     : "' . addslashes($uploadtempname) . '"
               , "type"         : "' . $_FILES[$key]["type"] . '"
               , "size"         : "' . $_FILES[$key]["size"] . '"
               , "displaysize"  : "' . display_filesize($_FILES[$key]["size"]) . '"
              }';
      } else {
        echo '{"success":false}';
      }
    }
  } else {
    echo '{"success":false}';
  }

?>