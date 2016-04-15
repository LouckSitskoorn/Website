<?php
	/*
	* Created on 16 dec 2008
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
  //port                  (number   - mail server port)
  //layer                 (string   - mail transport layer)
  //username              (string   - mail username)
  //password              (string   - mail password)
  //subject               (string   - mail subject)
  //from                  (string   - mail from emailadressen)
  //to                    (string   - mail to emailadressen)
  //replyto               (string   - mail replyto emailadressen)
  //cc                    (string   - mail cc emailadressen
  //bcc                   (string   - mail bcc emailadressen

  //OPERATIONS: MAIL

  //INCLUDES  framework
  include_once __DIR__ . "/classes/sb/__sb_xmltemplate_mail.php";

  //INCLUDES  classses
  include_once __DIR__ . "/libraries/phpmailer/class.phpmailer.php";


  //create resultset
  $resultset = array(
    "success"    => false,
    "message"    => "Unknown error",
    "mailerid"   => $senderid
  );

  //template inlezen
  if ($resultarray
  &&  !empty($resultarray) )  {
    //template aanmaken
    if (isnotempty($templatefilename)) {
      $mailtemplate = new SB_XMLTemplate_Mail();

      $mailtemplate->ID                 = "EmailTemplate";
      $mailtemplate->CustomPath         = dirname(__FILE__) . "/../usersettings/" . stripouterslashes($organisatiepath);
      $mailtemplate->Filename           = $templatefilename;
      $mailtemplate->Params             = $params;
      $mailtemplate->PrimaryFieldName   = $primaryfieldname;
      $mailtemplate->PrimaryFieldValue  = $primaryfieldvalue;
      $mailtemplate->Request            = $_REQUEST;
      $mailtemplate->RootPath           = dirname(__FILE__) . "/../";
      $mailtemplate->Session            = $_SESSION;
      $mailtemplate->Values             = $values;

      //template lezen en parsen
      $mailtemplate->readTemplate();
      $mailtemplate->init();

      $original_body = "";
      $original_body .= $mailtemplate->outputCSS();
      $original_body .= $mailtemplate->outputHTML();

  	  //replace values
  	  $original_body = jitReplace($original_body);

      $fieldnames = $query->getFieldnames();
      foreach($resultarray as $key=>$record) {
        $body    =  $original_body;
        $subject =  $original_subject;
        $from    =  $original_from;
        $fromname=  $original_fromname;
        $replyto =  $original_replyto;
        $to      =  $original_to;
        $cc      =  $original_cc;
        $bcc     =  $original_bcc;

        //vervang alle [%...] door velden uit record
        foreach($fieldnames as $fieldname) {
          $body = str_ireplace('[%'.trim($fieldname).']', $record[$fieldname], $body);
          $body = str_ireplace('[%%'.trim($fieldname).']', cryptConvert($record[$fieldname], "crid", false), $body);

          $subject = str_ireplace('[%'.trim($fieldname).']', $record[$fieldname], $subject);
          $from = str_ireplace('[%'.trim($fieldname).']', $record[$fieldname], $from);
          $fromname = str_ireplace('[%'.trim($fieldname).']', $record[$fieldname], $fromname);
          $replyto = str_ireplace('[%'.trim($fieldname).']', $record[$fieldname], $replyto);
          $to = str_ireplace('[%'.trim($fieldname).']', $record[$fieldname], $to);
          $cc = str_ireplace('[%'.trim($fieldname).']', $record[$fieldname], $cc);
          $bcc = str_ireplace('[%'.trim($fieldname).']', $record[$fieldname], $bcc);
        }

        //vervang alle [%...] customvelden
        if (isnotempty($customfieldnames)) {
          $customfields  = explode(",", $customfieldnames);
          $xmlcustom = false;
          foreach ($customfields as $customfield) {
            if (isnotempty($record[trim($customfield)])) {
              //$xmlcustom =  simplexml_load_string(sb_utf8_encode($record[trim($customfield)]));
              $xmlcustom =  simplexml_load_string($record[trim($customfield)]);
              if ($xmlcustom) {
                foreach ($xmlcustom->children() as $node) {
                  $nodename = $node->getName();
                  //node is fields?
                  if (comparetext($nodename, "fields")) {
                    foreach($node->children() as $fieldnode) {
                      $fieldnodename = $fieldnode->getName();
                      //node is field?
                      if (comparetext($fieldnodename, "field")) {
                        //field attributes bepalen
                        $fieldname        = (string)$fieldnode["fieldname"];
                        $fieldvalue       = (string)$fieldnode;

                        //replace customvelden
                        $body = str_ireplace('[%'.trim($fieldname).']', $fieldvalue, $body);
                      }
                    }
                  }
                }
              }
            }
          }
        }

        //create mailer object
        $mailer = new PHPMailer();
        $mailer->IsSMTP();

        try {
          if (isnotempty($to)) {
            //mailer properties
            $mailer->Host     = $server;
            $mailer->IsSMTP();
            $mailer->SMTPAuth = true;
            $mailer->SMTPDebug= false;
            $mailer->SMTPSecure = ($layer) ? $layer : "";
            $mailer->Port = ($port) ? $port : 25;
            $mailer->Username = $username;
            $mailer->Password = $password;
            $mailer->CharSet  = "UTF-8";

            //FROM
            $mailer->SetFrom($from, $fromname);
            $mailer->AddReplyTo($from);

            //SUBJECT
            $mailer->Subject  = $subject;
            $mailer->AltBody  = "To view the message, please use an HTML compatible e-mail client.";
            $mailer->MsgHTML($body);

            //TO
            if (isnotempty($to)) {
              //spaties en komma's omzetten in puntkomma's
              $to=str_ireplace(" ", ";", $to);
              $to=str_ireplace(",", ";", $to);

              //geadresseerden bepalen
              $tos = explode(";", $to);
              foreach ($tos as $torecipient) {
                $mailer->AddAddress($torecipient);
              }
            }

  	        //CC
  	        if (isnotempty($cc)) {
  	          $ccs = explode(";", $cc);
    	        foreach ($ccs as $ccrecipient) {
  	            $mailer->AddCC($ccrecipient);
  	          }
  	        }

  	        //BCC
            if (isnotempty($bcc)) {
              $bccs = explode(";", $bcc);
              foreach ($bccs as $bccrecipient) {
  	            $mailer->AddBCC($bccrecipient);
    	        }
  	        }

  	        //ATTACHMENTS
  	        if (isnotempty($attachment)) {
              $attachments = explode(";", $attachment);
              foreach ($attachments as $attachmentfilename) {
                $mailer->AddAttachment(dirname(__FILE__) . "/../" . $attachmentfilename);
              }
  	        }

            //SEND MAIL
            try {
              if (!isempty($to) || !is_empty($cc)) {
                if ($mailer->Send()) {
                  $resultset = array(
                    "success"    => true,
                    "message"    => "E-mail successfully sent to " . $to,
                    "mailerid"   => $senderid
                  );
                } else {
                  $resultset = array(
                    "success"    => false,
                    "message"    => "{$mailer->ErrorInfo}",
                    "mailerid"   => $senderid
                  );
                }
              } else {
                $resultset = array(
                  "success"    => true,
                  "message"    => "No addressee",
                  "mailerid"   => $senderid
                );
              }
            } catch (phpmailerException $e) {
                $resultset = array(
                  "success"    => false,
                  "message"    => "{$mailer->ErrorInfo}",
                  "mailerid"   => $senderid
                );
            }
          }

        } catch (phpmailerException $e) {
          $resultset = array(
            "success"    => false,
            "message"    => "{$e->errorMessage()}",
            "mailerid"   => $senderid
          );

        } catch (Exception $e) {
          $resultset = array(
            "success"    => false,
            "message"    => "{$e->getMessage()}",
            "mailerid"   => $senderid
          );
        }

	    }
    } else {
      $resultset = array(
        "success"    => false,
        "message"    => "Template not found",
        "mailerid"   => $senderid
      );
    }
  } else {
    $resultset = array(
      "success"    => false,
      "message"    => "Result empty",
      "mailerid"   => $senderid
    );
  }

  //logging
  if ($logging) {
    $logtableid   = logtext_totable_insert($query->ConnectionObject->DB, NULL, NULL, "logs", "PHP", "MAIL", "MAIL", $tablename, $primaryfieldname, $primaryfieldvalue, !$resultset["success"], 0, addquotes($resultset["message"]), 0);
  }

  //json result
  echo json_encode($resultset);
?>