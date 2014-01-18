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
   * The "purchases" collection of methods.
   * Typical usage is:
   *  <code>
   *   $androidpublisherService = new Google_AndroidpublisherService(...);
   *   $purchases = $androidpublisherService->purchases;
   *  </code>
   */
  class Google_PurchasesServiceResource extends Google_ServiceResource {


    /**
     * Cancels a user's subscription purchase. The subscription remains valid until its expiration time.
     * (purchases.cancel)
     *
     * @param string $packageName The package name of the application for which this subscription was purchased (for example, 'com.some.thing').
     * @param string $subscriptionId The purchased subscription ID (for example, 'monthly001').
     * @param string $token The token provided to the user's device when the subscription was purchased.
     * @param array $optParams Optional parameters.
     */
    public function cancel($packageName, $subscriptionId, $token, $optParams = array()) {
      $params = array('packageName' => $packageName, 'subscriptionId' => $subscriptionId, 'token' => $token);
      $params = array_merge($params, $optParams);
      $data = $this->__call('cancel', array($params));
      return $data;
    }
    /**
     * Checks whether a user's subscription purchase is valid and returns its expiry time.
     * (purchases.get)
     *
     * @param string $packageName The package name of the application for which this subscription was purchased (for example, 'com.some.thing').
     * @param string $subscriptionId The purchased subscription ID (for example, 'monthly001').
     * @param string $token The token provided to the user's device when the subscription was purchased.
     * @param array $optParams Optional parameters.
     * @return Google_SubscriptionPurchase
     */
    public function get($packageName, $subscriptionId, $token, $optParams = array()) {
      $params = array('packageName' => $packageName, 'subscriptionId' => $subscriptionId, 'token' => $token);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_SubscriptionPurchase($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Androidpublisher (v1).
 *
 * <p>
 * Lets Android application developers access their Google Play accounts.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/android-publisher" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_AndroidpublisherService extends Google_Service {
  public $purchases;
  /**
   * Constructs the internal representation of the Androidpublisher service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'androidpublisher/v1/applications/';
    $this->version = 'v1';
    $this->serviceName = 'androidpublisher';

    $client->addService($this->serviceName, $this->version);
    $this->purchases = new Google_PurchasesServiceResource($this, $this->serviceName, 'purchases', json_decode('{"methods": {"cancel": {"parameters": {"packageName": {"required": true, "type": "string", "location": "path"}, "subscriptionId": {"required": true, "type": "string", "location": "path"}, "token": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "POST", "path": "{packageName}/subscriptions/{subscriptionId}/purchases/{token}/cancel", "id": "androidpublisher.purchases.cancel"}, "get": {"parameters": {"packageName": {"required": true, "type": "string", "location": "path"}, "subscriptionId": {"required": true, "type": "string", "location": "path"}, "token": {"required": true, "type": "string", "location": "path"}}, "response": {"$ref": "SubscriptionPurchase"}, "httpMethod": "GET", "path": "{packageName}/subscriptions/{subscriptionId}/purchases/{token}", "id": "androidpublisher.purchases.get"}}}', true));

  }
}

class Google_SubscriptionPurchase extends Google_Model {
  public $autoRenewing;
  public $initiationTimestampMsec;
  public $kind;
  public $validUntilTimestampMsec;
  public function setAutoRenewing($autoRenewing) {
    $this->autoRenewing = $autoRenewing;
  }
  public function getAutoRenewing() {
    return $this->autoRenewing;
  }
  public function setInitiationTimestampMsec($initiationTimestampMsec) {
    $this->initiationTimestampMsec = $initiationTimestampMsec;
  }
  public function getInitiationTimestampMsec() {
    return $this->initiationTimestampMsec;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setValidUntilTimestampMsec($validUntilTimestampMsec) {
    $this->validUntilTimestampMsec = $validUntilTimestampMsec;
  }
  public function getValidUntilTimestampMsec() {
    return $this->validUntilTimestampMsec;
  }
}
