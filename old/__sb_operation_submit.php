<?php
  /*
  * Created on 17 mrt 2008
  * by Louck
  */

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

  //OPERATIONS: SUBMIT

  //objecten
  $xmloutput = new SimpleXMLElement("<xml></xml>");

  //SUBMIT
  if ($xmlsubmit) {
    //submit xml-based data
    //$db = new SB_Connection($connectionparameters);

    //$db->Logging          = $logging;
    //$db->LoggingPath      = $logpath;
    //$db->LoggingFilename  = $logfilename;
    //$db->LoggingTablename = $logtablename;

    //if ($db) {
    //  $db->connect();

    //  if ($db->Connected) {
        //$db->DB->StartTrans();

        try {
          //xml nodes aflopen
          foreach ($xmlsubmit->children() as $xmlrecord) {
            //tagname bepalen
            $tagname = $xmlrecord->getName();
            if (comparetext($tagname, "record")) {
              $recordoperation        = (string)$xmlrecord["operation"];
              $recordtablename        = (string)$xmlrecord["tablename"];
              $recordprimaryfieldname = (string)$xmlrecord["primaryfieldname"];
              $recordmasterfieldname  = (string)$xmlrecord["masterfieldname"];
              $recordcustomfieldname  = (string)$xmlrecord["customfieldname"];

              //table submitten?
              if (!comparetext($tablename, "unknown")
              && !isempty($tablename)) {
                //master of detail submitten?
                if (comparetext($tablename, $recordtablename)
                &&  isempty($recordmasterfieldname)) {
                  submit_xml_master($query, $xmlrecord, $operation, $tablename, $primaryfieldname, $primaryfieldvalue, $xmlsubmit, $xmloutput);
                } else {
                  submit_xml_details($query, $xmlrecord, $operation, $tablename, $primaryfieldname, $primaryfieldvalue, $xmlsubmit, $xmloutput);
                }
              } else if (!isempty($sqlfilename)) {
                submit_xml_query($query, $xmlrecord, $operation, $tablename, $primaryfieldname, $primaryfieldvalue, $xmlsubmit, $xmloutput);
              }
            }
          }

          //$db->DB->CompleteTrans();
        } catch (Exception $e) {
          //$db->DB->RollbackTrans();
        }
      }
    //}
  //}

  //SUBMIT FILES
  if (!empty($_FILES)) {
    if (!dir_exists($_SERVER["DOCUMENT_ROOT"] . $uploaddir)) {
      mkpath($_SERVER["DOCUMENT_ROOT"] . "/" . stripouterslashes($uploaddir));
    }

    foreach ($_FILES as $key=>$file) {
      $uploadpath     = $_SERVER["DOCUMENT_ROOT"] . "/" . stripouterslashes($uploaddir);
      $uploadfilename = preg_replace( '/^.+[\\\\\\/]/', '', $_FILES[$key]["name"]);
      $uploadtempname = $_FILES[$key]["tmp_name"];

      $uploadfilename = str_ireplace(" ", "_", $uploadfilename);

      if (move_uploaded_file($uploadtempname, $uploadpath . "/" . $uploadfilename)) {
/*
          $ftpserver="postnl.acceptatie.servicebeheer.nl";
          $ftpuser="postnla";
          $ftppassword="dylan74";
          $ftppath="/";
*/

/*
          //copy file to ftp server
          $conn_id = ftp_connect($ftpserver);
          $login_result = ftp_login($conn_id, $ftpuser, $ftppassword);
          if (($conn_id) && ($login_result)) {
            $upload = ftp_put($conn_id, "{$folder}/{$handle}/{$newfilename}", "{$handle}/{$newfilename}", FTP_BINARY);
            ftp_close($conn_id);
          }
*/

        $record = $xmloutput->addChild("file");
        $record->addAttribute("path", $uploadpath);
        $record->addAttribute("filename", $uploadfilename);
        $record->addAttribute("tempname", $uploadtempname);
        $record->addAttribute("type", $_FILES[$key]["type"]);
        $record->addAttribute("size", $_FILES[$key]["size"]);
        $record->addAttribute("displaysize", display_filesize($_FILES[$key]["size"]));

        //echo '{success:true, file:'.json_encode($_FILES['Filename']['name']).'}';
      } else {
        //echo '{"success":false}';
      }
    }
  } else {
    //echo '{"success":false}';
  }

  //json xmloutput samenstellen
  $jsonrows = json_rows_xml($xmloutput);
  $json     = json_encode($jsonrows);

  echo $json;





  //FUNCTION SUBMIT MASTER XML
  function submit_xml_master($query, $xmlrecord, $operation, $tablename, $primaryfieldname, &$primaryfieldvalue, $xmlsubmit, &$xmloutput) {
    global $_SESSION;
    global $logging, $logfilename, $logpath, $logtablename;
    global $primaryfieldvalues;

    $encryptedfieldnames = (isset($_SESSION["encryptedfieldnames"])) ? unserialize($_SESSION["encryptedfieldnames"]) : false;

    //xml nodes aflopen
    //foreach ($xmlsubmit->children() as $record) {
      //tagname bepalen
      $tagname = $xmlrecord->getName();

      //node is record?
      if (comparetext($tagname, "record")) {
        //record attributes bepalen
        $recordoperation        = (string)$xmlrecord["operation"];
        $recordtablename        = (string)$xmlrecord["tablename"];
        $recordprimaryfieldname = (string)$xmlrecord["primaryfieldname"];
        $recordcustomfieldname  = (string)$xmlrecord["customfieldname"];
        $recordformid           = (string)$xmlrecord["formid"];
        $recordsubmit           = strtobool((string)$xmlrecord["submit"]);

        //sql statements clearen
        $sqlupdate    = "";
        $sqlinsert    = "";
        $sqldelete    = "";

        //tablename controleren
        if (comparetext($tablename, $recordtablename)
        &&  $recordsubmit) {
          //field array samenstellen uit xmlsubmit
          $fieldarray = array();
          $customarray= array();

          foreach ($xmlrecord->children() as $field) {
            $fieldname        = (string)$field["fieldname"];
            $fieldtype        = (string)$field["datatype"];
            $fieldsubmit      = strtobool2((string)$field["submit"]);
            $fieldcustom      = strtobool2((string)$field["custom"]);
            $fieldlabel       = (string)$field["label"];
            $fielddescription = (string)$field["description"];
            $fieldvalue       = (string)$field;

            //check fieldtype
            if ($fieldtype=="number") {
              $fieldvalue = floatsql($fieldvalue);

            } elseif ($fieldtype == "date" || stripos($fieldname, "datum") !== false) {
              if ($fieldcustom) {
                $fieldvalue = mysql_date2($fieldvalue);
              } else {
                $fieldvalue = mysql_date($fieldvalue);
              }

            } elseif ($fieldtype == "string") {
                if ($fieldvalue == "[submit:primaryfieldname]")     {$fieldvalue = $primaryfieldname;}
                if ($fieldvalue == "[submit:primaryfieldvalue]")    {$fieldvalue = $primaryfieldvalue;}
            }

            //TODO: betere oplossing voor enkele quotes
            //Values worden geconvert naar ISO ??? Raar maar werkt
            if (($fieldtype == "string" || $fieldtype == "")) {
              if (!$fieldcustom) {
                $fieldvalue = str_ireplace("'", "`", $fieldvalue);
                $fieldvalue = str_ireplace("&nbsp;", "", $fieldvalue);
                //$fieldvalue = convert_to_iso($fieldvalue);
              } else {
                $fieldvalue = str_ireplace("'", "`", $fieldvalue);
                $fieldvalue = str_ireplace("&nbsp;", "", $fieldvalue);
                $fieldvalue = preg_replace('/&(?![a-z#]+;)/i','&amp;',$fieldvalue);
                //$fieldvalue = str_ireplace("&", "&amp;", $fieldvalue);
                //$fieldvalue = str_ireplace("<", "&lt;", $fieldvalue);
                //$fieldvalue = convert_to_iso($fieldvalue);
              }
            } elseif ($fieldtype == "html") {
              $fieldvalue = mb_convert_encoding($fieldvalue, "HTML-ENTITIES", "auto");
            }

            //decrypt value?
            if (is_array($encryptedfieldnames)
            &&  in_array($fieldname, $encryptedfieldnames)) {
              $fieldvalue  =  decryptConvert($fieldvalue);
            }

            //utf8 value?
            if (isnotempty($fieldvalue)) {
              $fieldvalue = forceUTF8($fieldvalue);
            }


            //veld toevoegen aan juiste array (gewoon of custom)
            if ($fieldname && $fieldsubmit && !$fieldcustom) {
              $fieldarray[$fieldname] = new Datafield($fieldname, $fieldtype, $fieldvalue, $fieldlabel, $fielddescription);
            }
            if ($fieldname && $fieldsubmit && $fieldcustom) {
              $customarray[$fieldname]= new Datafield($fieldname, $fieldtype, $fieldvalue, $fieldlabel, $fielddescription);
            }
          }


          //ADD/COPY/STACK
          if (  (comparetext($recordoperation, "ADD")
              || comparetext($recordoperation, "COPY")
              || comparetext($recordoperation, "STACK") )
          &&     (count($fieldarray) > 0
              ||  count($customarray) > 0) ) {
            $sqlinsert .= "INSERT INTO $recordtablename (";
            $komma      = "";

            //veldnamen bepalen
            foreach ($fieldarray as $field) {
              //calculated fields en primaryfieldname niet opnemen in insert
              if (!comparetext($field->Fieldtype, 'calculated')
              &&  !comparetext($field->Fieldname, $recordprimaryfieldname)) {
                $sqlinsert .= $komma . "$field->Fieldname";
                $komma      = ", ";
              }
            }

            //veldnamen bepalen - customfieldname
            if ($recordcustomfieldname
            &&  count($customarray) > 0) {
              $sqlinsert .= $komma . "$recordcustomfieldname";
            }

            $sqlinsert .= ")";

            //values aanmaken
            $sqlinsert .= " VALUES (";
            $komma      = "";
            foreach ($fieldarray as $field) {
              //caluclated fields en primaryfieldname niet opnemen in insert
              if (!comparetext($field->Fieldtype, "calculated")
              &&  !comparetext($field->Fieldname, $recordprimaryfieldname)) {
                //controleren op {null} value
                if (is_empty($field->Value)) {
                  $sqlinsert .= $komma . "NULL";
                  $komma      = ", ";
                } else {
                  $sqlinsert .= $komma . "\"" . addslashes($field->Value) . "\"";
                  $komma      = ", ";
                }
              }
            }

            //value customfield aanmaken
            if ($recordcustomfieldname
            &&  count($customarray) > 0) {
              $xmlcustom = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><xml></xml>");

              $customfields = $xmlcustom->addChild("fields");
              foreach ($customarray as $custom){
                //controleren op {null} value
                if (comparetext($custom->Value, "{null}")) {
                    $customfield = $customfields->addChild("field");
                } else {
                    $customfield = $customfields->addChild("field", $custom->Value);
                }

                $customfield->addAttribute("fieldname", $custom->Fieldname);
                if(isnotempty($custom->FieldType))    {$customfield->addAttribute("datatype", $custom->Fieldtype);}
                if(isnotempty($custom->Label))        {$customfield->addAttribute("label", $custom->Label);}
                if(isnotempty($custom->Description))  {$customfield->addAttribute("description", $custom->Description);}
              }

              $customvalue  = $xmlcustom->asXML();
              $customvalue  = str_ireplace('"', "'", $customvalue);
              $customvalue  = str_ireplace(chr(13), " ", $customvalue);

              $sqlinsert .= $komma . "\"" . $customvalue . "\"";
            }

            $sqlinsert .= ")";

            //EXECUTE insert query
            if ($query) {
              $result = $query->insert($sqlinsert);

              //if ($this->Developer) {
                fb_sql($sqlinsert, "{$operation} {$tablename}");
              //}

              //primaryfieldvalue bewaren
              if ($result) {
                $primaryfieldvalue                  = $query->InsertID;
                $primaryfieldvalues[$recordformid]  = $query->InsertID;
              }

              //output xml aanpassen
              $record = $xmloutput->addChild("record");
              $record->addAttribute("tablename", $recordtablename);
              $record->addAttribute("primaryfieldname", $recordprimaryfieldname);

              $field = $record->addChild("field", $primaryfieldvalue);
              $field->addAttribute("fieldname", $recordprimaryfieldname);
            }

          //EDIT
          } elseif ((comparetext($recordoperation, "EDIT"))
                &&  (count($fieldarray) > 0) || (count($customarray) > 0)) {

            //update
            $sqlupdate .= "UPDATE $recordtablename ";
            $sqlupdate .= "SET ";

            //values aanmaken
            $komma      = "";
            foreach ($fieldarray as $field) {
              if (!comparetext($field->Fieldtype, "calculated")
              &&  !comparetext($field->Fieldname, $primaryfieldname)) {
                if (comparetext($field->Value, "{null}")) {
                  $sqlupdate .= $komma . "$field->Fieldname = NULL ";
                  $komma      = ", ";
                } else {
                  $sqlupdate .= $komma . "$field->Fieldname = '" . addslashes($field->Value) . "' ";
                  $komma      = ", ";
                }
              }
            }

            //value customfield aanmaken
            if ($recordcustomfieldname
            &&  count($customarray) > 0) {
              //customfield ophalen
              $sqlcustom     = "
                SELECT {$recordcustomfieldname}
                FROM {$recordtablename}
                WHERE {$primaryfieldname} = '{$primaryfieldvalue}'
              ";
              $resultcustom  = $query->execute($sqlcustom);

              if (count($resultcustom) > 0) {
                $query->Executed  =  false;

                //customfield bestaat al ?
                if ($resultcustom[0][$recordcustomfieldname]) {
                  //bestaand customfield aanpassen
                  $xmlcustom  =  simplexml_load_string($resultcustom[0][$recordcustomfieldname]);

                  foreach ($customarray as $custom) {
                    $xmlcustomfields = $xmlcustom->xpath('/xml/fields');
                    $xmlcustomfield  = $xmlcustom->xpath('/xml/fields/field[@fieldname="' . $custom->Fieldname . '"]');

                    if (count($xmlcustomfield) > 0) {
                      while (list( , $node) = each($xmlcustomfield)) {
                        //bestaande node aanpassen
                        $node[0][0]  =  $custom->Value;
                        }
                    } else {
                      //nieuwe node toevoegen
                      if (comparetext($custom->Value, "{null}")) {
                        $customfield = $xmlcustom->children()->addChild("field");
                      } else {
                        $customfield = $xmlcustom->children()->addChild("field", $custom->Value);
                      }

                      $customfield->addAttribute("fieldname", $custom->Fieldname);
                      if(isnotempty($custom->FieldType))    {$customfield->addAttribute("datatype", $custom->Fieldtype);}
                      if(isnotempty($custom->Label))        {$customfield->addAttribute("label", $custom->Label);}
                      if(isnotempty($custom->Description))  {$customfield->addAttribute("description", $custom->Description);}
                    }
                  }

                  $sqlupdate .= $komma . "$recordcustomfieldname = '{$xmlcustom->asXML()}'";
                } else {
                  //nieuw customfield aanmaken
                  $xmlcustom =  simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><xml><fields></fields></xml>");

                  foreach ($customarray as $custom) {
                    //nieuwe node toevoegen
                    if (comparetext($custom->Value, "{null}")) {
                      $customfield = $xmlcustom->children()->addChild("field");
                    } else {
                      $customfield = $xmlcustom->children()->addChild("field", $custom->Value);
                    }

                    $customfield->addAttribute("fieldname", $custom->Fieldname);
                    if(isnotempty($custom->FieldType))    {$customfield->addAttribute("datatype", $custom->Fieldtype);}
                    if(isnotempty($custom->Label))        {$customfield->addAttribute("label", $custom->Label);}
                    if(isnotempty($custom->Description))  {$customfield->addAttribute("description", $custom->Description);}
                  }

                  $sqlupdate .= $komma . "$recordcustomfieldname = '{$xmlcustom->asXML()}'";
                }
              }
            }

            //where
            $sqlupdate .= " WHERE $primaryfieldname = '$primaryfieldvalue'\n";

            //EXECUTE update query
            if ($query) {
              //execute query
              $result = $query->update($sqlupdate);

              //primaryfieldvalue bewaren
              if ($result) {
                $primaryfieldvalue                  = $primaryfieldvalue;
                $primaryfieldvalues[$recordformid]  = $primaryfieldvalue;
              }

              //output xml aanpassen
              $record = $xmloutput->addChild("record");
              $record->addAttribute("tablename", $recordtablename);
              $record->addAttribute("primaryfieldname", $recordprimaryfieldname);

              $field = $record->addChild("field", $primaryfieldvalue);
              $field->addAttribute("fieldname", $recordprimaryfieldname);
            }

          //DELETE
          } elseif (comparetext($recordoperation, "DELETE")) {
            $sqldelete .= "DELETE FROM $tablename ";
            $sqldelete .= "WHERE $primaryfieldname = '$primaryfieldvalue'";

            //EXECUTE delete query
            if ($query) {
              $query->delete($sqldelete);
            }

          //DISABLE
          } elseif (comparetext($recordoperation, "DISABLE")) {
            $sqlupdate .= "UPDATE $recordtablename ";
            $sqlupdate .= "SET Enabled = 'FALSE' ";
            $sqlupdate .= "WHERE $primaryfieldname='$primaryfieldvalue'\n";

            //EXECUTE disable query
            if ($query) {
              $query->update($sqlupdate);
            }
          }
        }
      }
  }






  //FUNCTION SUBMIT DETAIL XML
  function submit_xml_details($query, $xmlrecord, $operation, $tablename, $primaryfieldname, &$primaryfieldvalue, $xmlsubmit, &$xmloutput) {
    global $_SESSION;
    global $logging, $logfilename, $logpath, $logtablename;
    global $sql, $sqlfilename;
    global $primaryfieldvalues;

    $encryptedfieldnames = (isset($_SESSION["encryptedfieldnames"])) ? unserialize($_SESSION["encryptedfieldnames"]) : false;

    //objecten aanmaken
    $xmlcustom = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><xml></xml>");

    //xml nodes aflopen
    //foreach ($xmlsubmit->children() as $record) {
      //tagname bepalen
      $tagname = $xmlrecord->getName();

      //node is record?
      if (comparetext($tagname, "record")) {
        //record attributes bepalen
        $recordoperation        = (string)$xmlrecord["operation"];
        $recordtablename        = (string)$xmlrecord["tablename"];
        $recordprimaryfieldname = (string)$xmlrecord["primaryfieldname"];
        $recordprimaryfieldvalue= (string)$xmlrecord["primaryfieldvalue"];
        $recordcustomfieldname  = (string)$xmlrecord["customfieldname"];
        $recordformid           = (string)$xmlrecord["formid"];
        $recordmasterformid     = (string)$xmlrecord["masterformid"];
        $recordsubmit           = strtobool((string)$xmlrecord["submit"]);

        //sql statements clearen
        $sqlupdate    = "";
        $sqlinsert    = "";
        $sqldelete    = "";

        //table controleren
        if (!comparetext($recordtablename, "unknown")
        &&  !isempty($recordtablename)
        &&  $recordsubmit) {
          //array van fields samenstellen
          $fieldarray = array();
          $customarray= array();
          foreach ($xmlrecord->children() as $field) {
            $fieldname        = (string)$field["fieldname"];
            $fieldtype        = (string)$field["datatype"];
            $fieldsubmit      = strtobool2((string)$field["submit"]);
            $fieldcustom      = strtobool2((string)$field["custom"]);
            $fieldlabel       = (string)$field["label"];
            $fielddescription = (string)$field["description"];
            $fieldvalue       = (string)$field;

            //check fieldtype
            if ($fieldtype=="number") {
              $fieldvalue = floatsql($fieldvalue);
            } elseif ($fieldtype == "date"
                  ||  stripos($fieldname, "datum") !== false) {
              if ($fieldcustom) {
                $fieldvalue = mysql_date2($fieldvalue);
              } else {
                $fieldvalue = mysql_date($fieldvalue);
              }
            } elseif ($fieldtype == "string") {
                if ($fieldvalue == "[submit:primaryfieldname]")     {$fieldvalue = $primaryfieldname;}
                if ($fieldvalue == "[submit:primaryfieldvalue]")    {$fieldvalue = $primaryfieldvalue;}
                if ($fieldvalue == "[submit:gebruikerid]")          {$fieldvalue = $_SESSION["project"]["gebruikerid"];}
                if ($fieldvalue == "[submit:gebruikernaam]")        {$fieldvalue = $_SESSION["project"]["gebruikernaam"];}
                if ($fieldvalue == "[submit:organisatieid]")        {$fieldvalue = $_SESSION["project"]["organisatieid"];}
                if ($fieldvalue == "[submit:organisatienaam]")      {$fieldvalue = $_SESSION["project"]["organisatienaam"];}
                if ($fieldvalue == "[submit:gebruikersprofielid]")  {$fieldvalue = $_SESSION["project"]["gebruikersprofielid"];}
                if ($fieldvalue == "[submit:optieprofielid]")       {$fieldvalue = $_SESSION["project"]["optieprofielid"];}
                if ($fieldvalue == "[submit:taalid]")               {$fieldvalue = $_SESSION["project"]["taalid"];}
                if ($fieldvalue == "[submit:klantid]")              {$fieldvalue = $_SESSION["project"]["klantid"];}
            }

            //decrypt value?
            if (is_array($encryptedfieldnames)
            &&  in_array($fieldname, $encryptedfieldnames)) {
              $fieldvalue  =  decryptConvert($fieldvalue);
            }

            //TODO: verbeteren voor enkele quotes (escapen?) in cleansqlstring ?
            if ($fieldname && ($fieldsubmit || $fieldtype == "calculated") && !$fieldcustom) {
              //$fieldarray[$fieldname] = new Datafield($fieldname, $fieldtype, utf8_decode(cleansqlstring($fieldvalue)));
              $fieldarray[$fieldname] = new Datafield($fieldname, $fieldtype, $fieldvalue, $fieldlabel, $fielddescription);
            }
            if ($fieldname && $fieldsubmit && $fieldcustom) {
              $customarray[$fieldname]= new Datafield($fieldname, $fieldtype, $fieldvalue, $fieldlabel, $fielddescription);
            }
          }

          //customfields aanmaken
          if ($recordcustomfieldname) {
            $customfields = $xmlcustom->addChild("fields");
            foreach ($customarray as $custom){
              //controleren op {null} value
              if (comparetext($custom->Value, "{null}")) {
                $customfield = $customfields->addChild("field");
              } else {
                $customfield = $customfields->addChild("field", $custom->Value);
              }

              $customfield->addAttribute("fieldname", $custom->Fieldname);
              if(isnotempty($custom->FieldType))    {$customfield->addAttribute("datatype", $custom->Fieldtype);}
              if(isnotempty($custom->Label))        {$customfield->addAttribute("label", $custom->Label);}
              if(isnotempty($custom->Description))  {$customfield->addAttribute("description", $custom->Description);}
            }
          }

          //CHECKED/UNCHECKED (griddetail) ?
          if (array_key_exists_case("checked", $fieldarray)) {
            //record controleren
            if (comparetext($fieldarray["checked"]->Value, "TRUE")) {
              //CHECKED items
              //if ((array_key_exists_case($recordprimaryfieldname, $fieldarray) && (comparetext($fieldarray[$recordprimaryfieldname]->Value, '') || comparetext($fieldarray[$recordprimaryfieldname]->Value, '{null}')))
              //||  ($primaryfieldname == $recordprimaryfieldname   && $fieldarray[$primaryfieldname]->Value != $primaryfieldvalue)) {

              if ((   comparetext($fieldarray[$recordprimaryfieldname]->Value, '')
                   || comparetext($fieldarray[$recordprimaryfieldname]->Value, '{null}')
                  )
              ||  (   $primaryfieldname == $recordprimaryfieldname
                   && $fieldarray[$primaryfieldname]->Value != $primaryfieldvalue)) {
                //CHECKED items die nog niet bestonden
                $sqlinsert .= "INSERT INTO $recordtablename (";
                $komma      = "";
                foreach ($fieldarray as $field) {
                  if (!comparetext($field->Fieldtype, "calculated")
                  &&  !comparetext($field->Fieldname, $recordprimaryfieldname)) {
                    $sqlinsert .= $komma . "$field->Fieldname";
                    $komma      = ", ";
                  }
                }

		            //veldnamen bepalen - customfieldname
		            if ($recordcustomfieldname
		            &&  count($customarray) > 0) {
		              $sqlinsert .= $komma . "$recordcustomfieldname";
		            }

                $sqlinsert .= ")";

                $sqlinsert .= " VALUES (";
                $komma      = "";
                foreach ($fieldarray as $field) {
                  if (!comparetext($field->Fieldtype, "calculated")
                  &&  !comparetext($field->Fieldname, $recordprimaryfieldname)) {
                    if ((comparetext($field->Fieldname, $primaryfieldname) || comparetext($field->Fieldname, "Parent" . $primaryfieldname))
                    && (comparetext($field->Value, "") ||  $fieldarray[$primaryfieldname]->Value != $primaryfieldvalue)) {
                      $sqlinsert .= $komma . "'$primaryfieldvalue'";
                      $komma      = ", ";
                    } else {
                      //controleren op {null} value
                      if (comparetext($field->Value, "{null}")) {
                        $sqlinsert .= $komma . "NULL";
                        $komma      = ", ";
                      } else {
                        $sqlinsert .= $komma . "\"" . addslashes($field->Value) . "\"";
                        $komma      = ", ";
                      }
                    }
                  }
                }
                $sqlinsert   .= ")";

                //EXECUTE insert query
                if ($query) {
                  $query->insert($sqlinsert);

		              //primaryfieldvalue bewaren
		              if ($result) {
		                $primaryfieldvalue                  = $query->InsertID;
		                $primaryfieldvalues[$recordformid]  = $query->InsertID;
		              }
                }

              } else {
                //CHECKED items die al bestonden
                $sqlupdate .= "UPDATE $recordtablename ";
                $sqlupdate .= "SET ";
                $komma      = "";
                foreach ($fieldarray as $field) {
                  if (!comparetext($field->Fieldtype, "calculated")
                  &&  !comparetext($field->Fieldname, $recordprimaryfieldname)) {
                    if (comparetext($field->Value, "{null}")) {
                      $sqlupdate .= $komma . "$field->Fieldname = NULL ";
                      $komma      = ", ";
                    } else {
                      $sqlupdate .= $komma . "$field->Fieldname = '" . addslashes($field->Value) . "' ";
                      $komma      = ", ";
                    }
                  }
                }

                $sqlupdate .= "WHERE $recordprimaryfieldname = '{$fieldarray[$recordprimaryfieldname]->Value}' ";

                //EXECUTE update query
                if ($query) {
                  $query->update($sqlupdate);

		              //primaryfieldvalue bewaren
		              if ($result) {
		                $primaryfieldvalue                  = $fieldarray[$recordprimaryfieldname]->Value;
		                $primaryfieldvalues[$recordformid]  = $fieldarray[$recordprimaryfieldname]->Value;
		              }
                }
              }

            } else {
              //UNCHECKED items
              if (array_key_exists_case($recordprimaryfieldname, $fieldarray)
              && !(comparetext($fieldarray[$recordprimaryfieldname]->Value, '') || comparetext($fieldarray[$recordprimaryfieldname]->Value, '{null}')) ) {
                //uchecked items die nog niet bestonden
                $sqldelete .= "DELETE FROM $recordtablename ";
                $sqldelete .= " WHERE $recordprimaryfieldname = '{$fieldarray[$recordprimaryfieldname]->Value}' \n";

                //EXECUTE delete query
                if ($query) {
                  $query->delete($sqldelete);
                }
              }
            }

          //----------------------------------------------------------------------------------------------
          } else {
            //ADD
            if (comparetext($recordoperation, "ADD")) {
              $sqlinsert .= "INSERT INTO $recordtablename (";
              $komma      = "";

              //veldnamen bepalen
              foreach ($fieldarray as $field) {
                //geen calculated fields en geen primaryfieldname opnemen in een insert
                if (!comparetext($field->Fieldtype, "calculated")
                &&  !comparetext($field->Fieldname, $recordprimaryfieldname)) {
                  $sqlinsert .= $komma . "$field->Fieldname";
                  $komma      = ", ";
                }
              }

              //veldnamen bepalen - customfieldname
	            if ($recordcustomfieldname
	            &&  count($customarray) > 0) {
	              $sqlinsert .= $komma . "$recordcustomfieldname";
	            }

              $sqlinsert .= ")";

              //values bepalen
              $sqlinsert .= " VALUES (";
              $komma      = "";
              foreach ($fieldarray as $field) {
                //geen calculated fields en geen primaryfieldname opnemen in een insert
                if (!comparetext($field->Fieldtype, "calculated")
                &&  !comparetext($field->Fieldname, $recordprimaryfieldname)) {
                  //masterfieldname ?
                  //TODO: Dit moet mooier !!!!!!!!!!!!!
                  if ((comparetext($field->Fieldname, $primaryfieldname) || comparetext($field->Value, "[master:primaryfieldvalue]")  || comparetext($field->Fieldname, "Parent" . $primaryfieldname))
                  && (comparetext($field->Value, "") || comparetext($field->Value, "{null}") || comparetext($field->Value, "[master:primaryfieldvalue]"))) {
                    if ($recordmasterformid) {
                      if (array_key_exists_case($recordmasterformid, $primaryfieldvalues)) {
                        $sqlinsert .= $komma . "'$primaryfieldvalues[$recordmasterformid]'";
                      } else {
                        $sqlinsert .= $komma . "'$primaryfieldvalue'";
                      }
                    } else {
                        $sqlinsert .= $komma . "'$primaryfieldvalue'";
                    }
                    $komma      = ", ";
                  } else {
                    //controleren op {null} value
                    if (comparetext($field->Value, "{null}")) {
                      $sqlinsert .= $komma . "NULL";
                      $komma      = ", ";
                    } else {
                      $sqlinsert .= $komma . "\"" . addslashes($field->Value) . "\"";
                      $komma      = ", ";
                    }
                  }
                }
              }

	            if ($recordcustomfieldname
	            &&  count($customarray) > 0) {
	              $customvalue  = $xmlcustom->asXML();
	              $customvalue  = str_ireplace('"', "'", $customvalue);
	              $customvalue  = str_ireplace(chr(13), " ", $customvalue);

	              $sqlinsert .= $komma . "\"" . $customvalue . "\"";
	            }

              $sqlinsert   .= ")";

              //EXECUTE insert query
              if ($query) {
                $result = $query->insert($sqlinsert);

                //primaryfieldvalue bewaren
                if ($result) {
                  $recordprimaryfieldvalue            = $query->InsertID;
                  $primaryfieldvalues[$recordformid]  = $query->InsertID;
                }

                //output xml aanpassen
                $record = $xmloutput->addChild("record");
                $record->addAttribute("tablename", $recordtablename);
                $record->addAttribute("primaryfieldname", $recordprimaryfieldname);

                $field = $record->addChild("field", $recordprimaryfieldvalue);
                $field->addAttribute("fieldname", $recordprimaryfieldname);
              }

            //EDIT
            } elseif (comparetext($recordoperation, "EDIT")) {
              $sqlupdate .= "UPDATE $recordtablename ";
              $sqlupdate .= "SET ";
              $komma      = "";
              foreach ($fieldarray as $field) {
                if (!comparetext($field->Fieldtype, "calculated")
                &&  !comparetext($field->Fieldname, $recordprimaryfieldname)) {
                  if (comparetext($field->Value, "{null}")) {
                    $sqlupdate .= $komma . "$field->Fieldname = NULL ";
                    $komma      = ", ";
                  } else {
                    $sqlupdate .= $komma . "$field->Fieldname = '" . addslashes($field->Value) . "' ";
                    $komma      = ", ";
                  }
                }
              }

              $sqlupdate .= "WHERE $recordprimaryfieldname = '{$fieldarray[$recordprimaryfieldname]->Value}' ";

              //EXECUTE update query
              if ($query) {
                $query->update($sqlupdate);

                //primaryfieldvalue bewaren
                if ($result) {
                  $primaryfieldvalue                  = $fieldarray[$recordprimaryfieldname]->Value;
                  $primaryfieldvalues[$recordformid]  = $fieldarray[$recordprimaryfieldname]->Value;
                }
              }

            //DELETE
            } elseif (comparetext($recordoperation, "DELETE")) {
              $sqldelete .= "DELETE FROM $tablename ";
              $sqldelete .= "WHERE $primaryfieldname = '$recordprimaryfieldvalue'";

              //EXECUTE delete query
              if ($query) {
                $query->delete($sqldelete);
              }

            //DISABLE
            } elseif (comparetext($recordoperation, "DISABLE")) {
              $sqlupdate .= "UPDATE $recordtablename ";
              $sqlupdate .= "SET Enabled = 'FALSE' ";
              $sqlupdate .= "WHERE $primaryfieldname = '$recordprimaryfieldvalue'\n";

              //EXECUTE disable query
              if ($query) {
                $query->update($sqlupdate);
              }
            }
          }
        }
      }
  }

  //FUNCTION SUBMIT CUSTOM
  function submit_xml_query($query, $xmlrecord, $operation, $tablename, $primaryfieldname, &$primaryfieldvalue, $xmlsubmit, &$xmloutput) {
    global $logging, $logfilename, $logpath, $logtablename;
    global $primaryfieldvalues;

    $recordoperation        = (string)$xmlrecord["operation"];
    $recordtablename        = (string)$xmlrecord["tablename"];
    $recordprimaryfieldname = (string)$xmlrecord["primaryfieldname"];
    $recordcustomfieldname  = (string)$xmlrecord["customfieldname"];
    $recordformid           = (string)$xmlrecord["formid"];
    $recordsubmit           = strtobool((string)$xmlrecord["submit"]);

    if ($query) {
      $result = $query->query();

      //primaryfieldvalue bewaren
      if ($result) {
        $primaryfieldvalue                  = $query->InsertID;
        $primaryfieldvalues[$recordformid]  = $query->InsertID;
      }

      $record = $xmloutput->addChild("record");
      $record->addAttribute("tablename", $recordtablename);
      $record->addAttribute("primaryfieldname", $recordprimaryfieldname);

      $field = $record->addChild("field", $primaryfieldvalue);
      $field->addAttribute("fieldname", $recordprimaryfieldname);
    }
  }
?>