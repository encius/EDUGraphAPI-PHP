<?php
/**
 *  Copyright (c) Microsoft Corporation. All rights reserved. Licensed under the MIT license.
 *  See LICENSE in the project root for license information.
 */
//require  ('/home/site/wwwroot/vendor/autoload.php');
//tobereplaced
require  ('../../../../vendor/autoload.php');
require ('bootstrap.php');
require('MSGraphHelper.php');
require('DbHelper.php');

//$clientId =getenv("CLIENT_ID");
$clientId ='4e3fa16f-9909-4bf6-9a66-5560e97e7082';

$dbHelper = new DBHelper();
$msGraphHelper = new MSGraphHelper();
$organizations = $dbHelper->getOrganizations();

foreach ($organizations as $org)
{

//    $dataSyncRecord = $dbHelper->getOrCreateDataSyncRecord($org);
//    $users = $msGraphHelper->queryUsers($dataSyncRecord->deltaLink,$org->tenantId,$clientId);
//    foreach ($users as $user) {
//        $dbHelper->updateUser($user);
//    }
}




