<?php
  /*
   * Created on 25 mei 2011
   * by Louck Sitskoorn
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

  //OPERATIONS: SCHEDULE

  //ini sets
  set_include_path (dirname(__FILE__) . "/libraries/ZendFramework/library/");

  //framework includes
  include_once __DIR__ . "/classes/sb/__sb_xmltemplate_mail.php";

  //library includes
  require_once dirname(__FILE__) . "/libraries/ZendFrameowrk/library/Zend/Loader.php";

  //function includes
  include_once __DIR__ . "/functions/_google_functions.php";

  //init Gdata
  Zend_Loader::loadClass('Zend_Gdata');
  Zend_Loader::loadClass('Zend_Gdata_AuthSub');
  Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
  Zend_Loader::loadClass('Zend_Gdata_Calendar');

  //create resultset
  $resultset = array(
    "success"    => false,
    "message"    => "Unknown error"
  );


  $username     = "servicebeheer.test2@gmail.com";
  $password     = "bobdylan74";
  $clientlogin  = false;

  if ($clientlogin) {
    //CLIENT login (not safe)
    $service  = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; // predefined service name for calendar

    try {
       $client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
    } catch (Zend_Gdata_App_CaptchaRequiredException $cre) {
        fbb('URL of CAPTCHA image: ' . $cre->getCaptchaUrl());
        fbb('Token ID: ' . $cre->getCaptchaToken());
  
        $resultset = array(
          "success"    => false,
          "message"    => "{$cre->getCaptchaUrl()}"
        );
    } catch (Zend_Gdata_App_AuthException $ae) {
        fbb('Problem authenticating: ' . $ae->exception());
  
        $resultset = array(
          "success"    => false,
          "message"    => "{$ae->exception()}"
        );
    } catch (Exception $e) {
        fbb('Problem : ' . $e->getMessage());
  
        $resultset = array(
          "success"    => false,
          "message"    => "{$e->getMessage()}"
        );
    }

  } else {
    //AUTHSUB login
    if (!isset($_SESSION['sessiontoken'])) {
        $resultset = array(
          "success"    => false,
          "message"    => "Not logged in"
        );
    } else {
      // Create an authenticated HTTP Client to talk to Google.
      try {
         $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessiontoken']);
      } catch (Zend_Gdata_App_CaptchaRequiredException $cre) {
          fbb('URL of CAPTCHA image: ' . $cre->getCaptchaUrl());
          fbb('Token ID: ' . $cre->getCaptchaToken());
    
          $resultset = array(
            "success"    => false,
            "message"    => "URL of CAPTCHA image : {$cre->getCaptchaUrl()}"
          );
      } catch (Zend_Gdata_App_AuthException $ae) {
          fbb('Problem authenticating: ' . $ae->exception());
    
          $resultset = array(
            "success"    => false,
            "message"    => "Problem authenticating : {$ae->exception()}"
          );
      } catch (Exception $e) {
          fbb('Problem : ' . $e->getMessage());
    
          $resultset = array(
            "success"    => false,
            "message"    => "Error : {$e->getMessage()}"
          );
      }
    }
  }

  if ($resultarray) {
    // Create a Gdata object using the authenticated Http Client
    $cal = new Zend_Gdata_Calendar($client);  

    // Create a new entry using the calendar service's magic factory method
    $event= $cal->newEventEntry();

    // Populate the event with the desired information
    // Note that each attribute is crated as an instance of a matching class
    $event->title = $cal->newTitle($resultarray[0]["Titel"]);
    $event->where = array($cal->newWhere("Kantoor Witte Huis"));
    $event->content =
    $cal->newContent($resultarray[0]["Tekst"]);

    // Set the date using RFC 3339 format.
    $startDate = $resultarray[0]["DatumUitvoering"];
    $startTime = $resultarray[0]["TijdVanaf"];
    $endDate = $resultarray[0]["DatumUitvoering"];
    $endTime = $resultarray[0]["TijdTot"];
    $tzOffset = "+01";

    $when = $cal->newWhen();
    $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
    $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
    $event->when = array($when);

    // Upload the event to the calendar server
    // A copy of the event as it is recorded on the server is returned
    $newEvent = $cal->insertEvent($event);

    $resultset = array(
      "success"    => true,
      "message"    => "Event successfully scheduled." 
    );
  } else {
    $resultset = array(
      "success"    => false,
      "message"    => "Result empty"
    );
  }

  //logging
  if ($logging) {
    $logtableid   = logtext_totable_insert($query->ConnectionObject->DB, NULL, NULL, "logs", "PHP", "SCHEDULE", "SCHEDULE", $tablename, $primaryfieldname, $primaryfieldvalue, !$resultset["success"], 0, addquotes($resultset["message"]), 0);
  }

  //json result
  echo json_encode($resultset);
?>