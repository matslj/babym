<?php
// ===========================================================================================
//
// config.php
//
// Config-file for database and SQL related issues. All SQL-statements are usually stored in this
// directory (TP_SQLPATH). This files contains global definitions for table names and so.
//
// Author: Mats Ljungquist
//

// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
define('DB_HOST', 	    'localhost');           // The database host
define('DB_USER', 	    'mats');		// The username of the database
define('DB_PASSWORD', 	'hemligt');		// The users password
define('DB_DATABASE', 	'sanxion');		// The name of the database to use

//
// The following supports having many databases in one database by using table/view prefix.
//
define('DB_PREFIX',     'bby_');    // Prefix to use infront of tablename and views

// -------------------------------------------------------------------------------------------
//
// Define the names for the database (tables, views, procedures, functions, triggers)
//
define('DBT_User', 	    	DB_PREFIX . 'User');
define('DBT_Group', 		DB_PREFIX . 'Group');
define('DBT_GroupMember',	DB_PREFIX . 'GroupMember');
define('DBT_Statistics',	DB_PREFIX . 'Statistics');
define('DBT_Sida',	     	DB_PREFIX . 'Sida');
define('DBT_BabyData',		DB_PREFIX . 'BabyData');

// Stored routines concerning page and pictures
define('DBSP_PInsertOrUpdateSida',	           DB_PREFIX . 'PInsertOrUpdateSida');
define('DBSP_PGetSidaDetails',		           DB_PREFIX . 'PGetSidaDetails');
define('DBSP_PGetSidaDetailsById',	           DB_PREFIX . 'PGetSidaDetailsById');
define('DBUDF_FCheckUserIsOwnerOrAdminOfSida', DB_PREFIX . 'FCheckUserIsOwnerOrAdminOfSida');
define('DBUDF_CheckUserIsAdmin',	           DB_PREFIX . 'FCheckUserIsAdmin');

// Stored routines concerning user
define('DBSP_AuthenticateUser',             DB_PREFIX . 'PAuthenticateUser');
define('DBSP_CreateUser',                   DB_PREFIX . 'PCreateUser');
define('DBSP_GetUserDetails',               DB_PREFIX . 'PGetUserDetails');
define('DBSP_SetUserDetails',               DB_PREFIX . 'PSetUserDetails');
define('DBSP_SetUserPassword',              DB_PREFIX . 'PSetUserPassword');
define('DBSP_SetUserEmail',                 DB_PREFIX . 'PSetUserEmail');
define('DBSP_UpdateLastLogin',              DB_PREFIX . 'PUpdateLastLogin');
define('DBSP_SetUserAvatar',                DB_PREFIX . 'PSetUserAvatar');
define('DBSP_SetUserGravatar',              DB_PREFIX . 'PSetUserGravatar');
define('DBUDF_FCheckUserIsOwnerOrAdmin',    DB_PREFIX . 'FCheckUserIsOwnerOrAdmin');
define('DBUDF_GetGravatarLinkFromEmail',    DB_PREFIX . 'FGetGravatarLinkFromEmail');
define('DBSP_SetUserNameAndEmail',          DB_PREFIX . 'PSetUserNameAndEmail');
define('DBSP_SetTournamentUser',            DB_PREFIX . 'PSetTournamentUser');
define('DBSP_CreateUserAccountOrEmail',     DB_PREFIX . 'PCreateUserAccountOrEmail');
define('DBSP_CreateUserAccount',            DB_PREFIX . 'PCreateUserAccount');
define('DBSP_DeleteUser',                   DB_PREFIX . 'PDeleteUser');

// Stored routines concerning baby data
define('DBSP_CreateBabyData',               DB_PREFIX . 'PCreateBabyData');
define('DBSP_EditBabyData',                 DB_PREFIX . 'PEditBabyData');
define('DBSP_EditNoteBabyData',             DB_PREFIX . 'PEditNoteBabyData');
define('DBSP_EditValueBabyData',            DB_PREFIX . 'PEditValueBabyData');
define('DBSP_DeleteBabyData',               DB_PREFIX . 'PDeleteBabyData');

// Triggers
define('DBTR_TInsertUser',	                DB_PREFIX . 'TInsertUser');
?>