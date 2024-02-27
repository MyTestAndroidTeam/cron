<?php
    /**
     * Created by PhpStorm.
     * User:      fduijnho
     * Author:    Frans-Willem Duijnhouwer
     * Copyright: EasyIQ
     */

    //module dir
    $apiV1CmsDir = realpath (dirname (__FILE__). '/..') . '/web/api/v1/cms/';

    //Execute the CMS API specific common settings
    require ($apiV1CmsDir . 'include/common.php');

    //General (shared) classes
    require (CONST_DIR_CMS_CLASS_CONTROLLER . 'clsLogging.php');
    $logger = new clsLogging();
    $logger->initLogFile (CONST_FILE_CMSCRON_LOG);

    //Fire up cron script
    require (CONST_DIR_CMS_CLASS_CONTROLLER. 'clsCron.php');
    $oCron = new clsCron($logger);
    $oCron->cronCheckRecurringRepairs ();
