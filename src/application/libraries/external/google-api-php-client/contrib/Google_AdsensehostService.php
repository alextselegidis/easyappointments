<?php
/*
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */


  /**
   * The "accounts" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $accounts = $adsensehostService->accounts;
   *  </code>
   */
  class Google_AccountsServiceResource extends Google_ServiceResource {


    /**
     * Get information about the selected associated AdSense account. (accounts.get)
     *
     * @param string $accountId Account to get information about.
     * @param array $optParams Optional parameters.
     * @return Google_Account
     */
    public function get($accountId, $optParams = array()) {
      $params = array('accountId' => $accountId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Account($data);
      } else {
        return $data;
      }
    }
    /**
     * List hosted accounts associated with this AdSense account by ad client id. (accounts.list)
     *
     * @param string $filterAdClientId Ad clients to list accounts for.
     * @param array $optParams Optional parameters.
     * @return Google_Accounts
     */
    public function listAccounts($filterAdClientId, $optParams = array()) {
      $params = array('filterAdClientId' => $filterAdClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Accounts($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "adclients" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $adclients = $adsensehostService->adclients;
   *  </code>
   */
  class Google_AccountsAdclientsServiceResource extends Google_ServiceResource {


    /**
     * Get information about one of the ad clients in the specified publisher's AdSense account.
     * (adclients.get)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client to get.
     * @param array $optParams Optional parameters.
     * @return Google_AdClient
     */
    public function get($accountId, $adClientId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AdClient($data);
      } else {
        return $data;
      }
    }
    /**
     * List all hosted ad clients in the specified hosted account. (adclients.list)
     *
     * @param string $accountId Account for which to list ad clients.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string maxResults The maximum number of ad clients to include in the response, used for paging.
     * @opt_param string pageToken A continuation token, used to page through ad clients. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @return Google_AdClients
     */
    public function listAccountsAdclients($accountId, $optParams = array()) {
      $params = array('accountId' => $accountId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdClients($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "adunits" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $adunits = $adsensehostService->adunits;
   *  </code>
   */
  class Google_AccountsAdunitsServiceResource extends Google_ServiceResource {


    /**
     * Delete the specified ad unit from the specified publisher AdSense account. (adunits.delete)
     *
     * @param string $accountId Account which contains the ad unit.
     * @param string $adClientId Ad client for which to get ad unit.
     * @param string $adUnitId Ad unit to delete.
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function delete($accountId, $adClientId, $adUnitId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
    /**
     * Get the specified host ad unit in this AdSense account. (adunits.get)
     *
     * @param string $accountId Account which contains the ad unit.
     * @param string $adClientId Ad client for which to get ad unit.
     * @param string $adUnitId Ad unit to get.
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function get($accountId, $adClientId, $adUnitId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
    /**
     * Get ad code for the specified ad unit, attaching the specified host custom channels.
     * (adunits.getAdCode)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client with contains the ad unit.
     * @param string $adUnitId Ad unit to get the code for.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string hostCustomChannelId Host custom channel to attach to the ad code.
     * @return Google_AdCode
     */
    public function getAdCode($accountId, $adClientId, $adUnitId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('getAdCode', array($params));
      if ($this->useObjects()) {
        return new Google_AdCode($data);
      } else {
        return $data;
      }
    }
    /**
     * Insert the supplied ad unit into the specified publisher AdSense account. (adunits.insert)
     *
     * @param string $accountId Account which will contain the ad unit.
     * @param string $adClientId Ad client into which to insert the ad unit.
     * @param Google_AdUnit $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function insert($accountId, $adClientId, Google_AdUnit $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
    /**
     * List all ad units in the specified publisher's AdSense account. (adunits.list)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client for which to list ad units.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeInactive Whether to include inactive ad units. Default: true.
     * @opt_param string maxResults The maximum number of ad units to include in the response, used for paging.
     * @opt_param string pageToken A continuation token, used to page through ad units. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @return Google_AdUnits
     */
    public function listAccountsAdunits($accountId, $adClientId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnits($data);
      } else {
        return $data;
      }
    }
    /**
     * Update the supplied ad unit in the specified publisher AdSense account. This method supports
     * patch semantics. (adunits.patch)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client which contains the ad unit.
     * @param string $adUnitId Ad unit to get.
     * @param Google_AdUnit $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function patch($accountId, $adClientId, $adUnitId, Google_AdUnit $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
    /**
     * Update the supplied ad unit in the specified publisher AdSense account. (adunits.update)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client which contains the ad unit.
     * @param Google_AdUnit $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function update($accountId, $adClientId, Google_AdUnit $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "reports" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $reports = $adsensehostService->reports;
   *  </code>
   */
  class Google_AccountsReportsServiceResource extends Google_ServiceResource {


    /**
     * Generate an AdSense report based on the report request sent in the query parameters. Returns the
     * result as JSON; to retrieve output in CSV format specify "alt=csv" as a query parameter.
     * (reports.generate)
     *
     * @param string $accountId Hosted account upon which to report.
     * @param string $startDate Start of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param string $endDate End of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string dimension Dimensions to base the report on.
     * @opt_param string filter Filters to be run on the report.
     * @opt_param string locale Optional locale to use for translating report output to a local language. Defaults to "en_US" if not specified.
     * @opt_param string maxResults The maximum number of rows of report data to return.
     * @opt_param string metric Numeric columns to include in the report.
     * @opt_param string sort The name of a dimension or metric to sort the resulting report on, optionally prefixed with "+" to sort ascending or "-" to sort descending. If no prefix is specified, the column is sorted ascending.
     * @opt_param string startIndex Index of the first row of report data to return.
     * @return Google_Report
     */
    public function generate($accountId, $startDate, $endDate, $optParams = array()) {
      $params = array('accountId' => $accountId, 'startDate' => $startDate, 'endDate' => $endDate);
      $params = array_merge($params, $optParams);
      $data = $this->__call('generate', array($params));
      if ($this->useObjects()) {
        return new Google_Report($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "adclients" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $adclients = $adsensehostService->adclients;
   *  </code>
   */
  class Google_AdclientsServiceResource extends Google_ServiceResource {


    /**
     * Get information about one of the ad clients in the Host AdSense account. (adclients.get)
     *
     * @param string $adClientId Ad client to get.
     * @param array $optParams Optional parameters.
     * @return Google_AdClient
     */
    public function get($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AdClient($data);
      } else {
        return $data;
      }
    }
    /**
     * List all host ad clients in this AdSense account. (adclients.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string maxResults The maximum number of ad clients to include in the response, used for paging.
     * @opt_param string pageToken A continuation token, used to page through ad clients. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @return Google_AdClients
     */
    public function listAdclients($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdClients($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "associationsessions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $associationsessions = $adsensehostService->associationsessions;
   *  </code>
   */
  class Google_AssociationsessionsServiceResource extends Google_ServiceResource {


    /**
     * Create an association session for initiating an association with an AdSense user.
     * (associationsessions.start)
     *
     * @param string $productCode Products to associate with the user.
     * @param string $websiteUrl The URL of the user's hosted website.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string userLocale The preferred locale of the user.
     * @opt_param string websiteLocale The locale of the user's hosted website.
     * @return Google_AssociationSession
     */
    public function start($productCode, $websiteUrl, $optParams = array()) {
      $params = array('productCode' => $productCode, 'websiteUrl' => $websiteUrl);
      $params = array_merge($params, $optParams);
      $data = $this->__call('start', array($params));
      if ($this->useObjects()) {
        return new Google_AssociationSession($data);
      } else {
        return $data;
      }
    }
    /**
     * Verify an association session after the association callback returns from AdSense signup.
     * (associationsessions.verify)
     *
     * @param string $token The token returned to the association callback URL.
     * @param array $optParams Optional parameters.
     * @return Google_AssociationSession
     */
    public function verify($token, $optParams = array()) {
      $params = array('token' => $token);
      $params = array_merge($params, $optParams);
      $data = $this->__call('verify', array($params));
      if ($this->useObjects()) {
        return new Google_AssociationSession($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "customchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $customchannels = $adsensehostService->customchannels;
   *  </code>
   */
  class Google_CustomchannelsServiceResource extends Google_ServiceResource {


    /**
     * Delete a specific custom channel from the host AdSense account. (customchannels.delete)
     *
     * @param string $adClientId Ad client from which to delete the custom channel.
     * @param string $customChannelId Custom channel to delete.
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function delete($adClientId, $customChannelId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'customChannelId' => $customChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * Get a specific custom channel from the host AdSense account. (customchannels.get)
     *
     * @param string $adClientId Ad client from which to get the custom channel.
     * @param string $customChannelId Custom channel to get.
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function get($adClientId, $customChannelId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'customChannelId' => $customChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * Add a new custom channel to the host AdSense account. (customchannels.insert)
     *
     * @param string $adClientId Ad client to which the new custom channel will be added.
     * @param Google_CustomChannel $postBody
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function insert($adClientId, Google_CustomChannel $postBody, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * List all host custom channels in this AdSense account. (customchannels.list)
     *
     * @param string $adClientId Ad client for which to list custom channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string maxResults The maximum number of custom channels to include in the response, used for paging.
     * @opt_param string pageToken A continuation token, used to page through custom channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @return Google_CustomChannels
     */
    public function listCustomchannels($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannels($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a custom channel in the host AdSense account. This method supports patch semantics.
     * (customchannels.patch)
     *
     * @param string $adClientId Ad client in which the custom channel will be updated.
     * @param string $customChannelId Custom channel to get.
     * @param Google_CustomChannel $postBody
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function patch($adClientId, $customChannelId, Google_CustomChannel $postBody, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'customChannelId' => $customChannelId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a custom channel in the host AdSense account. (customchannels.update)
     *
     * @param string $adClientId Ad client in which the custom channel will be updated.
     * @param Google_CustomChannel $postBody
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function update($adClientId, Google_CustomChannel $postBody, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "reports" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $reports = $adsensehostService->reports;
   *  </code>
   */
  class Google_ReportsServiceResource extends Google_ServiceResource {


    /**
     * Generate an AdSense report based on the report request sent in the query parameters. Returns the
     * result as JSON; to retrieve output in CSV format specify "alt=csv" as a query parameter.
     * (reports.generate)
     *
     * @param string $startDate Start of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param string $endDate End of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string dimension Dimensions to base the report on.
     * @opt_param string filter Filters to be run on the report.
     * @opt_param string locale Optional locale to use for translating report output to a local language. Defaults to "en_US" if not specified.
     * @opt_param string maxResults The maximum number of rows of report data to return.
     * @opt_param string metric Numeric columns to include in the report.
     * @opt_param string sort The name of a dimension or metric to sort the resulting report on, optionally prefixed with "+" to sort ascending or "-" to sort descending. If no prefix is specified, the column is sorted ascending.
     * @opt_param string startIndex Index of the first row of report data to return.
     * @return Google_Report
     */
    public function generate($startDate, $endDate, $optParams = array()) {
      $params = array('startDate' => $startDate, 'endDate' => $endDate);
      $params = array_merge($params, $optParams);
      $data = $this->__call('generate', array($params));
      if ($this->useObjects()) {
        return new Google_Report($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "urlchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdSenseHostService(...);
   *   $urlchannels = $adsensehostService->urlchannels;
   *  </code>
   */
  class Google_UrlchannelsServiceResource extends Google_ServiceResource {


    /**
     * Delete a URL channel from the host AdSense account. (urlchannels.delete)
     *
     * @param string $adClientId Ad client from which to delete the URL channel.
     * @param string $urlChannelId URL channel to delete.
     * @param array $optParams Optional parameters.
     * @return Google_UrlChannel
     */
    public function delete($adClientId, $urlChannelId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'urlChannelId' => $urlChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      if ($this->useObjects()) {
        return new Google_UrlChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * Add a new URL channel to the host AdSense account. (urlchannels.insert)
     *
     * @param string $adClientId Ad client to which the new URL channel will be added.
     * @param Google_UrlChannel $postBody
     * @param array $optParams Optional parameters.
     * @return Google_UrlChannel
     */
    public function insert($adClientId, Google_UrlChannel $postBody, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_UrlChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * List all host URL channels in the host AdSense account. (urlchannels.list)
     *
     * @param string $adClientId Ad client for which to list URL channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string maxResults The maximum number of URL channels to include in the response, used for paging.
     * @opt_param string pageToken A continuation token, used to page through URL channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @return Google_UrlChannels
     */
    public function listUrlchannels($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_UrlChannels($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_AdSenseHost (v4.1).
 *
 * <p>
 * Gives AdSense Hosts access to report generation, ad code generation, and publisher management capabilities.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/adsense/host/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_AdSenseHostService extends Google_Service {
  public $accounts;
  public $accounts_adclients;
  public $accounts_adunits;
  public $accounts_reports;
  public $adclients;
  public $associationsessions;
  public $customchannels;
  public $reports;
  public $urlchannels;
  /**
   * Constructs the internal representation of the AdSenseHost service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'adsensehost/v4.1/';
    $this->version = 'v4.1';
    $this->serviceName = 'adsensehost';

    $client->addService($this->serviceName, $this->version);
    $this->accounts = new Google_AccountsServiceResource($this, $this->serviceName, 'accounts', json_decode('{"methods": {"get": {"id": "adsensehost.accounts.get", "path": "accounts/{accountId}", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "Account"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "list": {"id": "adsensehost.accounts.list", "path": "accounts", "httpMethod": "GET", "parameters": {"filterAdClientId": {"type": "string", "required": true, "repeated": true, "location": "query"}}, "response": {"$ref": "Accounts"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));
    $this->accounts_adclients = new Google_AccountsAdclientsServiceResource($this, $this->serviceName, 'adclients', json_decode('{"methods": {"get": {"id": "adsensehost.accounts.adclients.get", "path": "accounts/{accountId}/adclients/{adClientId}", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "adClientId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "AdClient"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "list": {"id": "adsensehost.accounts.adclients.list", "path": "accounts/{accountId}/adclients", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "maxResults": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "10000", "location": "query"}, "pageToken": {"type": "string", "location": "query"}}, "response": {"$ref": "AdClients"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));
    $this->accounts_adunits = new Google_AccountsAdunitsServiceResource($this, $this->serviceName, 'adunits', json_decode('{"methods": {"delete": {"id": "adsensehost.accounts.adunits.delete", "path": "accounts/{accountId}/adclients/{adClientId}/adunits/{adUnitId}", "httpMethod": "DELETE", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "adClientId": {"type": "string", "required": true, "location": "path"}, "adUnitId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "AdUnit"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "get": {"id": "adsensehost.accounts.adunits.get", "path": "accounts/{accountId}/adclients/{adClientId}/adunits/{adUnitId}", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "adClientId": {"type": "string", "required": true, "location": "path"}, "adUnitId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "AdUnit"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "getAdCode": {"id": "adsensehost.accounts.adunits.getAdCode", "path": "accounts/{accountId}/adclients/{adClientId}/adunits/{adUnitId}/adcode", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "adClientId": {"type": "string", "required": true, "location": "path"}, "adUnitId": {"type": "string", "required": true, "location": "path"}, "hostCustomChannelId": {"type": "string", "repeated": true, "location": "query"}}, "response": {"$ref": "AdCode"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "insert": {"id": "adsensehost.accounts.adunits.insert", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "httpMethod": "POST", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "adClientId": {"type": "string", "required": true, "location": "path"}}, "request": {"$ref": "AdUnit"}, "response": {"$ref": "AdUnit"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "list": {"id": "adsensehost.accounts.adunits.list", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "adClientId": {"type": "string", "required": true, "location": "path"}, "includeInactive": {"type": "boolean", "location": "query"}, "maxResults": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "10000", "location": "query"}, "pageToken": {"type": "string", "location": "query"}}, "response": {"$ref": "AdUnits"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "patch": {"id": "adsensehost.accounts.adunits.patch", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "httpMethod": "PATCH", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "adClientId": {"type": "string", "required": true, "location": "path"}, "adUnitId": {"type": "string", "required": true, "location": "query"}}, "request": {"$ref": "AdUnit"}, "response": {"$ref": "AdUnit"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "update": {"id": "adsensehost.accounts.adunits.update", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "httpMethod": "PUT", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "adClientId": {"type": "string", "required": true, "location": "path"}}, "request": {"$ref": "AdUnit"}, "response": {"$ref": "AdUnit"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));
    $this->accounts_reports = new Google_AccountsReportsServiceResource($this, $this->serviceName, 'reports', json_decode('{"methods": {"generate": {"id": "adsensehost.accounts.reports.generate", "path": "accounts/{accountId}/reports", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "dimension": {"type": "string", "repeated": true, "location": "query"}, "endDate": {"type": "string", "required": true, "location": "query"}, "filter": {"type": "string", "repeated": true, "location": "query"}, "locale": {"type": "string", "location": "query"}, "maxResults": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "50000", "location": "query"}, "metric": {"type": "string", "repeated": true, "location": "query"}, "sort": {"type": "string", "repeated": true, "location": "query"}, "startDate": {"type": "string", "required": true, "location": "query"}, "startIndex": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "5000", "location": "query"}}, "response": {"$ref": "Report"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));
    $this->adclients = new Google_AdclientsServiceResource($this, $this->serviceName, 'adclients', json_decode('{"methods": {"get": {"id": "adsensehost.adclients.get", "path": "adclients/{adClientId}", "httpMethod": "GET", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "AdClient"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "list": {"id": "adsensehost.adclients.list", "path": "adclients", "httpMethod": "GET", "parameters": {"maxResults": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "10000", "location": "query"}, "pageToken": {"type": "string", "location": "query"}}, "response": {"$ref": "AdClients"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));
    $this->associationsessions = new Google_AssociationsessionsServiceResource($this, $this->serviceName, 'associationsessions', json_decode('{"methods": {"start": {"id": "adsensehost.associationsessions.start", "path": "associationsessions/start", "httpMethod": "GET", "parameters": {"productCode": {"type": "string", "required": true, "enum": ["AFC", "AFMC", "AFS"], "repeated": true, "location": "query"}, "userLocale": {"type": "string", "location": "query"}, "websiteLocale": {"type": "string", "location": "query"}, "websiteUrl": {"type": "string", "required": true, "location": "query"}}, "response": {"$ref": "AssociationSession"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "verify": {"id": "adsensehost.associationsessions.verify", "path": "associationsessions/verify", "httpMethod": "GET", "parameters": {"token": {"type": "string", "required": true, "location": "query"}}, "response": {"$ref": "AssociationSession"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));
    $this->customchannels = new Google_CustomchannelsServiceResource($this, $this->serviceName, 'customchannels', json_decode('{"methods": {"delete": {"id": "adsensehost.customchannels.delete", "path": "adclients/{adClientId}/customchannels/{customChannelId}", "httpMethod": "DELETE", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}, "customChannelId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "CustomChannel"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "get": {"id": "adsensehost.customchannels.get", "path": "adclients/{adClientId}/customchannels/{customChannelId}", "httpMethod": "GET", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}, "customChannelId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "CustomChannel"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "insert": {"id": "adsensehost.customchannels.insert", "path": "adclients/{adClientId}/customchannels", "httpMethod": "POST", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}}, "request": {"$ref": "CustomChannel"}, "response": {"$ref": "CustomChannel"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "list": {"id": "adsensehost.customchannels.list", "path": "adclients/{adClientId}/customchannels", "httpMethod": "GET", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}, "maxResults": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "10000", "location": "query"}, "pageToken": {"type": "string", "location": "query"}}, "response": {"$ref": "CustomChannels"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "patch": {"id": "adsensehost.customchannels.patch", "path": "adclients/{adClientId}/customchannels", "httpMethod": "PATCH", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}, "customChannelId": {"type": "string", "required": true, "location": "query"}}, "request": {"$ref": "CustomChannel"}, "response": {"$ref": "CustomChannel"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "update": {"id": "adsensehost.customchannels.update", "path": "adclients/{adClientId}/customchannels", "httpMethod": "PUT", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}}, "request": {"$ref": "CustomChannel"}, "response": {"$ref": "CustomChannel"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));
    $this->reports = new Google_ReportsServiceResource($this, $this->serviceName, 'reports', json_decode('{"methods": {"generate": {"id": "adsensehost.reports.generate", "path": "reports", "httpMethod": "GET", "parameters": {"dimension": {"type": "string", "repeated": true, "location": "query"}, "endDate": {"type": "string", "required": true, "location": "query"}, "filter": {"type": "string", "repeated": true, "location": "query"}, "locale": {"type": "string", "location": "query"}, "maxResults": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "50000", "location": "query"}, "metric": {"type": "string", "repeated": true, "location": "query"}, "sort": {"type": "string", "repeated": true, "location": "query"}, "startDate": {"type": "string", "required": true, "location": "query"}, "startIndex": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "5000", "location": "query"}}, "response": {"$ref": "Report"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));
    $this->urlchannels = new Google_UrlchannelsServiceResource($this, $this->serviceName, 'urlchannels', json_decode('{"methods": {"delete": {"id": "adsensehost.urlchannels.delete", "path": "adclients/{adClientId}/urlchannels/{urlChannelId}", "httpMethod": "DELETE", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}, "urlChannelId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "UrlChannel"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "insert": {"id": "adsensehost.urlchannels.insert", "path": "adclients/{adClientId}/urlchannels", "httpMethod": "POST", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}}, "request": {"$ref": "UrlChannel"}, "response": {"$ref": "UrlChannel"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}, "list": {"id": "adsensehost.urlchannels.list", "path": "adclients/{adClientId}/urlchannels", "httpMethod": "GET", "parameters": {"adClientId": {"type": "string", "required": true, "location": "path"}, "maxResults": {"type": "integer", "format": "uint32", "minimum": "0", "maximum": "10000", "location": "query"}, "pageToken": {"type": "string", "location": "query"}}, "response": {"$ref": "UrlChannels"}, "scopes": ["https://www.googleapis.com/auth/adsensehost"]}}}', true));

  }
}

class Google_Account extends Google_Model {
  public $id;
  public $kind;
  public $name;
  public $status;
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
}

class Google_Accounts extends Google_Model {
  public $etag;
  protected $__itemsType = 'Google_Account';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setItems(/* array(Google_Account) */ $items) {
    $this->assertIsArray($items, 'Google_Account', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_AdClient extends Google_Model {
  public $arcOptIn;
  public $id;
  public $kind;
  public $productCode;
  public $supportsReporting;
  public function setArcOptIn($arcOptIn) {
    $this->arcOptIn = $arcOptIn;
  }
  public function getArcOptIn() {
    return $this->arcOptIn;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setProductCode($productCode) {
    $this->productCode = $productCode;
  }
  public function getProductCode() {
    return $this->productCode;
  }
  public function setSupportsReporting($supportsReporting) {
    $this->supportsReporting = $supportsReporting;
  }
  public function getSupportsReporting() {
    return $this->supportsReporting;
  }
}

class Google_AdClients extends Google_Model {
  public $etag;
  protected $__itemsType = 'Google_AdClient';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $nextPageToken;
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setItems(/* array(Google_AdClient) */ $items) {
    $this->assertIsArray($items, 'Google_AdClient', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
}

class Google_AdCode extends Google_Model {
  public $adCode;
  public $kind;
  public function setAdCode($adCode) {
    $this->adCode = $adCode;
  }
  public function getAdCode() {
    return $this->adCode;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_AdStyle extends Google_Model {
  protected $__colorsType = 'Google_AdStyleColors';
  protected $__colorsDataType = '';
  public $colors;
  public $corners;
  protected $__fontType = 'Google_AdStyleFont';
  protected $__fontDataType = '';
  public $font;
  public $kind;
  public function setColors(Google_AdStyleColors $colors) {
    $this->colors = $colors;
  }
  public function getColors() {
    return $this->colors;
  }
  public function setCorners($corners) {
    $this->corners = $corners;
  }
  public function getCorners() {
    return $this->corners;
  }
  public function setFont(Google_AdStyleFont $font) {
    $this->font = $font;
  }
  public function getFont() {
    return $this->font;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_AdStyleColors extends Google_Model {
  public $background;
  public $border;
  public $text;
  public $title;
  public $url;
  public function setBackground($background) {
    $this->background = $background;
  }
  public function getBackground() {
    return $this->background;
  }
  public function setBorder($border) {
    $this->border = $border;
  }
  public function getBorder() {
    return $this->border;
  }
  public function setText($text) {
    $this->text = $text;
  }
  public function getText() {
    return $this->text;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class Google_AdStyleFont extends Google_Model {
  public $family;
  public $size;
  public function setFamily($family) {
    $this->family = $family;
  }
  public function getFamily() {
    return $this->family;
  }
  public function setSize($size) {
    $this->size = $size;
  }
  public function getSize() {
    return $this->size;
  }
}

class Google_AdUnit extends Google_Model {
  public $code;
  protected $__contentAdsSettingsType = 'Google_AdUnitContentAdsSettings';
  protected $__contentAdsSettingsDataType = '';
  public $contentAdsSettings;
  protected $__customStyleType = 'Google_AdStyle';
  protected $__customStyleDataType = '';
  public $customStyle;
  public $id;
  public $kind;
  protected $__mobileContentAdsSettingsType = 'Google_AdUnitMobileContentAdsSettings';
  protected $__mobileContentAdsSettingsDataType = '';
  public $mobileContentAdsSettings;
  public $name;
  public $status;
  public function setCode($code) {
    $this->code = $code;
  }
  public function getCode() {
    return $this->code;
  }
  public function setContentAdsSettings(Google_AdUnitContentAdsSettings $contentAdsSettings) {
    $this->contentAdsSettings = $contentAdsSettings;
  }
  public function getContentAdsSettings() {
    return $this->contentAdsSettings;
  }
  public function setCustomStyle(Google_AdStyle $customStyle) {
    $this->customStyle = $customStyle;
  }
  public function getCustomStyle() {
    return $this->customStyle;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setMobileContentAdsSettings(Google_AdUnitMobileContentAdsSettings $mobileContentAdsSettings) {
    $this->mobileContentAdsSettings = $mobileContentAdsSettings;
  }
  public function getMobileContentAdsSettings() {
    return $this->mobileContentAdsSettings;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
}

class Google_AdUnitContentAdsSettings extends Google_Model {
  protected $__backupOptionType = 'Google_AdUnitContentAdsSettingsBackupOption';
  protected $__backupOptionDataType = '';
  public $backupOption;
  public $size;
  public $type;
  public function setBackupOption(Google_AdUnitContentAdsSettingsBackupOption $backupOption) {
    $this->backupOption = $backupOption;
  }
  public function getBackupOption() {
    return $this->backupOption;
  }
  public function setSize($size) {
    $this->size = $size;
  }
  public function getSize() {
    return $this->size;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_AdUnitContentAdsSettingsBackupOption extends Google_Model {
  public $color;
  public $type;
  public $url;
  public function setColor($color) {
    $this->color = $color;
  }
  public function getColor() {
    return $this->color;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class Google_AdUnitMobileContentAdsSettings extends Google_Model {
  public $markupLanguage;
  public $scriptingLanguage;
  public $size;
  public $type;
  public function setMarkupLanguage($markupLanguage) {
    $this->markupLanguage = $markupLanguage;
  }
  public function getMarkupLanguage() {
    return $this->markupLanguage;
  }
  public function setScriptingLanguage($scriptingLanguage) {
    $this->scriptingLanguage = $scriptingLanguage;
  }
  public function getScriptingLanguage() {
    return $this->scriptingLanguage;
  }
  public function setSize($size) {
    $this->size = $size;
  }
  public function getSize() {
    return $this->size;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_AdUnits extends Google_Model {
  public $etag;
  protected $__itemsType = 'Google_AdUnit';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $nextPageToken;
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setItems(/* array(Google_AdUnit) */ $items) {
    $this->assertIsArray($items, 'Google_AdUnit', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
}

class Google_AssociationSession extends Google_Model {
  public $accountId;
  public $id;
  public $kind;
  public $productCodes;
  public $redirectUrl;
  public $status;
  public $userLocale;
  public $websiteLocale;
  public $websiteUrl;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setProductCodes($productCodes) {
    $this->productCodes = $productCodes;
  }
  public function getProductCodes() {
    return $this->productCodes;
  }
  public function setRedirectUrl($redirectUrl) {
    $this->redirectUrl = $redirectUrl;
  }
  public function getRedirectUrl() {
    return $this->redirectUrl;
  }
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setUserLocale($userLocale) {
    $this->userLocale = $userLocale;
  }
  public function getUserLocale() {
    return $this->userLocale;
  }
  public function setWebsiteLocale($websiteLocale) {
    $this->websiteLocale = $websiteLocale;
  }
  public function getWebsiteLocale() {
    return $this->websiteLocale;
  }
  public function setWebsiteUrl($websiteUrl) {
    $this->websiteUrl = $websiteUrl;
  }
  public function getWebsiteUrl() {
    return $this->websiteUrl;
  }
}

class Google_CustomChannel extends Google_Model {
  public $code;
  public $id;
  public $kind;
  public $name;
  public function setCode($code) {
    $this->code = $code;
  }
  public function getCode() {
    return $this->code;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_CustomChannels extends Google_Model {
  public $etag;
  protected $__itemsType = 'Google_CustomChannel';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $nextPageToken;
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setItems(/* array(Google_CustomChannel) */ $items) {
    $this->assertIsArray($items, 'Google_CustomChannel', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
}

class Google_Report extends Google_Model {
  public $averages;
  protected $__headersType = 'Google_ReportHeaders';
  protected $__headersDataType = 'array';
  public $headers;
  public $kind;
  public $rows;
  public $totalMatchedRows;
  public $totals;
  public $warnings;
  public function setAverages($averages) {
    $this->averages = $averages;
  }
  public function getAverages() {
    return $this->averages;
  }
  public function setHeaders(/* array(Google_ReportHeaders) */ $headers) {
    $this->assertIsArray($headers, 'Google_ReportHeaders', __METHOD__);
    $this->headers = $headers;
  }
  public function getHeaders() {
    return $this->headers;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRows($rows) {
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setTotalMatchedRows($totalMatchedRows) {
    $this->totalMatchedRows = $totalMatchedRows;
  }
  public function getTotalMatchedRows() {
    return $this->totalMatchedRows;
  }
  public function setTotals($totals) {
    $this->totals = $totals;
  }
  public function getTotals() {
    return $this->totals;
  }
  public function setWarnings($warnings) {
    $this->warnings = $warnings;
  }
  public function getWarnings() {
    return $this->warnings;
  }
}

class Google_ReportHeaders extends Google_Model {
  public $currency;
  public $name;
  public $type;
  public function setCurrency($currency) {
    $this->currency = $currency;
  }
  public function getCurrency() {
    return $this->currency;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_UrlChannel extends Google_Model {
  public $id;
  public $kind;
  public $urlPattern;
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setUrlPattern($urlPattern) {
    $this->urlPattern = $urlPattern;
  }
  public function getUrlPattern() {
    return $this->urlPattern;
  }
}

class Google_UrlChannels extends Google_Model {
  public $etag;
  protected $__itemsType = 'Google_UrlChannel';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $nextPageToken;
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setItems(/* array(Google_UrlChannel) */ $items) {
    $this->assertIsArray($items, 'Google_UrlChannel', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
}
