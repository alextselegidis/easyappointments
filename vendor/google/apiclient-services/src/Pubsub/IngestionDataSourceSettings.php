<?php
/*
 * Copyright 2014 Google Inc.
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

namespace Google\Service\Pubsub;

class IngestionDataSourceSettings extends \Google\Model
{
  protected $awsKinesisType = AwsKinesis::class;
  protected $awsKinesisDataType = '';
  protected $cloudStorageType = CloudStorage::class;
  protected $cloudStorageDataType = '';
  protected $platformLogsSettingsType = PlatformLogsSettings::class;
  protected $platformLogsSettingsDataType = '';

  /**
   * @param AwsKinesis
   */
  public function setAwsKinesis(AwsKinesis $awsKinesis)
  {
    $this->awsKinesis = $awsKinesis;
  }
  /**
   * @return AwsKinesis
   */
  public function getAwsKinesis()
  {
    return $this->awsKinesis;
  }
  /**
   * @param CloudStorage
   */
  public function setCloudStorage(CloudStorage $cloudStorage)
  {
    $this->cloudStorage = $cloudStorage;
  }
  /**
   * @return CloudStorage
   */
  public function getCloudStorage()
  {
    return $this->cloudStorage;
  }
  /**
   * @param PlatformLogsSettings
   */
  public function setPlatformLogsSettings(PlatformLogsSettings $platformLogsSettings)
  {
    $this->platformLogsSettings = $platformLogsSettings;
  }
  /**
   * @return PlatformLogsSettings
   */
  public function getPlatformLogsSettings()
  {
    return $this->platformLogsSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IngestionDataSourceSettings::class, 'Google_Service_Pubsub_IngestionDataSourceSettings');
