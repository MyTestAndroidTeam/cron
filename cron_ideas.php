<?php
    
    //module dir
    $apiV1CmsDir = realpath (dirname (__FILE__). '/..') . '/web/api/v1/cms/';
    
    //Execute the CMS API specific common settings
    require ($apiV1CmsDir . 'include/common.php');

    //
    require_once (CONST_DIR_CMS_CLASS_CONTROLLER.'/clsLogging.php');
    $logger = new clsLogging();
    $logger->initLogFile (CONST_FILE_CMSCRON_LOG);

    require_once (CONST_DIR_CMS_CLASS_DATABASE.'/clsCmsMySQLDatabase.php');
    $database = new clsCmsMySQLDatabase($logger);

    //get ideas that are not set to deleted
    $database->clear();
    $database->addSelectField ( 'id_user');
    $database->addSelectField ( 'recordCreated');
    $database->addSelectField ( 'idea');
    $database->addTable (CONST_TBL_ideas);
    $database->addWhereKeyValue ('recordDeleted',0,CONST_DB_WHERETYPE_AND);
    $ideas = $database->dbSelect (CONST_DB_SELECT_ALL);

    //get users
    $database->clear();
    $database->addSelectField ( 'id');
    $database->addSelectField ( 'userFirstName');
    $database->addSelectField ( 'userLastName');
    $database->addTable (CONST_TBL_user);
    $users = $database->dbSelect (CONST_DB_SELECT_ALL);

    //organize users
    $userCache = array();
    foreach ($users as $user) {
        $userCache[$user['id']] = $user;
    }

    //stop if we do not have any ideas
    if (empty($ideas)) {
        exit();
    }

    //create data array
    $output = "";
    foreach ($ideas as $idea) {

        //nice date
        $ts = strtotime ($idea['recordCreated']);
        $date = date('d-m-Y', $ts);

        //user info
        $userName = "Onbekende gebruiker";
        $user = isset($userCache[$idea['id_user']])?$userCache[$idea['id_user']]:NULL;
        if (!empty($user)) {
            $userFirstName = $user[ 'userFirstName' ];
            $userLastName = $user[ 'userLastName' ];
            $userName = $userFirstName . ' ' . $userLastName;
        }

        //idea
        $ideaContent = $idea['idea'];
        $ideaContent = str_replace('"',"'",$ideaContent);

        //
        $output.= $date.': '.$userName.': '.$ideaContent."<br/>\n";
    }

    //send as email
    foreach (CONST_EMAIL_IDEAS_BOXES as $box) {
        sendMail ($box, date ('d-m-Y', time ()) . ': Ideeënbus', $output);
    }
    
    //clear table
    $database->clear();
    $database->addTable (CONST_TBL_ideas);
    $database->addWhereKeyValue ('recordDeleted',0,CONST_DB_WHERETYPE_AND);
    $database->dbSoftDelete (0);
