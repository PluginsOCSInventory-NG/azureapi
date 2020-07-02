# Azure API

Retrive Azure VM List using API

## Webconsole and communication server setup 

You can install the plugin using the classic way of installing OCS Plugins : 
* https://wiki.ocsinventory-ng.org/10.Plugin-engine/Using-plugins-installer/

## Install the agent file

The rest API call are made trough an Unix Agent, thus a specific module need to be installed in one of your OCS agent.

You will have to put the agent file (in the agent folder of the plugin release) in the Modules directory.
It can be found here for most of the systems : /usr/local/share/perl/5.26.1/Ocsinventory/Agent/Modules/

Then edit your modules.conf file present in /etc/ocsinventory-agent/ or /etc/ocsinventory if you installed using the RPM packages and add the following line : 
* use Ocsinventory::Agent::Modules::Azureapi;

## Configure the agent file

Now, you need to configure the agent to scan the azure vm list.

In the Azureapi.pm file from line 20 to 25, you will find the following code : 
```
my %auth_hashes = (
    APP_ID => "APPLICATION_ID",
    TENANT_ID => "TENANT_ID",
    SECRET => "SECRET",
);
```