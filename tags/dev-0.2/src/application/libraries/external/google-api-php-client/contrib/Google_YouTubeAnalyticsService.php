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
   * The "reports" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeAnalyticsService = new Google_YouTubeAnalyticsService(...);
   *   $reports = $youtubeAnalyticsService->reports;
   *  </code>
   */
  class Google_ReportsServiceResource extends Google_ServiceResource {


    /**
     * Retrieve your YouTube Analytics reports. (reports.query)
     *
     * @param string $ids Identifies the YouTube channel or content owner for which you are retrieving YouTube Analytics data.
    - To request data for a YouTube user, set the ids parameter value to channel==CHANNEL_ID, where CHANNEL_ID specifies the unique YouTube channel ID.
    - To request data for a YouTube CMS content owner, set the ids parameter value to contentOwner==OWNER_NAME, where OWNER_NAME is the CMS name of the content owner.
     * @param string $start_date The start date for fetching YouTube Analytics data. The value should be in YYYY-MM-DD format.
     * @param string $end_date The end date for fetching YouTube Analytics data. The value should be in YYYY-MM-DD format.
     * @param string $metrics A comma-separated list of YouTube Analytics metrics, such as views or likes,dislikes. See the Available Reports document for a list of the reports that you can retrieve and the metrics available in each report, and see the Metrics document for definitions of those metrics.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string dimensions A comma-separated list of YouTube Analytics dimensions, such as views or ageGroup,gender. See the Available Reports document for a list of the reports that you can retrieve and the dimensions used for those reports. Also see the Dimensions document for definitions of those dimensions.
     * @opt_param string filters A list of filters that should be applied when retrieving YouTube Analytics data. The Available Reports document identifies the dimensions that can be used to filter each report, and the Dimensions document defines those dimensions. If a request uses multiple filters, join them together with a semicolon (;), and the returned result table will satisfy both filters. For example, a filters parameter value of video==dMH0bHeiRNg;country==IT restricts the result set to include data for the given video in Italy.
     * @opt_param int max-results The maximum number of rows to include in the response.
     * @opt_param string sort A comma-separated list of dimensions or metrics that determine the sort order for YouTube Analytics data. By default the sort order is ascending. The '-' prefix causes descending sort order.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter (one-based, inclusive).
     * @return Google_ResultTable
     */
    public function query($ids, $start_date, $end_date, $metrics, $optParams = array()) {
      $params = array('ids' => $ids, 'start-date' => $start_date, 'end-date' => $end_date, 'metrics' => $metrics);
      $params = array_merge($params, $optParams);
      $data = $this->__call('query', array($params));
      if ($this->useObjects()) {
        return new Google_ResultTable($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_YouTubeAnalytics (v1).
 *
 * <p>
 * Retrieve your YouTube Analytics reports.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://developers.google.com/youtube/analytics/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_YouTubeAnalyticsService extends Google_Service {
  public $reports;
  /**
   * Constructs the internal representation of the YouTubeAnalytics service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'youtube/analytics/v1/';
    $this->version = 'v1';
    $this->serviceName = 'youtubeAnalytics';

    $client->addService($this->serviceName, $this->version);
    $this->reports = new Google_ReportsServiceResource($this, $this->serviceName, 'reports', json_decode('{"methods": {"query": {"id": "youtubeAnalytics.reports.query", "path": "reports", "httpMethod": "GET", "parameters": {"dimensions": {"type": "string", "location": "query"}, "end-date": {"type": "string", "required": true, "location": "query"}, "filters": {"type": "string", "location": "query"}, "ids": {"type": "string", "required": true, "location": "query"}, "max-results": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}, "metrics": {"type": "string", "required": true, "location": "query"}, "sort": {"type": "string", "location": "query"}, "start-date": {"type": "string", "required": true, "location": "query"}, "start-index": {"type": "integer", "format": "int32", "minimum": "1", "location": "query"}}, "response": {"$ref": "ResultTable"}, "scopes": ["https://www.googleapis.com/auth/yt-analytics-monetary.readonly", "https://www.googleapis.com/auth/yt-analytics.readonly"]}}}', true));

  }
}



class Google_ResultTable extends Google_Model {
  protected $__columnHeadersType = 'Google_ResultTableColumnHeaders';
  protected $__columnHeadersDataType = 'array';
  public $columnHeaders;
  public $kind;
  public $rows;
  public function setColumnHeaders(/* array(Google_ResultTableColumnHeaders) */ $columnHeaders) {
    $this->assertIsArray($columnHeaders, 'Google_ResultTableColumnHeaders', __METHOD__);
    $this->columnHeaders = $columnHeaders;
  }
  public function getColumnHeaders() {
    return $this->columnHeaders;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRows(/* array(Google_object) */ $rows) {
    $this->assertIsArray($rows, 'Google_object', __METHOD__);
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
}

class Google_ResultTableColumnHeaders extends Google_Model {
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
