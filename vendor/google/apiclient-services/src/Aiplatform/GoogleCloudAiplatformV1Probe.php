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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Probe extends \Google\Model
{
  protected $execType = GoogleCloudAiplatformV1ProbeExecAction::class;
  protected $execDataType = '';
  /**
   * @var int
   */
  public $periodSeconds;
  /**
   * @var int
   */
  public $timeoutSeconds;

  /**
   * @param GoogleCloudAiplatformV1ProbeExecAction
   */
  public function setExec(GoogleCloudAiplatformV1ProbeExecAction $exec)
  {
    $this->exec = $exec;
  }
  /**
   * @return GoogleCloudAiplatformV1ProbeExecAction
   */
  public function getExec()
  {
    return $this->exec;
  }
  /**
   * @param int
   */
  public function setPeriodSeconds($periodSeconds)
  {
    $this->periodSeconds = $periodSeconds;
  }
  /**
   * @return int
   */
  public function getPeriodSeconds()
  {
    return $this->periodSeconds;
  }
  /**
   * @param int
   */
  public function setTimeoutSeconds($timeoutSeconds)
  {
    $this->timeoutSeconds = $timeoutSeconds;
  }
  /**
   * @return int
   */
  public function getTimeoutSeconds()
  {
    return $this->timeoutSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Probe::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Probe');
