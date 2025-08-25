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

namespace Google\Service\Looker\Resource;

use Google\Service\Looker\ExportInstanceRequest;
use Google\Service\Looker\ImportInstanceRequest;
use Google\Service\Looker\Instance;
use Google\Service\Looker\ListInstancesResponse;
use Google\Service\Looker\Operation;
use Google\Service\Looker\Policy;
use Google\Service\Looker\RestartInstanceRequest;
use Google\Service\Looker\SetIamPolicyRequest;
use Google\Service\Looker\TestIamPermissionsRequest;
use Google\Service\Looker\TestIamPermissionsResponse;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $lookerService = new Google\Service\Looker(...);
 *   $instances = $lookerService->projects_locations_instances;
 *  </code>
 */
class ProjectsLocationsInstances extends \Google\Service\Resource
{
  /**
   * Creates a new Instance in a given project and location. (instances.create)
   *
   * @param string $parent Required. Format:
   * `projects/{project}/locations/{location}`.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string instanceId Required. The unique instance identifier. Must
   * contain only lowercase letters, numbers, or hyphens, with the first character
   * a letter and the last a letter or a number. 63 characters maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Instance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Delete instance. (instances.delete)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Whether to force cascading delete.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Export instance. (instances.export)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param ExportInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function export($name, ExportInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('export', [$params], Operation::class);
  }
  /**
   * Gets details of a single Instance. (instances.get)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param array $optParams Optional parameters.
   * @return Instance
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Instance::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (instances.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Import instance. (instances.import)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param ImportInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function import($name, ImportInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], Operation::class);
  }
  /**
   * Lists Instances in a given project and location.
   * (instances.listProjectsLocationsInstances)
   *
   * @param string $parent Required. Format:
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of instances to return. If
   * unspecified at most 256 will be returned. The maximum possible value is 2048.
   * @opt_param string pageToken A page token received from a previous
   * ListInstancesRequest.
   * @return ListInstancesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInstances($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstancesResponse::class);
  }
  /**
   * Update Instance. (instances.patch)
   *
   * @param string $name Output only. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask used to specify the fields
   * to be overwritten in the Instance resource by the update. The fields
   * specified in the mask are relative to the resource, not the full request. A
   * field will be overwritten if it is in the mask.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Instance $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Restart instance. (instances.restart)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param RestartInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restart($name, RestartInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restart', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (instances.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (instances.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInstances::class, 'Google_Service_Looker_Resource_ProjectsLocationsInstances');
