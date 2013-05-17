<?php
/*
 * Copyright 2010 Google Inc.
 *
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
   * The "column" collection of methods.
   * Typical usage is:
   *  <code>
   *   $fusiontablesService = new Google_FusiontablesService(...);
   *   $column = $fusiontablesService->column;
   *  </code>
   */
  class Google_ColumnServiceResource extends Google_ServiceResource {


    /**
     * Deletes the column. (column.delete)
     *
     * @param string $tableId Table from which the column is being deleted.
     * @param string $columnId Name or identifier for the column being deleted.
     * @param array $optParams Optional parameters.
     */
    public function delete($tableId, $columnId, $optParams = array()) {
      $params = array('tableId' => $tableId, 'columnId' => $columnId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
    /**
     * Retrieves a specific column by its id. (column.get)
     *
     * @param string $tableId Table to which the column belongs.
     * @param string $columnId Name or identifier for the column that is being requested.
     * @param array $optParams Optional parameters.
     * @return Google_Column
     */
    public function get($tableId, $columnId, $optParams = array()) {
      $params = array('tableId' => $tableId, 'columnId' => $columnId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Column($data);
      } else {
        return $data;
      }
    }
    /**
     * Adds a new column to the table. (column.insert)
     *
     * @param string $tableId Table for which a new column is being added.
     * @param Google_Column $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Column
     */
    public function insert($tableId, Google_Column $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Column($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves a list of columns. (column.list)
     *
     * @param string $tableId Table whose columns are being listed.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Continuation token specifying which result page to return. Optional.
     * @opt_param string maxResults Maximum number of columns to return. Optional. Default is 5.
     * @return Google_ColumnList
     */
    public function listColumn($tableId, $optParams = array()) {
      $params = array('tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_ColumnList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the name or type of an existing column. This method supports patch semantics.
     * (column.patch)
     *
     * @param string $tableId Table for which the column is being updated.
     * @param string $columnId Name or identifier for the column that is being updated.
     * @param Google_Column $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Column
     */
    public function patch($tableId, $columnId, Google_Column $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'columnId' => $columnId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Column($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the name or type of an existing column. (column.update)
     *
     * @param string $tableId Table for which the column is being updated.
     * @param string $columnId Name or identifier for the column that is being updated.
     * @param Google_Column $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Column
     */
    public function update($tableId, $columnId, Google_Column $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'columnId' => $columnId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Column($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "query" collection of methods.
   * Typical usage is:
   *  <code>
   *   $fusiontablesService = new Google_FusiontablesService(...);
   *   $query = $fusiontablesService->query;
   *  </code>
   */
  class Google_QueryServiceResource extends Google_ServiceResource {


    /**
     * Executes an SQL SELECT/INSERT/UPDATE/DELETE/SHOW/DESCRIBE/CREATE statement. (query.sql)
     *
     * @param string $sql An SQL SELECT/SHOW/DESCRIBE/INSERT/UPDATE/DELETE/CREATE statement.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool typed Should typed values be returned in the (JSON) response -- numbers for numeric values and parsed geometries for KML values? Default is true.
     * @opt_param bool hdrs Should column names be included (in the first row)?. Default is true.
     * @return Google_Sqlresponse
     */
    public function sql($sql, $optParams = array()) {
      $params = array('sql' => $sql);
      $params = array_merge($params, $optParams);
      $data = $this->__call('sql', array($params));
      if ($this->useObjects()) {
        return new Google_Sqlresponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Executes an SQL SELECT/SHOW/DESCRIBE statement. (query.sqlGet)
     *
     * @param string $sql An SQL SELECT/SHOW/DESCRIBE statement.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool typed Should typed values be returned in the (JSON) response -- numbers for numeric values and parsed geometries for KML values? Default is true.
     * @opt_param bool hdrs Should column names be included (in the first row)?. Default is true.
     * @return Google_Sqlresponse
     */
    public function sqlGet($sql, $optParams = array()) {
      $params = array('sql' => $sql);
      $params = array_merge($params, $optParams);
      $data = $this->__call('sqlGet', array($params));
      if ($this->useObjects()) {
        return new Google_Sqlresponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "style" collection of methods.
   * Typical usage is:
   *  <code>
   *   $fusiontablesService = new Google_FusiontablesService(...);
   *   $style = $fusiontablesService->style;
   *  </code>
   */
  class Google_StyleServiceResource extends Google_ServiceResource {


    /**
     * Deletes a style. (style.delete)
     *
     * @param string $tableId Table from which the style is being deleted
     * @param int $styleId Identifier (within a table) for the style being deleted
     * @param array $optParams Optional parameters.
     */
    public function delete($tableId, $styleId, $optParams = array()) {
      $params = array('tableId' => $tableId, 'styleId' => $styleId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
    /**
     * Gets a specific style. (style.get)
     *
     * @param string $tableId Table to which the requested style belongs
     * @param int $styleId Identifier (integer) for a specific style in a table
     * @param array $optParams Optional parameters.
     * @return Google_StyleSetting
     */
    public function get($tableId, $styleId, $optParams = array()) {
      $params = array('tableId' => $tableId, 'styleId' => $styleId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_StyleSetting($data);
      } else {
        return $data;
      }
    }
    /**
     * Adds a new style for the table. (style.insert)
     *
     * @param string $tableId Table for which a new style is being added
     * @param Google_StyleSetting $postBody
     * @param array $optParams Optional parameters.
     * @return Google_StyleSetting
     */
    public function insert($tableId, Google_StyleSetting $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_StyleSetting($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves a list of styles. (style.list)
     *
     * @param string $tableId Table whose styles are being listed
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Continuation token specifying which result page to return. Optional.
     * @opt_param string maxResults Maximum number of styles to return. Optional. Default is 5.
     * @return Google_StyleSettingList
     */
    public function listStyle($tableId, $optParams = array()) {
      $params = array('tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_StyleSettingList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an existing style. This method supports patch semantics. (style.patch)
     *
     * @param string $tableId Table whose style is being updated.
     * @param int $styleId Identifier (within a table) for the style being updated.
     * @param Google_StyleSetting $postBody
     * @param array $optParams Optional parameters.
     * @return Google_StyleSetting
     */
    public function patch($tableId, $styleId, Google_StyleSetting $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'styleId' => $styleId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_StyleSetting($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an existing style. (style.update)
     *
     * @param string $tableId Table whose style is being updated.
     * @param int $styleId Identifier (within a table) for the style being updated.
     * @param Google_StyleSetting $postBody
     * @param array $optParams Optional parameters.
     * @return Google_StyleSetting
     */
    public function update($tableId, $styleId, Google_StyleSetting $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'styleId' => $styleId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_StyleSetting($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "table" collection of methods.
   * Typical usage is:
   *  <code>
   *   $fusiontablesService = new Google_FusiontablesService(...);
   *   $table = $fusiontablesService->table;
   *  </code>
   */
  class Google_TableServiceResource extends Google_ServiceResource {


    /**
     * Copies a table. (table.copy)
     *
     * @param string $tableId ID of the table that is being copied.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool copyPresentation Whether to also copy tabs, styles, and templates. Default is false.
     * @return Google_Table
     */
    public function copy($tableId, $optParams = array()) {
      $params = array('tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('copy', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes a table. (table.delete)
     *
     * @param string $tableId ID of the table that is being deleted.
     * @param array $optParams Optional parameters.
     */
    public function delete($tableId, $optParams = array()) {
      $params = array('tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
    /**
     * Retrieves a specific table by its id. (table.get)
     *
     * @param string $tableId Identifier(ID) for the table being requested.
     * @param array $optParams Optional parameters.
     * @return Google_Table
     */
    public function get($tableId, $optParams = array()) {
      $params = array('tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
    /**
     * Import more rows into a table. (table.importRows)
     *
     * @param string $tableId The table into which new rows are being imported.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int startLine The index of the first line from which to start importing, inclusive. Default is 0.
     * @opt_param bool isStrict Whether the CSV must have the same number of values for each row. If false, rows with fewer values will be padded with empty values. Default is true.
     * @opt_param string encoding The encoding of the content. Default is UTF-8. Use 'auto-detect' if you are unsure of the encoding.
     * @opt_param string delimiter The delimiter used to separate cell values. This can only consist of a single character. Default is ','.
     * @opt_param int endLine The index of the last line from which to start importing, exclusive. Thus, the number of imported lines is endLine - startLine. If this parameter is not provided, the file will be imported until the last line of the file. If endLine is negative, then the imported content will exclude the last endLine lines. That is, if endline is negative, no line will be imported whose index is greater than N + endLine where N is the number of lines in the file, and the number of imported lines will be N + endLine - startLine.
     * @return Google_Import
     */
    public function importRows($tableId, $optParams = array()) {
      $params = array('tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('importRows', array($params));
      if ($this->useObjects()) {
        return new Google_Import($data);
      } else {
        return $data;
      }
    }
    /**
     * Creates a new table. (table.insert)
     *
     * @param Google_Table $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Table
     */
    public function insert(Google_Table $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves a list of tables a user owns. (table.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Continuation token specifying which result page to return. Optional.
     * @opt_param string maxResults Maximum number of styles to return. Optional. Default is 5.
     * @return Google_TableList
     */
    public function listTable($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_TableList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an existing table. Unless explicitly requested, only the name, description, and
     * attribution will be updated. This method supports patch semantics. (table.patch)
     *
     * @param string $tableId ID of the table that is being updated.
     * @param Google_Table $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool replaceViewDefinition Should the view definition also be updated? The specified view definition replaces the existing one. Only a view can be updated with a new definition.
     * @return Google_Table
     */
    public function patch($tableId, Google_Table $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an existing table. Unless explicitly requested, only the name, description, and
     * attribution will be updated. (table.update)
     *
     * @param string $tableId ID of the table that is being updated.
     * @param Google_Table $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool replaceViewDefinition Should the view definition also be updated? The specified view definition replaces the existing one. Only a view can be updated with a new definition.
     * @return Google_Table
     */
    public function update($tableId, Google_Table $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Table($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "template" collection of methods.
   * Typical usage is:
   *  <code>
   *   $fusiontablesService = new Google_FusiontablesService(...);
   *   $template = $fusiontablesService->template;
   *  </code>
   */
  class Google_TemplateServiceResource extends Google_ServiceResource {


    /**
     * Deletes a template (template.delete)
     *
     * @param string $tableId Table from which the template is being deleted
     * @param int $templateId Identifier for the template which is being deleted
     * @param array $optParams Optional parameters.
     */
    public function delete($tableId, $templateId, $optParams = array()) {
      $params = array('tableId' => $tableId, 'templateId' => $templateId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
    /**
     * Retrieves a specific template by its id (template.get)
     *
     * @param string $tableId Table to which the template belongs
     * @param int $templateId Identifier for the template that is being requested
     * @param array $optParams Optional parameters.
     * @return Google_Template
     */
    public function get($tableId, $templateId, $optParams = array()) {
      $params = array('tableId' => $tableId, 'templateId' => $templateId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Template($data);
      } else {
        return $data;
      }
    }
    /**
     * Creates a new template for the table. (template.insert)
     *
     * @param string $tableId Table for which a new template is being created
     * @param Google_Template $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Template
     */
    public function insert($tableId, Google_Template $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Template($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves a list of templates. (template.list)
     *
     * @param string $tableId Identifier for the table whose templates are being requested
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Continuation token specifying which results page to return. Optional.
     * @opt_param string maxResults Maximum number of templates to return. Optional. Default is 5.
     * @return Google_TemplateList
     */
    public function listTemplate($tableId, $optParams = array()) {
      $params = array('tableId' => $tableId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_TemplateList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an existing template. This method supports patch semantics. (template.patch)
     *
     * @param string $tableId Table to which the updated template belongs
     * @param int $templateId Identifier for the template that is being updated
     * @param Google_Template $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Template
     */
    public function patch($tableId, $templateId, Google_Template $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'templateId' => $templateId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Template($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an existing template (template.update)
     *
     * @param string $tableId Table to which the updated template belongs
     * @param int $templateId Identifier for the template that is being updated
     * @param Google_Template $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Template
     */
    public function update($tableId, $templateId, Google_Template $postBody, $optParams = array()) {
      $params = array('tableId' => $tableId, 'templateId' => $templateId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Template($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Fusiontables (v1).
 *
 * <p>
 * API for working with Fusion Tables data.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/fusiontables" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_FusiontablesService extends Google_Service {
  public $column;
  public $query;
  public $style;
  public $table;
  public $template;
  /**
   * Constructs the internal representation of the Fusiontables service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'fusiontables/v1/';
    $this->version = 'v1';
    $this->serviceName = 'fusiontables';

    $client->addService($this->serviceName, $this->version);
    $this->column = new Google_ColumnServiceResource($this, $this->serviceName, 'column', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Column"}, "response": {"$ref": "Column"}, "httpMethod": "POST", "path": "tables/{tableId}/columns", "id": "fusiontables.column.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "columnId": {"required": true, "type": "string", "location": "path"}}, "response": {"$ref": "Column"}, "httpMethod": "GET", "path": "tables/{tableId}/columns/{columnId}", "id": "fusiontables.column.get"}, "list": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "tableId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "format": "uint32"}}, "response": {"$ref": "ColumnList"}, "httpMethod": "GET", "path": "tables/{tableId}/columns", "id": "fusiontables.column.list"}, "update": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "columnId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Column"}, "response": {"$ref": "Column"}, "httpMethod": "PUT", "path": "tables/{tableId}/columns/{columnId}", "id": "fusiontables.column.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "columnId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Column"}, "response": {"$ref": "Column"}, "httpMethod": "PATCH", "path": "tables/{tableId}/columns/{columnId}", "id": "fusiontables.column.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "columnId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE", "path": "tables/{tableId}/columns/{columnId}", "id": "fusiontables.column.delete"}}}', true));
    $this->query = new Google_QueryServiceResource($this, $this->serviceName, 'query', json_decode('{"methods": {"sqlGet": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"typed": {"type": "boolean", "location": "query"}, "hdrs": {"type": "boolean", "location": "query"}, "sql": {"required": true, "type": "string", "location": "query"}}, "response": {"$ref": "Sqlresponse"}, "httpMethod": "GET", "path": "query", "id": "fusiontables.query.sqlGet"}, "sql": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"typed": {"type": "boolean", "location": "query"}, "hdrs": {"type": "boolean", "location": "query"}, "sql": {"required": true, "type": "string", "location": "query"}}, "response": {"$ref": "Sqlresponse"}, "httpMethod": "POST", "path": "query", "id": "fusiontables.query.sql"}}}', true));
    $this->style = new Google_StyleServiceResource($this, $this->serviceName, 'style', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "StyleSetting"}, "response": {"$ref": "StyleSetting"}, "httpMethod": "POST", "path": "tables/{tableId}/styles", "id": "fusiontables.style.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "styleId": {"location": "path", "required": true, "type": "integer", "format": "int32"}}, "response": {"$ref": "StyleSetting"}, "httpMethod": "GET", "path": "tables/{tableId}/styles/{styleId}", "id": "fusiontables.style.get"}, "list": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "tableId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "format": "uint32"}}, "response": {"$ref": "StyleSettingList"}, "httpMethod": "GET", "path": "tables/{tableId}/styles", "id": "fusiontables.style.list"}, "update": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "styleId": {"location": "path", "required": true, "type": "integer", "format": "int32"}}, "request": {"$ref": "StyleSetting"}, "response": {"$ref": "StyleSetting"}, "httpMethod": "PUT", "path": "tables/{tableId}/styles/{styleId}", "id": "fusiontables.style.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "styleId": {"location": "path", "required": true, "type": "integer", "format": "int32"}}, "request": {"$ref": "StyleSetting"}, "response": {"$ref": "StyleSetting"}, "httpMethod": "PATCH", "path": "tables/{tableId}/styles/{styleId}", "id": "fusiontables.style.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "styleId": {"location": "path", "required": true, "type": "integer", "format": "int32"}}, "httpMethod": "DELETE", "path": "tables/{tableId}/styles/{styleId}", "id": "fusiontables.style.delete"}}}', true));
    $this->table = new Google_TableServiceResource($this, $this->serviceName, 'table', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "request": {"$ref": "Table"}, "id": "fusiontables.table.insert", "httpMethod": "POST", "path": "tables", "response": {"$ref": "Table"}}, "get": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}}, "response": {"$ref": "Table"}, "httpMethod": "GET", "path": "tables/{tableId}", "id": "fusiontables.table.get"}, "importRows": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"startLine": {"location": "query", "type": "integer", "format": "int32"}, "isStrict": {"type": "boolean", "location": "query"}, "encoding": {"type": "string", "location": "query"}, "delimiter": {"type": "string", "location": "query"}, "tableId": {"required": true, "type": "string", "location": "path"}, "endLine": {"location": "query", "type": "integer", "format": "int32"}}, "supportsMediaUpload": true, "mediaUpload": {"maxSize": "100MB", "protocols": {"simple": {"path": "/upload/fusiontables/v1/tables/{tableId}/import", "multipart": true}, "resumable": {"path": "/resumable/upload/fusiontables/v1/tables/{tableId}/import", "multipart": true}}, "accept": ["application/octet-stream"]}, "response": {"$ref": "Import"}, "httpMethod": "POST", "path": "tables/{tableId}/import", "id": "fusiontables.table.importRows"}, "list": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "format": "uint32"}}, "id": "fusiontables.table.list", "httpMethod": "GET", "path": "tables", "response": {"$ref": "TableList"}}, "update": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"replaceViewDefinition": {"type": "boolean", "location": "query"}, "tableId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Table"}, "response": {"$ref": "Table"}, "httpMethod": "PUT", "path": "tables/{tableId}", "id": "fusiontables.table.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"replaceViewDefinition": {"type": "boolean", "location": "query"}, "tableId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Table"}, "response": {"$ref": "Table"}, "httpMethod": "PATCH", "path": "tables/{tableId}", "id": "fusiontables.table.patch"}, "copy": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"copyPresentation": {"type": "boolean", "location": "query"}, "tableId": {"required": true, "type": "string", "location": "path"}}, "response": {"$ref": "Table"}, "httpMethod": "POST", "path": "tables/{tableId}/copy", "id": "fusiontables.table.copy"}, "delete": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE", "path": "tables/{tableId}", "id": "fusiontables.table.delete"}}}', true));
    $this->template = new Google_TemplateServiceResource($this, $this->serviceName, 'template', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Template"}, "response": {"$ref": "Template"}, "httpMethod": "POST", "path": "tables/{tableId}/templates", "id": "fusiontables.template.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "templateId": {"location": "path", "required": true, "type": "integer", "format": "int32"}}, "response": {"$ref": "Template"}, "httpMethod": "GET", "path": "tables/{tableId}/templates/{templateId}", "id": "fusiontables.template.get"}, "list": {"scopes": ["https://www.googleapis.com/auth/fusiontables", "https://www.googleapis.com/auth/fusiontables.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "tableId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "format": "uint32"}}, "response": {"$ref": "TemplateList"}, "httpMethod": "GET", "path": "tables/{tableId}/templates", "id": "fusiontables.template.list"}, "update": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "templateId": {"location": "path", "required": true, "type": "integer", "format": "int32"}}, "request": {"$ref": "Template"}, "response": {"$ref": "Template"}, "httpMethod": "PUT", "path": "tables/{tableId}/templates/{templateId}", "id": "fusiontables.template.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "templateId": {"location": "path", "required": true, "type": "integer", "format": "int32"}}, "request": {"$ref": "Template"}, "response": {"$ref": "Template"}, "httpMethod": "PATCH", "path": "tables/{tableId}/templates/{templateId}", "id": "fusiontables.template.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/fusiontables"], "parameters": {"tableId": {"required": true, "type": "string", "location": "path"}, "templateId": {"location": "path", "required": true, "type": "integer", "format": "int32"}}, "httpMethod": "DELETE", "path": "tables/{tableId}/templates/{templateId}", "id": "fusiontables.template.delete"}}}', true));

  }
}

class Google_Bucket extends Google_Model {
  public $color;
  public $icon;
  public $max;
  public $min;
  public $opacity;
  public $weight;
  public function setColor($color) {
    $this->color = $color;
  }
  public function getColor() {
    return $this->color;
  }
  public function setIcon($icon) {
    $this->icon = $icon;
  }
  public function getIcon() {
    return $this->icon;
  }
  public function setMax($max) {
    $this->max = $max;
  }
  public function getMax() {
    return $this->max;
  }
  public function setMin($min) {
    $this->min = $min;
  }
  public function getMin() {
    return $this->min;
  }
  public function setOpacity($opacity) {
    $this->opacity = $opacity;
  }
  public function getOpacity() {
    return $this->opacity;
  }
  public function setWeight($weight) {
    $this->weight = $weight;
  }
  public function getWeight() {
    return $this->weight;
  }
}

class Google_Column extends Google_Model {
  protected $__baseColumnType = 'Google_ColumnBaseColumn';
  protected $__baseColumnDataType = '';
  public $baseColumn;
  public $columnId;
  public $kind;
  public $name;
  public $type;
  public function setBaseColumn(Google_ColumnBaseColumn $baseColumn) {
    $this->baseColumn = $baseColumn;
  }
  public function getBaseColumn() {
    return $this->baseColumn;
  }
  public function setColumnId($columnId) {
    $this->columnId = $columnId;
  }
  public function getColumnId() {
    return $this->columnId;
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
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_ColumnBaseColumn extends Google_Model {
  public $columnId;
  public $tableIndex;
  public function setColumnId($columnId) {
    $this->columnId = $columnId;
  }
  public function getColumnId() {
    return $this->columnId;
  }
  public function setTableIndex($tableIndex) {
    $this->tableIndex = $tableIndex;
  }
  public function getTableIndex() {
    return $this->tableIndex;
  }
}

class Google_ColumnList extends Google_Model {
  protected $__itemsType = 'Google_Column';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $nextPageToken;
  public $totalItems;
  public function setItems(/* array(Google_Column) */ $items) {
    $this->assertIsArray($items, 'Google_Column', __METHOD__);
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
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
}

class Google_Geometry extends Google_Model {
  public $geometries;
  public $geometry;
  public $type;
  public function setGeometries(/* array(Google_object) */ $geometries) {
    $this->assertIsArray($geometries, 'Google_object', __METHOD__);
    $this->geometries = $geometries;
  }
  public function getGeometries() {
    return $this->geometries;
  }
  public function setGeometry($geometry) {
    $this->geometry = $geometry;
  }
  public function getGeometry() {
    return $this->geometry;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_Import extends Google_Model {
  public $kind;
  public $numRowsReceived;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setNumRowsReceived($numRowsReceived) {
    $this->numRowsReceived = $numRowsReceived;
  }
  public function getNumRowsReceived() {
    return $this->numRowsReceived;
  }
}

class Google_Line extends Google_Model {
  public $coordinates;
  public $type;
  public function setCoordinates(/* array(Google_double) */ $coordinates) {
    $this->assertIsArray($coordinates, 'Google_double', __METHOD__);
    $this->coordinates = $coordinates;
  }
  public function getCoordinates() {
    return $this->coordinates;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_LineStyle extends Google_Model {
  public $strokeColor;
  protected $__strokeColorStylerType = 'Google_StyleFunction';
  protected $__strokeColorStylerDataType = '';
  public $strokeColorStyler;
  public $strokeOpacity;
  public $strokeWeight;
  protected $__strokeWeightStylerType = 'Google_StyleFunction';
  protected $__strokeWeightStylerDataType = '';
  public $strokeWeightStyler;
  public function setStrokeColor($strokeColor) {
    $this->strokeColor = $strokeColor;
  }
  public function getStrokeColor() {
    return $this->strokeColor;
  }
  public function setStrokeColorStyler(Google_StyleFunction $strokeColorStyler) {
    $this->strokeColorStyler = $strokeColorStyler;
  }
  public function getStrokeColorStyler() {
    return $this->strokeColorStyler;
  }
  public function setStrokeOpacity($strokeOpacity) {
    $this->strokeOpacity = $strokeOpacity;
  }
  public function getStrokeOpacity() {
    return $this->strokeOpacity;
  }
  public function setStrokeWeight($strokeWeight) {
    $this->strokeWeight = $strokeWeight;
  }
  public function getStrokeWeight() {
    return $this->strokeWeight;
  }
  public function setStrokeWeightStyler(Google_StyleFunction $strokeWeightStyler) {
    $this->strokeWeightStyler = $strokeWeightStyler;
  }
  public function getStrokeWeightStyler() {
    return $this->strokeWeightStyler;
  }
}

class Google_Point extends Google_Model {
  public $coordinates;
  public $type;
  public function setCoordinates(/* array(Google_double) */ $coordinates) {
    $this->assertIsArray($coordinates, 'Google_double', __METHOD__);
    $this->coordinates = $coordinates;
  }
  public function getCoordinates() {
    return $this->coordinates;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_PointStyle extends Google_Model {
  public $iconName;
  protected $__iconStylerType = 'Google_StyleFunction';
  protected $__iconStylerDataType = '';
  public $iconStyler;
  public function setIconName($iconName) {
    $this->iconName = $iconName;
  }
  public function getIconName() {
    return $this->iconName;
  }
  public function setIconStyler(Google_StyleFunction $iconStyler) {
    $this->iconStyler = $iconStyler;
  }
  public function getIconStyler() {
    return $this->iconStyler;
  }
}

class Google_Polygon extends Google_Model {
  public $coordinates;
  public $type;
  public function setCoordinates(/* array(Google_double) */ $coordinates) {
    $this->assertIsArray($coordinates, 'Google_double', __METHOD__);
    $this->coordinates = $coordinates;
  }
  public function getCoordinates() {
    return $this->coordinates;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_PolygonStyle extends Google_Model {
  public $fillColor;
  protected $__fillColorStylerType = 'Google_StyleFunction';
  protected $__fillColorStylerDataType = '';
  public $fillColorStyler;
  public $fillOpacity;
  public $strokeColor;
  protected $__strokeColorStylerType = 'Google_StyleFunction';
  protected $__strokeColorStylerDataType = '';
  public $strokeColorStyler;
  public $strokeOpacity;
  public $strokeWeight;
  protected $__strokeWeightStylerType = 'Google_StyleFunction';
  protected $__strokeWeightStylerDataType = '';
  public $strokeWeightStyler;
  public function setFillColor($fillColor) {
    $this->fillColor = $fillColor;
  }
  public function getFillColor() {
    return $this->fillColor;
  }
  public function setFillColorStyler(Google_StyleFunction $fillColorStyler) {
    $this->fillColorStyler = $fillColorStyler;
  }
  public function getFillColorStyler() {
    return $this->fillColorStyler;
  }
  public function setFillOpacity($fillOpacity) {
    $this->fillOpacity = $fillOpacity;
  }
  public function getFillOpacity() {
    return $this->fillOpacity;
  }
  public function setStrokeColor($strokeColor) {
    $this->strokeColor = $strokeColor;
  }
  public function getStrokeColor() {
    return $this->strokeColor;
  }
  public function setStrokeColorStyler(Google_StyleFunction $strokeColorStyler) {
    $this->strokeColorStyler = $strokeColorStyler;
  }
  public function getStrokeColorStyler() {
    return $this->strokeColorStyler;
  }
  public function setStrokeOpacity($strokeOpacity) {
    $this->strokeOpacity = $strokeOpacity;
  }
  public function getStrokeOpacity() {
    return $this->strokeOpacity;
  }
  public function setStrokeWeight($strokeWeight) {
    $this->strokeWeight = $strokeWeight;
  }
  public function getStrokeWeight() {
    return $this->strokeWeight;
  }
  public function setStrokeWeightStyler(Google_StyleFunction $strokeWeightStyler) {
    $this->strokeWeightStyler = $strokeWeightStyler;
  }
  public function getStrokeWeightStyler() {
    return $this->strokeWeightStyler;
  }
}

class Google_Sqlresponse extends Google_Model {
  public $columns;
  public $kind;
  public $rows;
  public function setColumns(/* array(Google_string) */ $columns) {
    $this->assertIsArray($columns, 'Google_string', __METHOD__);
    $this->columns = $columns;
  }
  public function getColumns() {
    return $this->columns;
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

class Google_StyleFunction extends Google_Model {
  protected $__bucketsType = 'Google_Bucket';
  protected $__bucketsDataType = 'array';
  public $buckets;
  public $columnName;
  protected $__gradientType = 'Google_StyleFunctionGradient';
  protected $__gradientDataType = '';
  public $gradient;
  public $kind;
  public function setBuckets(/* array(Google_Bucket) */ $buckets) {
    $this->assertIsArray($buckets, 'Google_Bucket', __METHOD__);
    $this->buckets = $buckets;
  }
  public function getBuckets() {
    return $this->buckets;
  }
  public function setColumnName($columnName) {
    $this->columnName = $columnName;
  }
  public function getColumnName() {
    return $this->columnName;
  }
  public function setGradient(Google_StyleFunctionGradient $gradient) {
    $this->gradient = $gradient;
  }
  public function getGradient() {
    return $this->gradient;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_StyleFunctionGradient extends Google_Model {
  protected $__colorsType = 'Google_StyleFunctionGradientColors';
  protected $__colorsDataType = 'array';
  public $colors;
  public $max;
  public $min;
  public function setColors(/* array(Google_StyleFunctionGradientColors) */ $colors) {
    $this->assertIsArray($colors, 'Google_StyleFunctionGradientColors', __METHOD__);
    $this->colors = $colors;
  }
  public function getColors() {
    return $this->colors;
  }
  public function setMax($max) {
    $this->max = $max;
  }
  public function getMax() {
    return $this->max;
  }
  public function setMin($min) {
    $this->min = $min;
  }
  public function getMin() {
    return $this->min;
  }
}

class Google_StyleFunctionGradientColors extends Google_Model {
  public $color;
  public $opacity;
  public function setColor($color) {
    $this->color = $color;
  }
  public function getColor() {
    return $this->color;
  }
  public function setOpacity($opacity) {
    $this->opacity = $opacity;
  }
  public function getOpacity() {
    return $this->opacity;
  }
}

class Google_StyleSetting extends Google_Model {
  public $kind;
  protected $__markerOptionsType = 'Google_PointStyle';
  protected $__markerOptionsDataType = '';
  public $markerOptions;
  public $name;
  protected $__polygonOptionsType = 'Google_PolygonStyle';
  protected $__polygonOptionsDataType = '';
  public $polygonOptions;
  protected $__polylineOptionsType = 'Google_LineStyle';
  protected $__polylineOptionsDataType = '';
  public $polylineOptions;
  public $styleId;
  public $tableId;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setMarkerOptions(Google_PointStyle $markerOptions) {
    $this->markerOptions = $markerOptions;
  }
  public function getMarkerOptions() {
    return $this->markerOptions;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setPolygonOptions(Google_PolygonStyle $polygonOptions) {
    $this->polygonOptions = $polygonOptions;
  }
  public function getPolygonOptions() {
    return $this->polygonOptions;
  }
  public function setPolylineOptions(Google_LineStyle $polylineOptions) {
    $this->polylineOptions = $polylineOptions;
  }
  public function getPolylineOptions() {
    return $this->polylineOptions;
  }
  public function setStyleId($styleId) {
    $this->styleId = $styleId;
  }
  public function getStyleId() {
    return $this->styleId;
  }
  public function setTableId($tableId) {
    $this->tableId = $tableId;
  }
  public function getTableId() {
    return $this->tableId;
  }
}

class Google_StyleSettingList extends Google_Model {
  protected $__itemsType = 'Google_StyleSetting';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $nextPageToken;
  public $totalItems;
  public function setItems(/* array(Google_StyleSetting) */ $items) {
    $this->assertIsArray($items, 'Google_StyleSetting', __METHOD__);
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
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
}

class Google_Table extends Google_Model {
  public $attribution;
  public $attributionLink;
  public $baseTableIds;
  protected $__columnsType = 'Google_Column';
  protected $__columnsDataType = 'array';
  public $columns;
  public $description;
  public $isExportable;
  public $kind;
  public $name;
  public $sql;
  public $tableId;
  public function setAttribution($attribution) {
    $this->attribution = $attribution;
  }
  public function getAttribution() {
    return $this->attribution;
  }
  public function setAttributionLink($attributionLink) {
    $this->attributionLink = $attributionLink;
  }
  public function getAttributionLink() {
    return $this->attributionLink;
  }
  public function setBaseTableIds(/* array(Google_string) */ $baseTableIds) {
    $this->assertIsArray($baseTableIds, 'Google_string', __METHOD__);
    $this->baseTableIds = $baseTableIds;
  }
  public function getBaseTableIds() {
    return $this->baseTableIds;
  }
  public function setColumns(/* array(Google_Column) */ $columns) {
    $this->assertIsArray($columns, 'Google_Column', __METHOD__);
    $this->columns = $columns;
  }
  public function getColumns() {
    return $this->columns;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setIsExportable($isExportable) {
    $this->isExportable = $isExportable;
  }
  public function getIsExportable() {
    return $this->isExportable;
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
  public function setSql($sql) {
    $this->sql = $sql;
  }
  public function getSql() {
    return $this->sql;
  }
  public function setTableId($tableId) {
    $this->tableId = $tableId;
  }
  public function getTableId() {
    return $this->tableId;
  }
}

class Google_TableList extends Google_Model {
  protected $__itemsType = 'Google_Table';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $nextPageToken;
  public function setItems(/* array(Google_Table) */ $items) {
    $this->assertIsArray($items, 'Google_Table', __METHOD__);
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

class Google_Template extends Google_Model {
  public $automaticColumnNames;
  public $body;
  public $kind;
  public $name;
  public $tableId;
  public $templateId;
  public function setAutomaticColumnNames(/* array(Google_string) */ $automaticColumnNames) {
    $this->assertIsArray($automaticColumnNames, 'Google_string', __METHOD__);
    $this->automaticColumnNames = $automaticColumnNames;
  }
  public function getAutomaticColumnNames() {
    return $this->automaticColumnNames;
  }
  public function setBody($body) {
    $this->body = $body;
  }
  public function getBody() {
    return $this->body;
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
  public function setTableId($tableId) {
    $this->tableId = $tableId;
  }
  public function getTableId() {
    return $this->tableId;
  }
  public function setTemplateId($templateId) {
    $this->templateId = $templateId;
  }
  public function getTemplateId() {
    return $this->templateId;
  }
}

class Google_TemplateList extends Google_Model {
  protected $__itemsType = 'Google_Template';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $nextPageToken;
  public $totalItems;
  public function setItems(/* array(Google_Template) */ $items) {
    $this->assertIsArray($items, 'Google_Template', __METHOD__);
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
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
}
