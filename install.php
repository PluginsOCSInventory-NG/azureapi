<?php

/**
 * This function is called on installation and is used to create database schema for the plugin
 */
function extension_install_azureapi()
{
    $commonObject = new ExtensionCommon;

    $commonObject -> sqlQuery("DROP TABLE IF EXISTS `azureresgroups` , `azurevms` , `azuredisk`, `azureres`, `azuresub`;");
    
    $commonObject -> sqlQuery("CREATE TABLE `azuresub` (
        `ID` INT(11) NOT NULL AUTO_INCREMENT,
        `HARDWARE_ID` INT(11) NOT NULL,
        `SUBID` VARCHAR(255) DEFAULT NULL,
        `SUBNAME` VARCHAR(255) DEFAULT NULL,
        `SUBSTATE` VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY  (`ID`,`HARDWARE_ID`)
    ) ENGINE=INNODB;");

    $commonObject -> sqlQuery("CREATE TABLE `azureresgroups` (
        `ID` INT(11) NOT NULL AUTO_INCREMENT,
        `HARDWARE_ID` INT(11) NOT NULL,
        `RESSUBID` VARCHAR(255) DEFAULT NULL,
        `RESNAME` VARCHAR(255) DEFAULT NULL,
        `RESTYPE` VARCHAR(255) DEFAULT NULL,
        `RESLOCATION` VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY  (`ID`,`HARDWARE_ID`)
    ) ENGINE=INNODB;");

    $commonObject -> sqlQuery("CREATE TABLE `azurevms` (
        `ID` INT(11) NOT NULL AUTO_INCREMENT,
        `HARDWARE_ID` INT(11) NOT NULL,
        `VMID` VARCHAR(255) DEFAULT NULL,
        `VMSUBID` VARCHAR(255) DEFAULT NULL,
        `VMRESGRP` VARCHAR(255) DEFAULT NULL,
        `VMNAME` VARCHAR(255) DEFAULT NULL,
        `VMTYPE` VARCHAR(255) DEFAULT NULL,
        `VMLOCATION` VARCHAR(255) DEFAULT NULL,
        `VMOWNER` VARCHAR(255) DEFAULT NULL,
        `VMTEAM` VARCHAR(255) DEFAULT NULL,
        `VMPLATFORM` VARCHAR(255) DEFAULT NULL,
        `VMUSERS` VARCHAR(255) DEFAULT NULL,
        `VMIMAGE` VARCHAR(255) DEFAULT NULL,
        `VMSKU` VARCHAR(255) DEFAULT NULL,
        `VMVERSION` VARCHAR(255) DEFAULT NULL,
        `VMEXACTVERSION` VARCHAR(255) DEFAULT NULL,
        `VMPROVSTATE` VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY  (`ID`,`HARDWARE_ID`)
    ) ENGINE=INNODB;");

    $commonObject -> sqlQuery("CREATE TABLE `azuredisk` (
        `ID` INT(11) NOT NULL AUTO_INCREMENT,
        `HARDWARE_ID` INT(11) NOT NULL,
        `VMID` VARCHAR(255) DEFAULT NULL,
        `DISKNAME` VARCHAR(255) DEFAULT NULL,
        `DISKSIZE` VARCHAR(255) DEFAULT NULL,
        `DISKTYPE` VARCHAR(255) DEFAULT NULL,
        `DISKCREATEOPTION` VARCHAR(255) DEFAULT NULL,
        `DISKCACHING` VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY  (`ID`,`HARDWARE_ID`)
    ) ENGINE=INNODB;");

    $commonObject -> sqlQuery("CREATE TABLE `azureres` (
        `ID` INT(11) NOT NULL AUTO_INCREMENT,
        `HARDWARE_ID` INT(11) NOT NULL,
        `RESGRP` VARCHAR(255) DEFAULT NULL,
        `RESSUBID` VARCHAR(255) DEFAULT NULL,
        `RESNAME` VARCHAR(255) DEFAULT NULL,
        `RESTYPE` VARCHAR(255) DEFAULT NULL,
        `RESLOCATION` VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY  (`ID`,`HARDWARE_ID`)
    ) ENGINE=INNODB;");
}

/**
 * This function is called on removal and is used to destroy database schema for the plugin
 */
function extension_delete_azureapi()
{
    $commonObject = new ExtensionCommon;
    $commonObject -> sqlQuery("DROP TABLE IF EXISTS `azureresgroups` , `azurevms` , `azuredisk`, `azureres`, `azuresub`;");
}

/**
 * This function is called on plugin upgrade
 */
function extension_upgrade_azureapi()
{

}