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
   * The "data" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $data = $analyticsService->data;
   *  </code>
   */
  class Google_DataServiceResource extends Google_ServiceResource {


  }

  /**
   * The "ga" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $ga = $analyticsService->ga;
   *  </code>
   */
  class Google_DataGaServiceResource extends Google_ServiceResource {


    /**
     * Returns Analytics data for a profile. (ga.get)
     *
     * @param string $ids Unique table ID for retrieving Analytics data. Table ID is of the form ga:XXXX, where XXXX is the Analytics profile ID.
     * @param string $start_date Start date for fetching Analytics data. All requests should specify a start date formatted as YYYY-MM-DD.
     * @param string $end_date End date for fetching Analytics data. All requests should specify an end date formatted as YYYY-MM-DD.
     * @param string $metrics A comma-separated list of Analytics metrics. E.g., 'ga:visits,ga:pageviews'. At least one metric must be specified.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string dimensions A comma-separated list of Analytics dimensions. E.g., 'ga:browser,ga:city'.
     * @opt_param string filters A comma-separated list of dimension or metric filters to be applied to Analytics data.
     * @opt_param int max-results The maximum number of entries to include in this feed.
     * @opt_param string segment An Analytics advanced segment to be applied to data.
     * @opt_param string sort A comma-separated list of dimensions or metrics that determine the sort order for Analytics data.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_GaData
     */
    public function get($ids, $start_date, $end_date, $metrics, $optParams = array()) {
      $params = array('ids' => $ids, 'start-date' => $start_date, 'end-date' => $end_date, 'metrics' => $metrics);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_GaData($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "mcf" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $mcf = $analyticsService->mcf;
   *  </code>
   */
  class Google_DataMcfServiceResource extends Google_ServiceResource {


    /**
     * Returns Analytics Multi-Channel Funnels data for a profile. (mcf.get)
     *
     * @param string $ids Unique table ID for retrieving Analytics data. Table ID is of the form ga:XXXX, where XXXX is the Analytics profile ID.
     * @param string $start_date Start date for fetching Analytics data. All requests should specify a start date formatted as YYYY-MM-DD.
     * @param string $end_date End date for fetching Analytics data. All requests should specify an end date formatted as YYYY-MM-DD.
     * @param string $metrics A comma-separated list of Multi-Channel Funnels metrics. E.g., 'mcf:totalConversions,mcf:totalConversionValue'. At least one metric must be specified.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string dimensions A comma-separated list of Multi-Channel Funnels dimensions. E.g., 'mcf:source,mcf:medium'.
     * @opt_param string filters A comma-separated list of dimension or metric filters to be applied to the Analytics data.
     * @opt_param int max-results The maximum number of entries to include in this feed.
     * @opt_param string sort A comma-separated list of dimensions or metrics that determine the sort order for the Analytics data.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_McfData
     */
    public function get($ids, $start_date, $end_date, $metrics, $optParams = array()) {
      $params = array('ids' => $ids, 'start-date' => $start_date, 'end-date' => $end_date, 'metrics' => $metrics);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_McfData($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "management" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $management = $analyticsService->management;
   *  </code>
   */
  class Google_ManagementServiceResource extends Google_ServiceResource {


  }

  /**
   * The "accounts" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $accounts = $analyticsService->accounts;
   *  </code>
   */
  class Google_ManagementAccountsServiceResource extends Google_ServiceResource {


    /**
     * Lists all accounts to which the user has access. (accounts.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of accounts to include in this response.
     * @opt_param int start-index An index of the first account to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_Accounts
     */
    public function listManagementAccounts($optParams = array()) {
      $params = array();
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
   * The "customDataSources" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $customDataSources = $analyticsService->customDataSources;
   *  </code>
   */
  class Google_ManagementCustomDataSourcesServiceResource extends Google_ServiceResource {


    /**
     * List custom data sources to which the user has access. (customDataSources.list)
     *
     * @param string $accountId Account Id for the custom data sources to retrieve.
     * @param string $webPropertyId Web property Id for the custom data sources to retrieve.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of custom data sources to include in this response.
     * @opt_param int start-index A 1-based index of the first custom data source to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_CustomDataSources
     */
    public function listManagementCustomDataSources($accountId, $webPropertyId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_CustomDataSources($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "dailyUploads" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $dailyUploads = $analyticsService->dailyUploads;
   *  </code>
   */
  class Google_ManagementDailyUploadsServiceResource extends Google_ServiceResource {


    /**
     * Delete uploaded data for the given date. (dailyUploads.delete)
     *
     * @param string $accountId Account Id associated with daily upload delete.
     * @param string $webPropertyId Web property Id associated with daily upload delete.
     * @param string $customDataSourceId Custom data source Id associated with daily upload delete.
     * @param string $date Date for which data is to be deleted. Date should be formatted as YYYY-MM-DD.
     * @param string $type Type of data for this delete.
     * @param array $optParams Optional parameters.
     */
    public function delete($accountId, $webPropertyId, $customDataSourceId, $date, $type, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'customDataSourceId' => $customDataSourceId, 'date' => $date, 'type' => $type);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
    /**
     * List daily uploads to which the user has access. (dailyUploads.list)
     *
     * @param string $accountId Account Id for the daily uploads to retrieve.
     * @param string $webPropertyId Web property Id for the daily uploads to retrieve.
     * @param string $customDataSourceId Custom data source Id for daily uploads to retrieve.
     * @param string $start_date Start date of the form YYYY-MM-DD.
     * @param string $end_date End date of the form YYYY-MM-DD.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of custom data sources to include in this response.
     * @opt_param int start-index A 1-based index of the first daily upload to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_DailyUploads
     */
    public function listManagementDailyUploads($accountId, $webPropertyId, $customDataSourceId, $start_date, $end_date, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'customDataSourceId' => $customDataSourceId, 'start-date' => $start_date, 'end-date' => $end_date);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_DailyUploads($data);
      } else {
        return $data;
      }
    }
    /**
     * Update/Overwrite data for a custom data source. (dailyUploads.upload)
     *
     * @param string $accountId Account Id associated with daily upload.
     * @param string $webPropertyId Web property Id associated with daily upload.
     * @param string $customDataSourceId Custom data source Id to which the data being uploaded belongs.
     * @param string $date Date for which data is uploaded. Date should be formatted as YYYY-MM-DD.
     * @param int $appendNumber Append number for this upload indexed from 1.
     * @param string $type Type of data for this upload.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool reset Reset/Overwrite all previous appends for this date and start over with this file as the first upload.
     * @return Google_DailyUploadAppend
     */
    public function upload($accountId, $webPropertyId, $customDataSourceId, $date, $appendNumber, $type, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'customDataSourceId' => $customDataSourceId, 'date' => $date, 'appendNumber' => $appendNumber, 'type' => $type);
      $params = array_merge($params, $optParams);
      $data = $this->__call('upload', array($params));
      if ($this->useObjects()) {
        return new Google_DailyUploadAppend($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "experiments" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $experiments = $analyticsService->experiments;
   *  </code>
   */
  class Google_ManagementExperimentsServiceResource extends Google_ServiceResource {


    /**
     * Delete an experiment. (experiments.delete)
     *
     * @param string $accountId Account ID to which the experiment belongs
     * @param string $webPropertyId Web property ID to which the experiment belongs
     * @param string $profileId Profile ID to which the experiment belongs
     * @param string $experimentId ID of the experiment to delete
     * @param array $optParams Optional parameters.
     */
    public function delete($accountId, $webPropertyId, $profileId, $experimentId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'profileId' => $profileId, 'experimentId' => $experimentId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
    /**
     * Returns an experiment to which the user has access. (experiments.get)
     *
     * @param string $accountId Account ID to retrieve the experiment for.
     * @param string $webPropertyId Web property ID to retrieve the experiment for.
     * @param string $profileId Profile ID to retrieve the experiment for.
     * @param string $experimentId Experiment ID to retrieve the experiment for.
     * @param array $optParams Optional parameters.
     * @return Google_Experiment
     */
    public function get($accountId, $webPropertyId, $profileId, $experimentId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'profileId' => $profileId, 'experimentId' => $experimentId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Experiment($data);
      } else {
        return $data;
      }
    }
    /**
     * Create a new experiment. (experiments.insert)
     *
     * @param string $accountId Account ID to create the experiment for.
     * @param string $webPropertyId Web property ID to create the experiment for.
     * @param string $profileId Profile ID to create the experiment for.
     * @param Google_Experiment $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Experiment
     */
    public function insert($accountId, $webPropertyId, $profileId, Google_Experiment $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'profileId' => $profileId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Experiment($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists experiments to which the user has access. (experiments.list)
     *
     * @param string $accountId Account ID to retrieve experiments for.
     * @param string $webPropertyId Web property ID to retrieve experiments for.
     * @param string $profileId Profile ID to retrieve experiments for.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of experiments to include in this response.
     * @opt_param int start-index An index of the first experiment to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_Experiments
     */
    public function listManagementExperiments($accountId, $webPropertyId, $profileId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'profileId' => $profileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Experiments($data);
      } else {
        return $data;
      }
    }
    /**
     * Update an existing experiment. This method supports patch semantics. (experiments.patch)
     *
     * @param string $accountId Account ID of the experiment to update.
     * @param string $webPropertyId Web property ID of the experiment to update.
     * @param string $profileId Profile ID of the experiment to update.
     * @param string $experimentId Experiment ID of the experiment to update.
     * @param Google_Experiment $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Experiment
     */
    public function patch($accountId, $webPropertyId, $profileId, $experimentId, Google_Experiment $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'profileId' => $profileId, 'experimentId' => $experimentId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Experiment($data);
      } else {
        return $data;
      }
    }
    /**
     * Update an existing experiment. (experiments.update)
     *
     * @param string $accountId Account ID of the experiment to update.
     * @param string $webPropertyId Web property ID of the experiment to update.
     * @param string $profileId Profile ID of the experiment to update.
     * @param string $experimentId Experiment ID of the experiment to update.
     * @param Google_Experiment $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Experiment
     */
    public function update($accountId, $webPropertyId, $profileId, $experimentId, Google_Experiment $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'profileId' => $profileId, 'experimentId' => $experimentId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Experiment($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "goals" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $goals = $analyticsService->goals;
   *  </code>
   */
  class Google_ManagementGoalsServiceResource extends Google_ServiceResource {


    /**
     * Lists goals to which the user has access. (goals.list)
     *
     * @param string $accountId Account ID to retrieve goals for. Can either be a specific account ID or '~all', which refers to all the accounts that user has access to.
     * @param string $webPropertyId Web property ID to retrieve goals for. Can either be a specific web property ID or '~all', which refers to all the web properties that user has access to.
     * @param string $profileId Profile ID to retrieve goals for. Can either be a specific profile ID or '~all', which refers to all the profiles that user has access to.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of goals to include in this response.
     * @opt_param int start-index An index of the first goal to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_Goals
     */
    public function listManagementGoals($accountId, $webPropertyId, $profileId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'profileId' => $profileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Goals($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "profiles" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $profiles = $analyticsService->profiles;
   *  </code>
   */
  class Google_ManagementProfilesServiceResource extends Google_ServiceResource {


    /**
     * Lists profiles to which the user has access. (profiles.list)
     *
     * @param string $accountId Account ID for the profiles to retrieve. Can either be a specific account ID or '~all', which refers to all the accounts to which the user has access.
     * @param string $webPropertyId Web property ID for the profiles to retrieve. Can either be a specific web property ID or '~all', which refers to all the web properties to which the user has access.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of profiles to include in this response.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_Profiles
     */
    public function listManagementProfiles($accountId, $webPropertyId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Profiles($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "segments" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $segments = $analyticsService->segments;
   *  </code>
   */
  class Google_ManagementSegmentsServiceResource extends Google_ServiceResource {


    /**
     * Lists advanced segments to which the user has access. (segments.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of advanced segments to include in this response.
     * @opt_param int start-index An index of the first advanced segment to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_Segments
     */
    public function listManagementSegments($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Segments($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "webproperties" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new Google_AnalyticsService(...);
   *   $webproperties = $analyticsService->webproperties;
   *  </code>
   */
  class Google_ManagementWebpropertiesServiceResource extends Google_ServiceResource {


    /**
     * Lists web properties to which the user has access. (webproperties.list)
     *
     * @param string $accountId Account ID to retrieve web properties for. Can either be a specific account ID or '~all', which refers to all the accounts that user has access to.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of web properties to include in this response.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return Google_Webproperties
     */
    public function listManagementWebproperties($accountId, $optParams = array()) {
      $params = array('accountId' => $accountId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Webproperties($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Analytics (v3).
 *
 * <p>
 * View and manage your Google Analytics data
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/analytics/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_AnalyticsService extends Google_Service {
  public $data_ga;
  public $data_mcf;
  public $management_accounts;
  public $management_customDataSources;
  public $management_dailyUploads;
  public $management_experiments;
  public $management_goals;
  public $management_profiles;
  public $management_segments;
  public $management_webproperties;
  /**
   * Constructs the internal representation of the Analytics service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'analytics/v3/';
    $this->version = 'v3';
    $this->serviceName = 'analytics';

    $client->addService($this->serviceName, $this->version);
    $this->data_ga = new Google_DataGaServiceResource($this, $this->serviceName, 'ga', json_decode('{"methods": {"get": {"id": "analytics.data.ga.get", "path": "data/ga", "httpMethod": "GET", "parameters": {"dimensions": {"type": "string", "location": "query"}, "end-date": {"type": "string", "required": true, "location": "query"}, "filters": {"type": "string", "location": "query"}, "ids": {"type": "string", "required": true, "location": "query"}, "max-results": {"type": "integer", "format": "int32", "location": "query"}, "metrics": {"type": "string", "required": true, "location": "query"}, "segment": {"type": "string", "location": "query"}, "sort": {"type": "string", "location": "query"}, "start-date": {"type": "string", "required": true, "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}}, "response": {"$ref": "GaData"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}}}', true));
    $this->data_mcf = new Google_DataMcfServiceResource($this, $this->serviceName, 'mcf', json_decode('{"methods": {"get": {"id": "analytics.data.mcf.get", "path": "data/mcf", "httpMethod": "GET", "parameters": {"dimensions": {"type": "string", "location": "query"}, "end-date": {"type": "string", "required": true, "location": "query"}, "filters": {"type": "string", "location": "query"}, "ids": {"type": "string", "required": true, "location": "query"}, "max-results": {"type": "integer", "format": "int32", "location": "query"}, "metrics": {"type": "string", "required": true, "location": "query"}, "sort": {"type": "string", "location": "query"}, "start-date": {"type": "string", "required": true, "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}}, "response": {"$ref": "McfData"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}}}', true));
    $this->management_accounts = new Google_ManagementAccountsServiceResource($this, $this->serviceName, 'accounts', json_decode('{"methods": {"list": {"id": "analytics.management.accounts.list", "path": "management/accounts", "httpMethod": "GET", "parameters": {"max-results": {"type": "integer", "format": "int32", "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}}, "response": {"$ref": "Accounts"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}}}', true));
    $this->management_customDataSources = new Google_ManagementCustomDataSourcesServiceResource($this, $this->serviceName, 'customDataSources', json_decode('{"methods": {"list": {"id": "analytics.management.customDataSources.list", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "max-results": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "CustomDataSources"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}}}', true));
    $this->management_dailyUploads = new Google_ManagementDailyUploadsServiceResource($this, $this->serviceName, 'dailyUploads', json_decode('{"methods": {"delete": {"id": "analytics.management.dailyUploads.delete", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads/{date}", "httpMethod": "DELETE", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "customDataSourceId": {"type": "string", "required": true, "location": "path"}, "date": {"type": "string", "required": true, "location": "path"}, "type": {"type": "string", "required": true, "enum": ["cost"], "location": "query"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "scopes": ["https://www.googleapis.com/auth/analytics"]}, "list": {"id": "analytics.management.dailyUploads.list", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "customDataSourceId": {"type": "string", "required": true, "location": "path"}, "end-date": {"type": "string", "required": true, "location": "query"}, "max-results": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}, "start-date": {"type": "string", "required": true, "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "DailyUploads"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}, "upload": {"id": "analytics.management.dailyUploads.upload", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads/{date}/uploads", "httpMethod": "POST", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "appendNumber": {"type": "integer", "required": true, "format": "int32", "minimum": "1", "maximum": "20", "location": "query"}, "customDataSourceId": {"type": "string", "required": true, "location": "path"}, "date": {"type": "string", "required": true, "location": "path"}, "reset": {"type": "boolean", "default": "false", "location": "query"}, "type": {"type": "string", "required": true, "enum": ["cost"], "location": "query"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "DailyUploadAppend"}, "scopes": ["https://www.googleapis.com/auth/analytics"], "supportsMediaUpload": true, "mediaUpload": {"accept": ["application/octet-stream"], "maxSize": "5MB", "protocols": {"simple": {"multipart": true, "path": "/upload/analytics/v3/management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads/{date}/uploads"}, "resumable": {"multipart": true, "path": "/resumable/upload/analytics/v3/management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads/{date}/uploads"}}}}}}', true));
    $this->management_experiments = new Google_ManagementExperimentsServiceResource($this, $this->serviceName, 'experiments', json_decode('{"methods": {"delete": {"id": "analytics.management.experiments.delete", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles/{profileId}/experiments/{experimentId}", "httpMethod": "DELETE", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "experimentId": {"type": "string", "required": true, "location": "path"}, "profileId": {"type": "string", "required": true, "location": "path"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "scopes": ["https://www.googleapis.com/auth/analytics"]}, "get": {"id": "analytics.management.experiments.get", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles/{profileId}/experiments/{experimentId}", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "experimentId": {"type": "string", "required": true, "location": "path"}, "profileId": {"type": "string", "required": true, "location": "path"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "Experiment"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}, "insert": {"id": "analytics.management.experiments.insert", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles/{profileId}/experiments", "httpMethod": "POST", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "profileId": {"type": "string", "required": true, "location": "path"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "request": {"$ref": "Experiment"}, "response": {"$ref": "Experiment"}, "scopes": ["https://www.googleapis.com/auth/analytics"]}, "list": {"id": "analytics.management.experiments.list", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles/{profileId}/experiments", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "max-results": {"type": "integer", "format": "int32", "location": "query"}, "profileId": {"type": "string", "required": true, "location": "path"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "Experiments"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}, "patch": {"id": "analytics.management.experiments.patch", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles/{profileId}/experiments/{experimentId}", "httpMethod": "PATCH", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "experimentId": {"type": "string", "required": true, "location": "path"}, "profileId": {"type": "string", "required": true, "location": "path"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "request": {"$ref": "Experiment"}, "response": {"$ref": "Experiment"}, "scopes": ["https://www.googleapis.com/auth/analytics"]}, "update": {"id": "analytics.management.experiments.update", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles/{profileId}/experiments/{experimentId}", "httpMethod": "PUT", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "experimentId": {"type": "string", "required": true, "location": "path"}, "profileId": {"type": "string", "required": true, "location": "path"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "request": {"$ref": "Experiment"}, "response": {"$ref": "Experiment"}, "scopes": ["https://www.googleapis.com/auth/analytics"]}}}', true));
    $this->management_goals = new Google_ManagementGoalsServiceResource($this, $this->serviceName, 'goals', json_decode('{"methods": {"list": {"id": "analytics.management.goals.list", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles/{profileId}/goals", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "max-results": {"type": "integer", "format": "int32", "location": "query"}, "profileId": {"type": "string", "required": true, "location": "path"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "Goals"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}}}', true));
    $this->management_profiles = new Google_ManagementProfilesServiceResource($this, $this->serviceName, 'profiles', json_decode('{"methods": {"list": {"id": "analytics.management.profiles.list", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "max-results": {"type": "integer", "format": "int32", "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}, "webPropertyId": {"type": "string", "required": true, "location": "path"}}, "response": {"$ref": "Profiles"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}}}', true));
    $this->management_segments = new Google_ManagementSegmentsServiceResource($this, $this->serviceName, 'segments', json_decode('{"methods": {"list": {"id": "analytics.management.segments.list", "path": "management/segments", "httpMethod": "GET", "parameters": {"max-results": {"type": "integer", "format": "int32", "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}}, "response": {"$ref": "Segments"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}}}', true));
    $this->management_webproperties = new Google_ManagementWebpropertiesServiceResource($this, $this->serviceName, 'webproperties', json_decode('{"methods": {"list": {"id": "analytics.management.webproperties.list", "path": "management/accounts/{accountId}/webproperties", "httpMethod": "GET", "parameters": {"accountId": {"type": "string", "required": true, "location": "path"}, "max-results": {"type": "integer", "format": "int32", "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}}, "response": {"$ref": "Webproperties"}, "scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"]}}}', true));

  }
}



class Google_Account extends Google_Model {
  protected $__childLinkType = 'Google_AccountChildLink';
  protected $__childLinkDataType = '';
  public $childLink;
  public $created;
  public $id;
  public $kind;
  public $name;
  public $selfLink;
  public $updated;
  public function setChildLink(Google_AccountChildLink $childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
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
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
}

class Google_AccountChildLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_Accounts extends Google_Model {
  protected $__itemsType = 'Google_Account';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  public $startIndex;
  public $totalResults;
  public $username;
  public function setItems(/* array(Google_Account) */ $items) {
    $this->assertIsArray($items, 'Google_Account', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
}

class Google_CustomDataSource extends Google_Model {
  public $accountId;
  protected $__childLinkType = 'Google_CustomDataSourceChildLink';
  protected $__childLinkDataType = '';
  public $childLink;
  public $created;
  public $description;
  public $id;
  public $kind;
  public $name;
  protected $__parentLinkType = 'Google_CustomDataSourceParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $profilesLinked;
  public $selfLink;
  public $updated;
  public $webPropertyId;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setChildLink(Google_CustomDataSourceChildLink $childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
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
  public function setParentLink(Google_CustomDataSourceParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
  public function setProfilesLinked(/* array(Google_string) */ $profilesLinked) {
    $this->assertIsArray($profilesLinked, 'Google_string', __METHOD__);
    $this->profilesLinked = $profilesLinked;
  }
  public function getProfilesLinked() {
    return $this->profilesLinked;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
}

class Google_CustomDataSourceChildLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_CustomDataSourceParentLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_CustomDataSources extends Google_Model {
  protected $__itemsType = 'Google_CustomDataSource';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  public $startIndex;
  public $totalResults;
  public $username;
  public function setItems(/* array(Google_CustomDataSource) */ $items) {
    $this->assertIsArray($items, 'Google_CustomDataSource', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
}

class Google_DailyUpload extends Google_Model {
  public $accountId;
  public $appendCount;
  public $createdTime;
  public $customDataSourceId;
  public $date;
  public $kind;
  public $modifiedTime;
  protected $__parentLinkType = 'Google_DailyUploadParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  protected $__recentChangesType = 'Google_DailyUploadRecentChanges';
  protected $__recentChangesDataType = 'array';
  public $recentChanges;
  public $selfLink;
  public $webPropertyId;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setAppendCount($appendCount) {
    $this->appendCount = $appendCount;
  }
  public function getAppendCount() {
    return $this->appendCount;
  }
  public function setCreatedTime($createdTime) {
    $this->createdTime = $createdTime;
  }
  public function getCreatedTime() {
    return $this->createdTime;
  }
  public function setCustomDataSourceId($customDataSourceId) {
    $this->customDataSourceId = $customDataSourceId;
  }
  public function getCustomDataSourceId() {
    return $this->customDataSourceId;
  }
  public function setDate($date) {
    $this->date = $date;
  }
  public function getDate() {
    return $this->date;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setModifiedTime($modifiedTime) {
    $this->modifiedTime = $modifiedTime;
  }
  public function getModifiedTime() {
    return $this->modifiedTime;
  }
  public function setParentLink(Google_DailyUploadParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
  public function setRecentChanges(/* array(Google_DailyUploadRecentChanges) */ $recentChanges) {
    $this->assertIsArray($recentChanges, 'Google_DailyUploadRecentChanges', __METHOD__);
    $this->recentChanges = $recentChanges;
  }
  public function getRecentChanges() {
    return $this->recentChanges;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
}

class Google_DailyUploadAppend extends Google_Model {
  public $accountId;
  public $appendNumber;
  public $customDataSourceId;
  public $date;
  public $kind;
  public $nextAppendLink;
  public $webPropertyId;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setAppendNumber($appendNumber) {
    $this->appendNumber = $appendNumber;
  }
  public function getAppendNumber() {
    return $this->appendNumber;
  }
  public function setCustomDataSourceId($customDataSourceId) {
    $this->customDataSourceId = $customDataSourceId;
  }
  public function getCustomDataSourceId() {
    return $this->customDataSourceId;
  }
  public function setDate($date) {
    $this->date = $date;
  }
  public function getDate() {
    return $this->date;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextAppendLink($nextAppendLink) {
    $this->nextAppendLink = $nextAppendLink;
  }
  public function getNextAppendLink() {
    return $this->nextAppendLink;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
}

class Google_DailyUploadParentLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_DailyUploadRecentChanges extends Google_Model {
  public $change;
  public $time;
  public function setChange($change) {
    $this->change = $change;
  }
  public function getChange() {
    return $this->change;
  }
  public function setTime($time) {
    $this->time = $time;
  }
  public function getTime() {
    return $this->time;
  }
}

class Google_DailyUploads extends Google_Model {
  protected $__itemsType = 'Google_DailyUpload';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  public $startIndex;
  public $totalResults;
  public $username;
  public function setItems(/* array(Google_DailyUpload) */ $items) {
    $this->assertIsArray($items, 'Google_DailyUpload', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
}

class Google_Experiment extends Google_Model {
  public $accountId;
  public $created;
  public $description;
  public $editableInGaUi;
  public $endTime;
  public $id;
  public $internalWebPropertyId;
  public $kind;
  public $minimumExperimentLengthInDays;
  public $name;
  public $objectiveMetric;
  public $optimizationType;
  protected $__parentLinkType = 'Google_ExperimentParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $profileId;
  public $reasonExperimentEnded;
  public $rewriteVariationUrlsAsOriginal;
  public $selfLink;
  public $servingFramework;
  public $snippet;
  public $startTime;
  public $status;
  public $trafficCoverage;
  public $updated;
  protected $__variationsType = 'Google_ExperimentVariations';
  protected $__variationsDataType = 'array';
  public $variations;
  public $webPropertyId;
  public $winnerConfidenceLevel;
  public $winnerFound;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setEditableInGaUi($editableInGaUi) {
    $this->editableInGaUi = $editableInGaUi;
  }
  public function getEditableInGaUi() {
    return $this->editableInGaUi;
  }
  public function setEndTime($endTime) {
    $this->endTime = $endTime;
  }
  public function getEndTime() {
    return $this->endTime;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setMinimumExperimentLengthInDays($minimumExperimentLengthInDays) {
    $this->minimumExperimentLengthInDays = $minimumExperimentLengthInDays;
  }
  public function getMinimumExperimentLengthInDays() {
    return $this->minimumExperimentLengthInDays;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setObjectiveMetric($objectiveMetric) {
    $this->objectiveMetric = $objectiveMetric;
  }
  public function getObjectiveMetric() {
    return $this->objectiveMetric;
  }
  public function setOptimizationType($optimizationType) {
    $this->optimizationType = $optimizationType;
  }
  public function getOptimizationType() {
    return $this->optimizationType;
  }
  public function setParentLink(Google_ExperimentParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
  public function setProfileId($profileId) {
    $this->profileId = $profileId;
  }
  public function getProfileId() {
    return $this->profileId;
  }
  public function setReasonExperimentEnded($reasonExperimentEnded) {
    $this->reasonExperimentEnded = $reasonExperimentEnded;
  }
  public function getReasonExperimentEnded() {
    return $this->reasonExperimentEnded;
  }
  public function setRewriteVariationUrlsAsOriginal($rewriteVariationUrlsAsOriginal) {
    $this->rewriteVariationUrlsAsOriginal = $rewriteVariationUrlsAsOriginal;
  }
  public function getRewriteVariationUrlsAsOriginal() {
    return $this->rewriteVariationUrlsAsOriginal;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setServingFramework($servingFramework) {
    $this->servingFramework = $servingFramework;
  }
  public function getServingFramework() {
    return $this->servingFramework;
  }
  public function setSnippet($snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setStartTime($startTime) {
    $this->startTime = $startTime;
  }
  public function getStartTime() {
    return $this->startTime;
  }
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setTrafficCoverage($trafficCoverage) {
    $this->trafficCoverage = $trafficCoverage;
  }
  public function getTrafficCoverage() {
    return $this->trafficCoverage;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setVariations(/* array(Google_ExperimentVariations) */ $variations) {
    $this->assertIsArray($variations, 'Google_ExperimentVariations', __METHOD__);
    $this->variations = $variations;
  }
  public function getVariations() {
    return $this->variations;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setWinnerConfidenceLevel($winnerConfidenceLevel) {
    $this->winnerConfidenceLevel = $winnerConfidenceLevel;
  }
  public function getWinnerConfidenceLevel() {
    return $this->winnerConfidenceLevel;
  }
  public function setWinnerFound($winnerFound) {
    $this->winnerFound = $winnerFound;
  }
  public function getWinnerFound() {
    return $this->winnerFound;
  }
}

class Google_ExperimentParentLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_ExperimentVariations extends Google_Model {
  public $name;
  public $status;
  public $url;
  public $weight;
  public $won;
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
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setWeight($weight) {
    $this->weight = $weight;
  }
  public function getWeight() {
    return $this->weight;
  }
  public function setWon($won) {
    $this->won = $won;
  }
  public function getWon() {
    return $this->won;
  }
}

class Google_Experiments extends Google_Model {
  protected $__itemsType = 'Google_Experiment';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  public $startIndex;
  public $totalResults;
  public $username;
  public function setItems(/* array(Google_Experiment) */ $items) {
    $this->assertIsArray($items, 'Google_Experiment', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
}

class Google_GaData extends Google_Model {
  protected $__columnHeadersType = 'Google_GaDataColumnHeaders';
  protected $__columnHeadersDataType = 'array';
  public $columnHeaders;
  public $containsSampledData;
  public $id;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  protected $__profileInfoType = 'Google_GaDataProfileInfo';
  protected $__profileInfoDataType = '';
  public $profileInfo;
  protected $__queryType = 'Google_GaDataQuery';
  protected $__queryDataType = '';
  public $query;
  public $rows;
  public $selfLink;
  public $totalResults;
  public $totalsForAllResults;
  public function setColumnHeaders(/* array(Google_GaDataColumnHeaders) */ $columnHeaders) {
    $this->assertIsArray($columnHeaders, 'Google_GaDataColumnHeaders', __METHOD__);
    $this->columnHeaders = $columnHeaders;
  }
  public function getColumnHeaders() {
    return $this->columnHeaders;
  }
  public function setContainsSampledData($containsSampledData) {
    $this->containsSampledData = $containsSampledData;
  }
  public function getContainsSampledData() {
    return $this->containsSampledData;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setProfileInfo(Google_GaDataProfileInfo $profileInfo) {
    $this->profileInfo = $profileInfo;
  }
  public function getProfileInfo() {
    return $this->profileInfo;
  }
  public function setQuery(Google_GaDataQuery $query) {
    $this->query = $query;
  }
  public function getQuery() {
    return $this->query;
  }
  public function setRows(/* array(Google_string) */ $rows) {
    $this->assertIsArray($rows, 'Google_string', __METHOD__);
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setTotalsForAllResults($totalsForAllResults) {
    $this->totalsForAllResults = $totalsForAllResults;
  }
  public function getTotalsForAllResults() {
    return $this->totalsForAllResults;
  }
}

class Google_GaDataColumnHeaders extends Google_Model {
  public $columnType;
  public $dataType;
  public $name;
  public function setColumnType($columnType) {
    $this->columnType = $columnType;
  }
  public function getColumnType() {
    return $this->columnType;
  }
  public function setDataType($dataType) {
    $this->dataType = $dataType;
  }
  public function getDataType() {
    return $this->dataType;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_GaDataProfileInfo extends Google_Model {
  public $accountId;
  public $internalWebPropertyId;
  public $profileId;
  public $profileName;
  public $tableId;
  public $webPropertyId;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
  }
  public function setProfileId($profileId) {
    $this->profileId = $profileId;
  }
  public function getProfileId() {
    return $this->profileId;
  }
  public function setProfileName($profileName) {
    $this->profileName = $profileName;
  }
  public function getProfileName() {
    return $this->profileName;
  }
  public function setTableId($tableId) {
    $this->tableId = $tableId;
  }
  public function getTableId() {
    return $this->tableId;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
}

class Google_GaDataQuery extends Google_Model {
  public $dimensions;
  public $end_date;
  public $filters;
  public $ids;
  public $max_results;
  public $metrics;
  public $segment;
  public $sort;
  public $start_date;
  public $start_index;
  public function setDimensions($dimensions) {
    $this->dimensions = $dimensions;
  }
  public function getDimensions() {
    return $this->dimensions;
  }
  public function setEnd_date($end_date) {
    $this->end_date = $end_date;
  }
  public function getEnd_date() {
    return $this->end_date;
  }
  public function setFilters($filters) {
    $this->filters = $filters;
  }
  public function getFilters() {
    return $this->filters;
  }
  public function setIds($ids) {
    $this->ids = $ids;
  }
  public function getIds() {
    return $this->ids;
  }
  public function setMax_results($max_results) {
    $this->max_results = $max_results;
  }
  public function getMax_results() {
    return $this->max_results;
  }
  public function setMetrics(/* array(Google_string) */ $metrics) {
    $this->assertIsArray($metrics, 'Google_string', __METHOD__);
    $this->metrics = $metrics;
  }
  public function getMetrics() {
    return $this->metrics;
  }
  public function setSegment($segment) {
    $this->segment = $segment;
  }
  public function getSegment() {
    return $this->segment;
  }
  public function setSort(/* array(Google_string) */ $sort) {
    $this->assertIsArray($sort, 'Google_string', __METHOD__);
    $this->sort = $sort;
  }
  public function getSort() {
    return $this->sort;
  }
  public function setStart_date($start_date) {
    $this->start_date = $start_date;
  }
  public function getStart_date() {
    return $this->start_date;
  }
  public function setStart_index($start_index) {
    $this->start_index = $start_index;
  }
  public function getStart_index() {
    return $this->start_index;
  }
}

class Google_Goal extends Google_Model {
  public $accountId;
  public $active;
  public $created;
  protected $__eventDetailsType = 'Google_GoalEventDetails';
  protected $__eventDetailsDataType = '';
  public $eventDetails;
  public $id;
  public $internalWebPropertyId;
  public $kind;
  public $name;
  protected $__parentLinkType = 'Google_GoalParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $profileId;
  public $selfLink;
  public $type;
  public $updated;
  protected $__urlDestinationDetailsType = 'Google_GoalUrlDestinationDetails';
  protected $__urlDestinationDetailsDataType = '';
  public $urlDestinationDetails;
  public $value;
  protected $__visitNumPagesDetailsType = 'Google_GoalVisitNumPagesDetails';
  protected $__visitNumPagesDetailsDataType = '';
  public $visitNumPagesDetails;
  protected $__visitTimeOnSiteDetailsType = 'Google_GoalVisitTimeOnSiteDetails';
  protected $__visitTimeOnSiteDetailsDataType = '';
  public $visitTimeOnSiteDetails;
  public $webPropertyId;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setActive($active) {
    $this->active = $active;
  }
  public function getActive() {
    return $this->active;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setEventDetails(Google_GoalEventDetails $eventDetails) {
    $this->eventDetails = $eventDetails;
  }
  public function getEventDetails() {
    return $this->eventDetails;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
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
  public function setParentLink(Google_GoalParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
  public function setProfileId($profileId) {
    $this->profileId = $profileId;
  }
  public function getProfileId() {
    return $this->profileId;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setUrlDestinationDetails(Google_GoalUrlDestinationDetails $urlDestinationDetails) {
    $this->urlDestinationDetails = $urlDestinationDetails;
  }
  public function getUrlDestinationDetails() {
    return $this->urlDestinationDetails;
  }
  public function setValue($value) {
    $this->value = $value;
  }
  public function getValue() {
    return $this->value;
  }
  public function setVisitNumPagesDetails(Google_GoalVisitNumPagesDetails $visitNumPagesDetails) {
    $this->visitNumPagesDetails = $visitNumPagesDetails;
  }
  public function getVisitNumPagesDetails() {
    return $this->visitNumPagesDetails;
  }
  public function setVisitTimeOnSiteDetails(Google_GoalVisitTimeOnSiteDetails $visitTimeOnSiteDetails) {
    $this->visitTimeOnSiteDetails = $visitTimeOnSiteDetails;
  }
  public function getVisitTimeOnSiteDetails() {
    return $this->visitTimeOnSiteDetails;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
}

class Google_GoalEventDetails extends Google_Model {
  protected $__eventConditionsType = 'Google_GoalEventDetailsEventConditions';
  protected $__eventConditionsDataType = 'array';
  public $eventConditions;
  public $useEventValue;
  public function setEventConditions(/* array(Google_GoalEventDetailsEventConditions) */ $eventConditions) {
    $this->assertIsArray($eventConditions, 'Google_GoalEventDetailsEventConditions', __METHOD__);
    $this->eventConditions = $eventConditions;
  }
  public function getEventConditions() {
    return $this->eventConditions;
  }
  public function setUseEventValue($useEventValue) {
    $this->useEventValue = $useEventValue;
  }
  public function getUseEventValue() {
    return $this->useEventValue;
  }
}

class Google_GoalEventDetailsEventConditions extends Google_Model {
  public $comparisonType;
  public $comparisonValue;
  public $expression;
  public $matchType;
  public $type;
  public function setComparisonType($comparisonType) {
    $this->comparisonType = $comparisonType;
  }
  public function getComparisonType() {
    return $this->comparisonType;
  }
  public function setComparisonValue($comparisonValue) {
    $this->comparisonValue = $comparisonValue;
  }
  public function getComparisonValue() {
    return $this->comparisonValue;
  }
  public function setExpression($expression) {
    $this->expression = $expression;
  }
  public function getExpression() {
    return $this->expression;
  }
  public function setMatchType($matchType) {
    $this->matchType = $matchType;
  }
  public function getMatchType() {
    return $this->matchType;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_GoalParentLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_GoalUrlDestinationDetails extends Google_Model {
  public $caseSensitive;
  public $firstStepRequired;
  public $matchType;
  protected $__stepsType = 'Google_GoalUrlDestinationDetailsSteps';
  protected $__stepsDataType = 'array';
  public $steps;
  public $url;
  public function setCaseSensitive($caseSensitive) {
    $this->caseSensitive = $caseSensitive;
  }
  public function getCaseSensitive() {
    return $this->caseSensitive;
  }
  public function setFirstStepRequired($firstStepRequired) {
    $this->firstStepRequired = $firstStepRequired;
  }
  public function getFirstStepRequired() {
    return $this->firstStepRequired;
  }
  public function setMatchType($matchType) {
    $this->matchType = $matchType;
  }
  public function getMatchType() {
    return $this->matchType;
  }
  public function setSteps(/* array(Google_GoalUrlDestinationDetailsSteps) */ $steps) {
    $this->assertIsArray($steps, 'Google_GoalUrlDestinationDetailsSteps', __METHOD__);
    $this->steps = $steps;
  }
  public function getSteps() {
    return $this->steps;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class Google_GoalUrlDestinationDetailsSteps extends Google_Model {
  public $name;
  public $number;
  public $url;
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setNumber($number) {
    $this->number = $number;
  }
  public function getNumber() {
    return $this->number;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class Google_GoalVisitNumPagesDetails extends Google_Model {
  public $comparisonType;
  public $comparisonValue;
  public function setComparisonType($comparisonType) {
    $this->comparisonType = $comparisonType;
  }
  public function getComparisonType() {
    return $this->comparisonType;
  }
  public function setComparisonValue($comparisonValue) {
    $this->comparisonValue = $comparisonValue;
  }
  public function getComparisonValue() {
    return $this->comparisonValue;
  }
}

class Google_GoalVisitTimeOnSiteDetails extends Google_Model {
  public $comparisonType;
  public $comparisonValue;
  public function setComparisonType($comparisonType) {
    $this->comparisonType = $comparisonType;
  }
  public function getComparisonType() {
    return $this->comparisonType;
  }
  public function setComparisonValue($comparisonValue) {
    $this->comparisonValue = $comparisonValue;
  }
  public function getComparisonValue() {
    return $this->comparisonValue;
  }
}

class Google_Goals extends Google_Model {
  protected $__itemsType = 'Google_Goal';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  public $startIndex;
  public $totalResults;
  public $username;
  public function setItems(/* array(Google_Goal) */ $items) {
    $this->assertIsArray($items, 'Google_Goal', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
}

class Google_McfData extends Google_Model {
  protected $__columnHeadersType = 'Google_McfDataColumnHeaders';
  protected $__columnHeadersDataType = 'array';
  public $columnHeaders;
  public $containsSampledData;
  public $id;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  protected $__profileInfoType = 'Google_McfDataProfileInfo';
  protected $__profileInfoDataType = '';
  public $profileInfo;
  protected $__queryType = 'Google_McfDataQuery';
  protected $__queryDataType = '';
  public $query;
  protected $__rowsType = 'Google_McfDataRows';
  protected $__rowsDataType = 'array';
  public $rows;
  public $selfLink;
  public $totalResults;
  public $totalsForAllResults;
  public function setColumnHeaders(/* array(Google_McfDataColumnHeaders) */ $columnHeaders) {
    $this->assertIsArray($columnHeaders, 'Google_McfDataColumnHeaders', __METHOD__);
    $this->columnHeaders = $columnHeaders;
  }
  public function getColumnHeaders() {
    return $this->columnHeaders;
  }
  public function setContainsSampledData($containsSampledData) {
    $this->containsSampledData = $containsSampledData;
  }
  public function getContainsSampledData() {
    return $this->containsSampledData;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setProfileInfo(Google_McfDataProfileInfo $profileInfo) {
    $this->profileInfo = $profileInfo;
  }
  public function getProfileInfo() {
    return $this->profileInfo;
  }
  public function setQuery(Google_McfDataQuery $query) {
    $this->query = $query;
  }
  public function getQuery() {
    return $this->query;
  }
  public function setRows(/* array(Google_McfDataRows) */ $rows) {
    $this->assertIsArray($rows, 'Google_McfDataRows', __METHOD__);
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setTotalsForAllResults($totalsForAllResults) {
    $this->totalsForAllResults = $totalsForAllResults;
  }
  public function getTotalsForAllResults() {
    return $this->totalsForAllResults;
  }
}

class Google_McfDataColumnHeaders extends Google_Model {
  public $columnType;
  public $dataType;
  public $name;
  public function setColumnType($columnType) {
    $this->columnType = $columnType;
  }
  public function getColumnType() {
    return $this->columnType;
  }
  public function setDataType($dataType) {
    $this->dataType = $dataType;
  }
  public function getDataType() {
    return $this->dataType;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_McfDataProfileInfo extends Google_Model {
  public $accountId;
  public $internalWebPropertyId;
  public $profileId;
  public $profileName;
  public $tableId;
  public $webPropertyId;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
  }
  public function setProfileId($profileId) {
    $this->profileId = $profileId;
  }
  public function getProfileId() {
    return $this->profileId;
  }
  public function setProfileName($profileName) {
    $this->profileName = $profileName;
  }
  public function getProfileName() {
    return $this->profileName;
  }
  public function setTableId($tableId) {
    $this->tableId = $tableId;
  }
  public function getTableId() {
    return $this->tableId;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
}

class Google_McfDataQuery extends Google_Model {
  public $dimensions;
  public $end_date;
  public $filters;
  public $ids;
  public $max_results;
  public $metrics;
  public $segment;
  public $sort;
  public $start_date;
  public $start_index;
  public function setDimensions($dimensions) {
    $this->dimensions = $dimensions;
  }
  public function getDimensions() {
    return $this->dimensions;
  }
  public function setEnd_date($end_date) {
    $this->end_date = $end_date;
  }
  public function getEnd_date() {
    return $this->end_date;
  }
  public function setFilters($filters) {
    $this->filters = $filters;
  }
  public function getFilters() {
    return $this->filters;
  }
  public function setIds($ids) {
    $this->ids = $ids;
  }
  public function getIds() {
    return $this->ids;
  }
  public function setMax_results($max_results) {
    $this->max_results = $max_results;
  }
  public function getMax_results() {
    return $this->max_results;
  }
  public function setMetrics(/* array(Google_string) */ $metrics) {
    $this->assertIsArray($metrics, 'Google_string', __METHOD__);
    $this->metrics = $metrics;
  }
  public function getMetrics() {
    return $this->metrics;
  }
  public function setSegment($segment) {
    $this->segment = $segment;
  }
  public function getSegment() {
    return $this->segment;
  }
  public function setSort(/* array(Google_string) */ $sort) {
    $this->assertIsArray($sort, 'Google_string', __METHOD__);
    $this->sort = $sort;
  }
  public function getSort() {
    return $this->sort;
  }
  public function setStart_date($start_date) {
    $this->start_date = $start_date;
  }
  public function getStart_date() {
    return $this->start_date;
  }
  public function setStart_index($start_index) {
    $this->start_index = $start_index;
  }
  public function getStart_index() {
    return $this->start_index;
  }
}

class Google_McfDataRows extends Google_Model {
  protected $__conversionPathValueType = 'Google_McfDataRowsConversionPathValue';
  protected $__conversionPathValueDataType = 'array';
  public $conversionPathValue;
  public $primitiveValue;
  public function setConversionPathValue(/* array(Google_McfDataRowsConversionPathValue) */ $conversionPathValue) {
    $this->assertIsArray($conversionPathValue, 'Google_McfDataRowsConversionPathValue', __METHOD__);
    $this->conversionPathValue = $conversionPathValue;
  }
  public function getConversionPathValue() {
    return $this->conversionPathValue;
  }
  public function setPrimitiveValue($primitiveValue) {
    $this->primitiveValue = $primitiveValue;
  }
  public function getPrimitiveValue() {
    return $this->primitiveValue;
  }
}

class Google_McfDataRowsConversionPathValue extends Google_Model {
  public $interactionType;
  public $nodeValue;
  public function setInteractionType($interactionType) {
    $this->interactionType = $interactionType;
  }
  public function getInteractionType() {
    return $this->interactionType;
  }
  public function setNodeValue($nodeValue) {
    $this->nodeValue = $nodeValue;
  }
  public function getNodeValue() {
    return $this->nodeValue;
  }
}

class Google_Profile extends Google_Model {
  public $accountId;
  protected $__childLinkType = 'Google_ProfileChildLink';
  protected $__childLinkDataType = '';
  public $childLink;
  public $created;
  public $currency;
  public $defaultPage;
  public $eCommerceTracking;
  public $excludeQueryParameters;
  public $id;
  public $internalWebPropertyId;
  public $kind;
  public $name;
  protected $__parentLinkType = 'Google_ProfileParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $selfLink;
  public $siteSearchCategoryParameters;
  public $siteSearchQueryParameters;
  public $timezone;
  public $type;
  public $updated;
  public $webPropertyId;
  public $websiteUrl;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setChildLink(Google_ProfileChildLink $childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setCurrency($currency) {
    $this->currency = $currency;
  }
  public function getCurrency() {
    return $this->currency;
  }
  public function setDefaultPage($defaultPage) {
    $this->defaultPage = $defaultPage;
  }
  public function getDefaultPage() {
    return $this->defaultPage;
  }
  public function setECommerceTracking($eCommerceTracking) {
    $this->eCommerceTracking = $eCommerceTracking;
  }
  public function getECommerceTracking() {
    return $this->eCommerceTracking;
  }
  public function setExcludeQueryParameters($excludeQueryParameters) {
    $this->excludeQueryParameters = $excludeQueryParameters;
  }
  public function getExcludeQueryParameters() {
    return $this->excludeQueryParameters;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
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
  public function setParentLink(Google_ProfileParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setSiteSearchCategoryParameters($siteSearchCategoryParameters) {
    $this->siteSearchCategoryParameters = $siteSearchCategoryParameters;
  }
  public function getSiteSearchCategoryParameters() {
    return $this->siteSearchCategoryParameters;
  }
  public function setSiteSearchQueryParameters($siteSearchQueryParameters) {
    $this->siteSearchQueryParameters = $siteSearchQueryParameters;
  }
  public function getSiteSearchQueryParameters() {
    return $this->siteSearchQueryParameters;
  }
  public function setTimezone($timezone) {
    $this->timezone = $timezone;
  }
  public function getTimezone() {
    return $this->timezone;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setWebsiteUrl($websiteUrl) {
    $this->websiteUrl = $websiteUrl;
  }
  public function getWebsiteUrl() {
    return $this->websiteUrl;
  }
}

class Google_ProfileChildLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_ProfileParentLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_Profiles extends Google_Model {
  protected $__itemsType = 'Google_Profile';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  public $startIndex;
  public $totalResults;
  public $username;
  public function setItems(/* array(Google_Profile) */ $items) {
    $this->assertIsArray($items, 'Google_Profile', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
}

class Google_Segment extends Google_Model {
  public $created;
  public $definition;
  public $id;
  public $kind;
  public $name;
  public $segmentId;
  public $selfLink;
  public $updated;
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setDefinition($definition) {
    $this->definition = $definition;
  }
  public function getDefinition() {
    return $this->definition;
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
  public function setSegmentId($segmentId) {
    $this->segmentId = $segmentId;
  }
  public function getSegmentId() {
    return $this->segmentId;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
}

class Google_Segments extends Google_Model {
  protected $__itemsType = 'Google_Segment';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  public $startIndex;
  public $totalResults;
  public $username;
  public function setItems(/* array(Google_Segment) */ $items) {
    $this->assertIsArray($items, 'Google_Segment', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
}

class Google_Webproperties extends Google_Model {
  protected $__itemsType = 'Google_Webproperty';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $kind;
  public $nextLink;
  public $previousLink;
  public $startIndex;
  public $totalResults;
  public $username;
  public function setItems(/* array(Google_Webproperty) */ $items) {
    $this->assertIsArray($items, 'Google_Webproperty', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
}

class Google_Webproperty extends Google_Model {
  public $accountId;
  protected $__childLinkType = 'Google_WebpropertyChildLink';
  protected $__childLinkDataType = '';
  public $childLink;
  public $created;
  public $id;
  public $internalWebPropertyId;
  public $kind;
  public $name;
  protected $__parentLinkType = 'Google_WebpropertyParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $selfLink;
  public $updated;
  public $websiteUrl;
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
  public function setChildLink(Google_WebpropertyChildLink $childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
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
  public function setParentLink(Google_WebpropertyParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setWebsiteUrl($websiteUrl) {
    $this->websiteUrl = $websiteUrl;
  }
  public function getWebsiteUrl() {
    return $this->websiteUrl;
  }
}

class Google_WebpropertyChildLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_WebpropertyParentLink extends Google_Model {
  public $href;
  public $type;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}
