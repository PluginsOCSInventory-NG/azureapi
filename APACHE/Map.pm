###############################################################################
## OCSINVENTORY-NG
## Copyright OCS Inventory team
## Web : http://www.ocsinventory-ng.org
##
## This code is open source and may be copied and modified as long as the source
## code is always made freely available.
## Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
################################################################################
 
package Apache::Ocsinventory::Plugins::Azureapi::Map;
 
use strict;
 
use Apache::Ocsinventory::Map;

$DATA_MAP{azuresub} = {
	mask => 0,
	multi => 1,
	auto => 1,
	delOnReplace => 1,
	sortBy => 'SUBNAME',
	writeDiff => 0,
	cache => 0,
	fields => {
        SUBID => {},
        SUBNAME => {},
        SUBSTATE => {}
	}
};

$DATA_MAP{azureresgroups} = {
	mask => 0,
	multi => 1,
	auto => 1,
	delOnReplace => 1,
	sortBy => 'RESNAME',
	writeDiff => 0,
	cache => 0,
	fields => {
		RESSUBID => {},
        RESNAME => {},
        RESTYPE => {},
        RESLOCATION => {}
	}
};

$DATA_MAP{azurevms} = {
	mask => 0,
	multi => 1,
	auto => 1,
	delOnReplace => 1,
	sortBy => 'VMID',
	writeDiff => 0,
	cache => 0,
	fields => {
        VMID => {},
		VMSUBID => {},
        VMRESGRP => {},
        VMNAME => {},
        VMTYPE => {},
        VMLOCATION => {},
        VMOWNER => {},
        VMTEAM => {},
        VMPLATFORM => {},
        VMUSERS => {},
		VMIMAGE => {},
		VMSKU => {},
		VMVERSION => {},
		VMEXACTVERSION => {},
		VMPROVSTATE => {}
	}
};

$DATA_MAP{azuredisk} = {
	mask => 0,
	multi => 1,
	auto => 1,
	delOnReplace => 1,
	sortBy => 'VMID',
	writeDiff => 0,
	cache => 0,
	fields => {
        VMID => {},
        DISKNAME => {},
		DISKSIZE => {},
		DISKTYPE => {},
		DISKCREATEOPTION => {},
		DISKCACHING => {}
	}
};

$DATA_MAP{azureres} = {
	mask => 0,
	multi => 1,
	auto => 1,
	delOnReplace => 1,
	sortBy => 'RESGRP',
	writeDiff => 0,
	cache => 0,
	fields => {
        RESGRP => {},
		RESSUBID => {},
        RESNAME => {},
		RESTYPE => {},
		RESLOCATION => {}
	}
};

1;