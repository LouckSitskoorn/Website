<?php
	/*
	* Created on 11 feb 2009
	* by Louck Sitskoorn
	*/

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

  //OPERATIONS: DELETE

  //objecten
  $xmloutput = new SimpleXMLElement("<xml></xml>");

  //DELETE
  if ($tablename 
  &&  $primaryfieldname
  &&  $primaryfieldvalue) {
    if ($query) {
      $sqldelete .= "DELETE FROM $tablename ";
      $sqldelete .= "WHERE $primaryfieldname = '$primaryfieldvalue'";

      //execute delete
      $timerstart = timer_start();
      $query->execute($sqldelete);
      $timerend   = timer_end($timerstart);

      fb_sql($sqldelete);

      $record = $xmloutput->addChild("record");
      $record->addAttribute("tablename", $tablename);
      $record->addAttribute("primaryfieldname", $primaryfieldname);

      $field = $record->addChild("field", $primaryfieldvalue);
      $field->addAttribute("fieldname", $primaryfieldname);
    }

    //echo $xmloutput->asXML();
    $jsonrows = json_rows_xml($xmloutput);
    $json     = json_encode($jsonrows);

    echo $json;
  }

?>