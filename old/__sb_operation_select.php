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

  //OPERATIONS: EDIT/VIEW/STACK/COPY/FILL/FILLGRID/FILLCOMBO/FILLCHART

  //resultaat in JSON omzetten
  if ($operation == "EDIT"
  ||  $operation == "STACK"
  ||  $operation == "VIEW"
  ||  $operation == "COPY"
  ||  $operation == "FILL"
  ||  $operation == "FILLGRID"
  ||  $operation == "FILLTREE"
  ||  $operation == "FILLCHART"
  ||  $operation == "FILLCALENDAR"
  ||  $operation == "FILLCOMBO") {
    $json = $query->getJSON($resultarray);

    /*
    if ($resulttype == "json") {
      if ($resultformat == "record") {
        //FORM
        if ($resultarray) {
          //1 enkele row (bijv select voor een form)
          $jsonrow  = json_row_array($resultarray, $rowcount, $xmldefault);
          $json     = json_encode($jsonrow);
        } else {
          $jsonrow  = json_row_empty($fields, $xmldefault);
          $json     = json_encode($jsonrow);
        }

      } elseif ($resultformat == "fields") {
        //FIELDS
        if ($resultarray) {
          //alle fields als afzonderlijke rows
          $jsonrows   = json_fields_array($resultarray, true);
          $json       = json_encode($jsonrows);
        } else {
          $jsonrow    = json_fields_empty($fields, $xmldefault);
          $json       = json_encode($jsonrow);
        }

      } elseif ($resultformat == "fullfields") {
        //FULLFIELDS
        if ($resultarray) {
          //alle gevulde fields als afzonderlijke rows
          $jsonrows   = json_fields_array($resultarray, false);
          $json       = json_encode($jsonrows);
        } else {
          $jsonrow    = json_fields_empty($fields, $xmldefault);
          $json       = json_encode($jsonrow);
        }

      } elseif ($resultformat == "grid") {
        //GRID
        if ($resultarray) {
          //meerdere rows (bijv select voor een grid)
          $jsonrows = json_rows_array($resultarray, $rowcount, $xmldefault, $page, $limit);
          $json     = json_encode($jsonrows);
        } else {
          $jsonrow  = json_rows_empty($fields, $xmldefault);
          $json     = json_encode($jsonrow);
        }

      } elseif ($resultformat == "chart") {
        //CHART
        $jsonrows   = json_chart_array($resultarray, $rowcount, $amountfieldname, $groupingfieldnames[0], $groupingfieldnames[1], $totalfieldname);
        $json       = json_encode($jsonrows);

      } elseif ($resultformat == "calendar") {
        //CALENDAR
        $jsonrows   = json_calendar_array($resultarray, $rowcount, $primaryfieldname, $titlefieldname, $datestartfieldname, $dateendfieldname);
        $json       = json_encode($jsonrows);

      } elseif ($resultformat == "tree") {
        //TREE
        if ($resultarray) {
          $jsonrows = json_tree_array($resultarray, $rowcount, $primaryfieldname, $parentfieldname);
          $json     = json_encode($jsonrows);
        } else {
          $jsonrow  = json_rows_empty($fields, $xmldefault);
          $json     = json_encode($jsonrow);
        }

      } elseif ($resultformat == "column") {
        //COLUMN
        $jsonrows   = json_columns_array($resultarray, $rowcount, $xmlcolumns, $page, $limit);
        $json       = json_encode($jsonrows);
      }
    }
    */

  //OPERATION ADD
  } else if ($operation == "ADD") {
      $jsonrow  = json_row_empty($fields, $xmldefault);
      $json     = json_encode($jsonrow);
  }


  //set header
  header('Content-type: application/json');

  //OUTPUT json
  if (isnotempty($json)) {
    echo forceUTF8($json);
  }
?>