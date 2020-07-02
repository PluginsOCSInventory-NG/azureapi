###############################################################################
## OCSINVENTORY-NG
## Copyleft Gilles Dubois 2020
## Web : http://www.ocsinventory-ng.org
##
## This code is open source and may be copied and modified as long as the source
## code is always made freely available.
## Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
################################################################################

package Ocsinventory::Agent::Modules::Azureapi;

# Use
use LWP::UserAgent;
use HTTP::Request::Common qw(POST);
use JSON;
use POSIX;

# Auth
my %auth_hashes = (
    APP_ID => "APPLICATION_ID",
    TENANT_ID => "TENANT_ID",
    SECRET => "APPLICATION_SECRET",
);

# API Endpoint list
my %azure_api_references = (
    "azure_oauth_login" => "https://login.microsoftonline.com/TENANT_ID/oauth2/token",
    "azure_subscriptions" => "https://management.azure.com/subscriptions?api-version=2020-01-01",
    "azure_res_groups" => "https://management.azure.com/subscriptions/SUB_ID/resourcegroups?api-version=2019-10-01",
    "azure_vms_list" => "https://management.azure.com/subscriptions/SUB_ID/resourceGroups/RES_GROUP/providers/Microsoft.Compute/virtualMachines?api-version=2019-12-01",
    "azure_res_list" => "https://management.azure.com/subscriptions/SUB_ID/resourceGroups/RES_GROUP/resources?api-version=2019-10-01"
);

####### Create method ########
sub new 
{

   my $name="azureapi";   # Name of the module

   my (undef,$context) = @_;
   my $self = {};

   #Create a special logger for the module
   $self->{logger} = new Ocsinventory::Logger ({
            config => $context->{config}
   });

   $self->{logger}->{header}="[$name]";

   $self->{context}=$context;

   $self->{structure}= {
                        name => $name,
                        start_handler => undef,    #or undef if don't use this hook
                        prolog_writer => undef,    #or undef if don't use this hook
                        prolog_reader => undef,    #or undef if don't use this hook
                        inventory_handler => $name."_inventory_handler",    #or undef if don't use this hook
                        end_handler => undef    #or undef if don't use this hook
   };

   bless $self;
}

######### Hook methods ############
sub azureapi_inventory_handler 
{

    my $self = shift;
    my $logger = $self->{logger};

    my $common = $self->{context}->{common};

    # Debug log for inventory
    $logger->debug("Starting Azure inventory plugin");

    # Login to Azure
    my $auth_infos = send_auth_query(
        $auth_hashes{'TENANT_ID'},
        $auth_hashes{'APP_ID'},
        $auth_hashes{'SECRET'}
    );

    my $azure_token = $auth_infos->{'access_token'};
    
    if(defined $azure_token){
        $logger->debug("Log in to Azure API successful");

        my $subs_api_return = send_api_query($azure_api_references{"azure_subscriptions"}, $azure_token);
        $logger->debug("Querying subcriptions ...");

        # For each azure subscription
        foreach (@{$subs_api_return->{'value'}}){

            # Get sub id
            my $sub_id = $_->{"subscriptionId"};

            # Push sub to XML
            push @{$common->{xmltags}->{AZURESUB}},
            {
                SUBID => [$sub_id],
                SUBNAME => [$_->{"displayName"}],
                SUBSTATE => [$_->{"state"}]
            };

            # Get resources group in this azure sub
            my $resgrp_endpoint = $azure_api_references{"azure_res_groups"};
            $resgrp_endpoint =~ s/SUB_ID/$sub_id/g;
            my $resgrp_api_return = send_api_query($resgrp_endpoint, $azure_token);

            $logger->debug("Querying resources groups ...");

            foreach (@{$resgrp_api_return->{'value'}}){

                my $resgrp_name = $_->{"name"};

                push @{$common->{xmltags}->{AZURERESGROUPS}},
                {
                    RESSUBID => [$sub_id],
                    RESNAME => [$_->{"name"}],
                    RESTYPE => [$_->{"type"}],
                    RESLOCATION => [$_->{"location"}]
                };

                # Get VMs in this azure res grp
                my $vms_endpoint = $azure_api_references{"azure_vms_list"};
                $vms_endpoint =~ s/SUB_ID/$sub_id/g;
                $vms_endpoint =~ s/RES_GROUP/$resgrp_name/g;
                my $vms_list = send_api_query($vms_endpoint, $azure_token);

                $logger->debug("Querying vms groups in res groups $resgrp_name ...");

                foreach (@{$vms_list->{'value'}}){

                    my $vmid = $_->{"properties"}->{"vmId"};

                    push @{$common->{xmltags}->{AZUREVMS}},
                    {
                        VMID => [$vmid],
                        VMRESGRP => [$resgrp_name],
                        VMSUBID => [$sub_id],
                        VMNAME => [$_->{"name"}],
                        VMTYPE => [$_->{"type"}],
                        VMLOCATION => [$_->{"location"}],
                        VMOWNER => ["N/A"],
                        VMTEAM => ["N/A"],
                        VMPLATFORM => [$_->{"properties"}->{"storageProfile"}->{"imageReference"}->{"publisher"}],
                        VMUSERS => ["N/A"],
                        VMIMAGE => [$_->{"properties"}->{"storageProfile"}->{"imageReference"}->{"offer"}],
                        VMSKU => [$_->{"properties"}->{"storageProfile"}->{"imageReference"}->{"sku"}],
                        VMVERSION => [$_->{"properties"}->{"storageProfile"}->{"imageReference"}->{"version"}],
                        VMEXACTVERSION => [$_->{"properties"}->{"storageProfile"}->{"imageReference"}->{"exactVersion"}],
                        VMPROVSTATE => [$_->{"properties"}->{"provisioningState"}]
                    };

                    push @{$common->{xmltags}->{AZUREDISK}},
                    {
                        VMID => [$vmid],
                        DISKNAME => [$_->{"properties"}->{"storageProfile"}->{"osDisk"}->{"name"}],
                        DISKSIZE => [$_->{"properties"}->{"storageProfile"}->{"osDisk"}->{"diskSizeGB"}],
                        DISKTYPE => [$_->{"properties"}->{"storageProfile"}->{"osDisk"}->{"osType"}],
                        DISKCREATEOPTION => [$_->{"properties"}->{"storageProfile"}->{"osDisk"}->{"createOption"}],
                        DISKCACHING => [$_->{"properties"}->{"storageProfile"}->{"osDisk"}->{"caching"}]
                    };

                    foreach (@{$_->{"properties"}->{"storageProfile"}->{"dataDisks"}}){
                        push @{$common->{xmltags}->{AZUREDISK}},
                        {
                            VMID => [$vmid],
                            DISKNAME => [$_->{"name"}],
                            DISKSIZE => [$_->{"diskSizeGB"}],
                            DISKTYPE => [$_->{"osType"}],
                            DISKCREATEOPTION => [$_->{"createOption"}],
                            DISKCACHING => [$_->{"caching"}]
                        };
                    }

                }

                # Get resources in Resgroup
                my $res_endpoint = $azure_api_references{"azure_res_list"};
                $res_endpoint =~ s/SUB_ID/$sub_id/g;
                $res_endpoint =~ s/RES_GROUP/$resgrp_name/g;
                my $res_list = send_api_query($res_endpoint, $azure_token);

                $logger->debug("Querying resources in res groups $resgrp_name ...");

                foreach (@{$res_list->{'value'}}){
                    push @{$common->{xmltags}->{AZURERES}},
                    {
                        RESGRP => [$resgrp_name],
                        RESSUBID => [$sub_id],
                        RESNAME => [$_->{"name"}],
                        RESTYPE => [$_->{"type"}],
                        RESLOCATION => [$_->{"location"}]
                    };
                }

            }  

        }
        
    }else{
        $logger->debug("An error occured during login to Azure API");
    }

}

sub manage_json_pp_bool 
{

  my $data_check;

  # Get passed arguments
  ($data_check) = @_;

  if ($data_check){
    return "true";
  }else{
    return "false";
  }

}

sub send_auth_query
{
    # Variables declaration
    my $lwp_useragent;
    my $server_endpoint;
    my $restpath;
    my $auth_dig;
    my $req;
    my $resp;
    my $message;
    my $offset;

    # Get passed arguments
    ($tenant_id, $app_id, $secret) = @_;

    $lwp_useragent = LWP::UserAgent->new;

    # Post data
    my $post_data = [
        'grant_type' => "client_credentials",
        'client_id' => $app_id,
        'client_secret' => $secret,
        'resource' => "https://management.azure.com/"
    ];

    # Auth
    my $auth_endpoint = $azure_api_references{"azure_oauth_login"};
    $auth_endpoint =~ s/TENANT_ID/$tenant_id/g;

    # Disable SSL Verify hostname
    $lwp_useragent->ssl_opts( verify_hostname => 0 ,SSL_verify_mode => 0x00);

    # Send request
    my $res = $lwp_useragent->request(POST($auth_endpoint, $post_data));

    # Manage return
    if ($res->is_success) {
        return decode_json($res->content);
    } else {
        return "Error: ", $res->status_line, "\n";
    }
}

# Query API to the nutanix server
sub send_api_query
{

  # Get passed arguments
  ($server_endpoint, $token) = @_;

  $lwp_useragent = LWP::UserAgent->new;

  # set custom HTTP request header fields
  $req = HTTP::Request->new(GET => $server_endpoint);
  $req->header('Content-Type' => 'application/json');
  $req->header('Authorization' => "Bearer $token");

  # Disable SSL Verify hostname
  $lwp_useragent->ssl_opts( verify_hostname => 0 ,SSL_verify_mode => 0x00);

  $resp = $lwp_useragent->request($req);
  if ($resp->is_success) {
      return decode_json($resp->content);
  }
  else {
      return $resp->message;
  }

}
