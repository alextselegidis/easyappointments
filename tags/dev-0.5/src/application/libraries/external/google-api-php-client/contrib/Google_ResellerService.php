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
   * The "customers" collection of methods.
   * Typical usage is:
   *  <code>
   *   $resellerService = new Google_ResellerService(...);
   *   $customers = $resellerService->customers;
   *  </code>
   */
  class Google_CustomersServiceResource extends Google_ServiceResource {


    /**
     * Gets a customer resource if one exists and is owned by the reseller. (customers.get)
     *
     * @param string $customerId Id of the Customer
     * @param array $optParams Optional parameters.
     * @return Google_Customer
     */
    public function get($customerId, $optParams = array()) {
      $params = array('customerId' => $customerId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Customer($data);
      } else {
        return $data;
      }
    }
    /**
     * Creates a customer resource if one does not already exist. (customers.insert)
     *
     * @param Google_Customer $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string customerAuthToken An auth token needed for inserting a customer for which domain already exists. Can be generated at https://www.google.com/a/cpanel//TransferToken. Optional.
     * @return Google_Customer
     */
    public function insert(Google_Customer $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Customer($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a customer resource if one it exists and is owned by the reseller. This method supports
     * patch semantics. (customers.patch)
     *
     * @param string $customerId Id of the Customer
     * @param Google_Customer $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Customer
     */
    public function patch($customerId, Google_Customer $postBody, $optParams = array()) {
      $params = array('customerId' => $customerId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Customer($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a customer resource if one it exists and is owned by the reseller. (customers.update)
     *
     * @param string $customerId Id of the Customer
     * @param Google_Customer $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Customer
     */
    public function update($customerId, Google_Customer $postBody, $optParams = array()) {
      $params = array('customerId' => $customerId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Customer($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "subscriptions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $resellerService = new Google_ResellerService(...);
   *   $subscriptions = $resellerService->subscriptions;
   *  </code>
   */
  class Google_SubscriptionsServiceResource extends Google_ServiceResource {


    /**
     * Changes the plan of a subscription (subscriptions.changePlan)
     *
     * @param string $customerId Id of the Customer
     * @param string $subscriptionId Id of the subscription, which is unique for a customer
     * @param Google_ChangePlanRequest $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Subscription
     */
    public function changePlan($customerId, $subscriptionId, Google_ChangePlanRequest $postBody, $optParams = array()) {
      $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('changePlan', array($params));
      if ($this->useObjects()) {
        return new Google_Subscription($data);
      } else {
        return $data;
      }
    }
    /**
     * Changes the renewal settings of a subscription (subscriptions.changeRenewalSettings)
     *
     * @param string $customerId Id of the Customer
     * @param string $subscriptionId Id of the subscription, which is unique for a customer
     * @param Google_RenewalSettings $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Subscription
     */
    public function changeRenewalSettings($customerId, $subscriptionId, Google_RenewalSettings $postBody, $optParams = array()) {
      $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('changeRenewalSettings', array($params));
      if ($this->useObjects()) {
        return new Google_Subscription($data);
      } else {
        return $data;
      }
    }
    /**
     * Changes the seats configuration of a subscription (subscriptions.changeSeats)
     *
     * @param string $customerId Id of the Customer
     * @param string $subscriptionId Id of the subscription, which is unique for a customer
     * @param Google_Seats $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Subscription
     */
    public function changeSeats($customerId, $subscriptionId, Google_Seats $postBody, $optParams = array()) {
      $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('changeSeats', array($params));
      if ($this->useObjects()) {
        return new Google_Subscription($data);
      } else {
        return $data;
      }
    }
    /**
     * Cancels/Downgrades a subscription. (subscriptions.delete)
     *
     * @param string $customerId Id of the Customer
     * @param string $subscriptionId Id of the subscription, which is unique for a customer
     * @param string $deletionType Whether the subscription is to be fully cancelled or downgraded
     * @param array $optParams Optional parameters.
     */
    public function delete($customerId, $subscriptionId, $deletionType, $optParams = array()) {
      $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId, 'deletionType' => $deletionType);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
    /**
     * Gets a subscription of the customer. (subscriptions.get)
     *
     * @param string $customerId Id of the Customer
     * @param string $subscriptionId Id of the subscription, which is unique for a customer
     * @param array $optParams Optional parameters.
     * @return Google_Subscription
     */
    public function get($customerId, $subscriptionId, $optParams = array()) {
      $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Subscription($data);
      } else {
        return $data;
      }
    }
    /**
     * Creates/Transfers a subscription for the customer. (subscriptions.insert)
     *
     * @param string $customerId Id of the Customer
     * @param Google_Subscription $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string customerAuthToken An auth token needed for transferring a subscription. Can be generated at https://www.google.com/a/cpanel/customer-domain/TransferToken. Optional.
     * @return Google_Subscription
     */
    public function insert($customerId, Google_Subscription $postBody, $optParams = array()) {
      $params = array('customerId' => $customerId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Subscription($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists subscriptions of a reseller, optionally filtered by a customer name prefix.
     * (subscriptions.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Token to specify next page in the list
     * @opt_param string maxResults Maximum number of results to return
     * @opt_param string customerNamePrefix Prefix of the customer's domain name by which the subscriptions should be filtered. Optional
     * @return Google_Subscriptions
     */
    public function listSubscriptions($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Subscriptions($data);
      } else {
        return $data;
      }
    }
    /**
     * Starts paid service of a trial subscription (subscriptions.startPaidService)
     *
     * @param string $customerId Id of the Customer
     * @param string $subscriptionId Id of the subscription, which is unique for a customer
     * @param array $optParams Optional parameters.
     * @return Google_Subscription
     */
    public function startPaidService($customerId, $subscriptionId, $optParams = array()) {
      $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('startPaidService', array($params));
      if ($this->useObjects()) {
        return new Google_Subscription($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Reseller (v1).
 *
 * <p>
 * Lets you create and manage your customers and their subscriptions.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/google-apps/reseller/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_ResellerService extends Google_Service {
  public $customers;
  public $subscriptions;
  /**
   * Constructs the internal representation of the Reseller service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'apps/reseller/v1/';
    $this->version = 'v1';
    $this->serviceName = 'reseller';

    $client->addService($this->serviceName, $this->version);
    $this->customers = new Google_CustomersServiceResource($this, $this->serviceName, 'customers', json_decode('{"methods": {"insert": {"parameters": {"customerAuthToken": {"type": "string", "location": "query"}}, "request": {"$ref": "Customer"}, "response": {"$ref": "Customer"}, "httpMethod": "POST", "path": "customers", "id": "reseller.customers.insert"}, "patch": {"parameters": {"customerId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Customer"}, "response": {"$ref": "Customer"}, "httpMethod": "PATCH", "path": "customers/{customerId}", "id": "reseller.customers.patch"}, "update": {"parameters": {"customerId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Customer"}, "response": {"$ref": "Customer"}, "httpMethod": "PUT", "path": "customers/{customerId}", "id": "reseller.customers.update"}, "get": {"parameters": {"customerId": {"required": true, "type": "string", "location": "path"}}, "response": {"$ref": "Customer"}, "httpMethod": "GET", "path": "customers/{customerId}", "id": "reseller.customers.get"}}}', true));
    $this->subscriptions = new Google_SubscriptionsServiceResource($this, $this->serviceName, 'subscriptions', json_decode('{"methods": {"insert": {"parameters": {"customerAuthToken": {"type": "string", "location": "query"}, "customerId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Subscription"}, "response": {"$ref": "Subscription"}, "httpMethod": "POST", "path": "customers/{customerId}/subscriptions", "id": "reseller.subscriptions.insert"}, "changeRenewalSettings": {"parameters": {"subscriptionId": {"required": true, "type": "string", "location": "path"}, "customerId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "RenewalSettings"}, "response": {"$ref": "Subscription"}, "httpMethod": "POST", "path": "customers/{customerId}/subscriptions/{subscriptionId}/changeRenewalSettings", "id": "reseller.subscriptions.changeRenewalSettings"}, "changePlan": {"parameters": {"subscriptionId": {"required": true, "type": "string", "location": "path"}, "customerId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "ChangePlanRequest"}, "response": {"$ref": "Subscription"}, "httpMethod": "POST", "path": "customers/{customerId}/subscriptions/{subscriptionId}/changePlan", "id": "reseller.subscriptions.changePlan"}, "get": {"parameters": {"subscriptionId": {"required": true, "type": "string", "location": "path"}, "customerId": {"required": true, "type": "string", "location": "path"}}, "response": {"$ref": "Subscription"}, "httpMethod": "GET", "path": "customers/{customerId}/subscriptions/{subscriptionId}", "id": "reseller.subscriptions.get"}, "list": {"parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"format": "uint32", "maximum": "100", "minimum": "1", "location": "query", "type": "integer"}, "customerNamePrefix": {"type": "string", "location": "query"}}, "id": "reseller.subscriptions.list", "httpMethod": "GET", "path": "subscriptions", "response": {"$ref": "Subscriptions"}}, "changeSeats": {"parameters": {"subscriptionId": {"required": true, "type": "string", "location": "path"}, "customerId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Seats"}, "response": {"$ref": "Subscription"}, "httpMethod": "POST", "path": "customers/{customerId}/subscriptions/{subscriptionId}/changeSeats", "id": "reseller.subscriptions.changeSeats"}, "startPaidService": {"parameters": {"subscriptionId": {"required": true, "type": "string", "location": "path"}, "customerId": {"required": true, "type": "string", "location": "path"}}, "response": {"$ref": "Subscription"}, "httpMethod": "POST", "path": "customers/{customerId}/subscriptions/{subscriptionId}/startPaidService", "id": "reseller.subscriptions.startPaidService"}, "delete": {"parameters": {"deletionType": {"enum": ["cancel", "downgrade", "suspend"], "required": true, "location": "query", "type": "string"}, "customerId": {"required": true, "type": "string", "location": "path"}, "subscriptionId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE", "path": "customers/{customerId}/subscriptions/{subscriptionId}", "id": "reseller.subscriptions.delete"}}}', true));

  }
}

class Google_Address extends Google_Model {
  public $addressLine1;
  public $addressLine2;
  public $addressLine3;
  public $contactName;
  public $countryCode;
  public $kind;
  public $locality;
  public $organizationName;
  public $postalCode;
  public $region;
  public function setAddressLine1($addressLine1) {
    $this->addressLine1 = $addressLine1;
  }
  public function getAddressLine1() {
    return $this->addressLine1;
  }
  public function setAddressLine2($addressLine2) {
    $this->addressLine2 = $addressLine2;
  }
  public function getAddressLine2() {
    return $this->addressLine2;
  }
  public function setAddressLine3($addressLine3) {
    $this->addressLine3 = $addressLine3;
  }
  public function getAddressLine3() {
    return $this->addressLine3;
  }
  public function setContactName($contactName) {
    $this->contactName = $contactName;
  }
  public function getContactName() {
    return $this->contactName;
  }
  public function setCountryCode($countryCode) {
    $this->countryCode = $countryCode;
  }
  public function getCountryCode() {
    return $this->countryCode;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setLocality($locality) {
    $this->locality = $locality;
  }
  public function getLocality() {
    return $this->locality;
  }
  public function setOrganizationName($organizationName) {
    $this->organizationName = $organizationName;
  }
  public function getOrganizationName() {
    return $this->organizationName;
  }
  public function setPostalCode($postalCode) {
    $this->postalCode = $postalCode;
  }
  public function getPostalCode() {
    return $this->postalCode;
  }
  public function setRegion($region) {
    $this->region = $region;
  }
  public function getRegion() {
    return $this->region;
  }
}

class Google_ChangePlanRequest extends Google_Model {
  public $kind;
  public $planName;
  public $purchaseOrderId;
  protected $__seatsType = 'Google_Seats';
  protected $__seatsDataType = '';
  public $seats;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setPlanName($planName) {
    $this->planName = $planName;
  }
  public function getPlanName() {
    return $this->planName;
  }
  public function setPurchaseOrderId($purchaseOrderId) {
    $this->purchaseOrderId = $purchaseOrderId;
  }
  public function getPurchaseOrderId() {
    return $this->purchaseOrderId;
  }
  public function setSeats(Google_Seats $seats) {
    $this->seats = $seats;
  }
  public function getSeats() {
    return $this->seats;
  }
}

class Google_Customer extends Google_Model {
  public $alternateEmail;
  public $customerDomain;
  public $customerId;
  public $kind;
  public $phoneNumber;
  protected $__postalAddressType = 'Google_Address';
  protected $__postalAddressDataType = '';
  public $postalAddress;
  public function setAlternateEmail($alternateEmail) {
    $this->alternateEmail = $alternateEmail;
  }
  public function getAlternateEmail() {
    return $this->alternateEmail;
  }
  public function setCustomerDomain($customerDomain) {
    $this->customerDomain = $customerDomain;
  }
  public function getCustomerDomain() {
    return $this->customerDomain;
  }
  public function setCustomerId($customerId) {
    $this->customerId = $customerId;
  }
  public function getCustomerId() {
    return $this->customerId;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setPhoneNumber($phoneNumber) {
    $this->phoneNumber = $phoneNumber;
  }
  public function getPhoneNumber() {
    return $this->phoneNumber;
  }
  public function setPostalAddress(Google_Address $postalAddress) {
    $this->postalAddress = $postalAddress;
  }
  public function getPostalAddress() {
    return $this->postalAddress;
  }
}

class Google_RenewalSettings extends Google_Model {
  public $kind;
  public $renewalType;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRenewalType($renewalType) {
    $this->renewalType = $renewalType;
  }
  public function getRenewalType() {
    return $this->renewalType;
  }
}

class Google_Seats extends Google_Model {
  public $kind;
  public $maximumNumberOfSeats;
  public $numberOfSeats;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setMaximumNumberOfSeats($maximumNumberOfSeats) {
    $this->maximumNumberOfSeats = $maximumNumberOfSeats;
  }
  public function getMaximumNumberOfSeats() {
    return $this->maximumNumberOfSeats;
  }
  public function setNumberOfSeats($numberOfSeats) {
    $this->numberOfSeats = $numberOfSeats;
  }
  public function getNumberOfSeats() {
    return $this->numberOfSeats;
  }
}

class Google_Subscription extends Google_Model {
  public $creationTime;
  public $customerId;
  public $kind;
  protected $__planType = 'Google_SubscriptionPlan';
  protected $__planDataType = '';
  public $plan;
  public $purchaseOrderId;
  protected $__renewalSettingsType = 'Google_RenewalSettings';
  protected $__renewalSettingsDataType = '';
  public $renewalSettings;
  protected $__seatsType = 'Google_Seats';
  protected $__seatsDataType = '';
  public $seats;
  public $skuId;
  public $subscriptionId;
  protected $__trialSettingsType = 'Google_SubscriptionTrialSettings';
  protected $__trialSettingsDataType = '';
  public $trialSettings;
  public function setCreationTime($creationTime) {
    $this->creationTime = $creationTime;
  }
  public function getCreationTime() {
    return $this->creationTime;
  }
  public function setCustomerId($customerId) {
    $this->customerId = $customerId;
  }
  public function getCustomerId() {
    return $this->customerId;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setPlan(Google_SubscriptionPlan $plan) {
    $this->plan = $plan;
  }
  public function getPlan() {
    return $this->plan;
  }
  public function setPurchaseOrderId($purchaseOrderId) {
    $this->purchaseOrderId = $purchaseOrderId;
  }
  public function getPurchaseOrderId() {
    return $this->purchaseOrderId;
  }
  public function setRenewalSettings(Google_RenewalSettings $renewalSettings) {
    $this->renewalSettings = $renewalSettings;
  }
  public function getRenewalSettings() {
    return $this->renewalSettings;
  }
  public function setSeats(Google_Seats $seats) {
    $this->seats = $seats;
  }
  public function getSeats() {
    return $this->seats;
  }
  public function setSkuId($skuId) {
    $this->skuId = $skuId;
  }
  public function getSkuId() {
    return $this->skuId;
  }
  public function setSubscriptionId($subscriptionId) {
    $this->subscriptionId = $subscriptionId;
  }
  public function getSubscriptionId() {
    return $this->subscriptionId;
  }
  public function setTrialSettings(Google_SubscriptionTrialSettings $trialSettings) {
    $this->trialSettings = $trialSettings;
  }
  public function getTrialSettings() {
    return $this->trialSettings;
  }
}

class Google_SubscriptionPlan extends Google_Model {
  protected $__commitmentIntervalType = 'Google_SubscriptionPlanCommitmentInterval';
  protected $__commitmentIntervalDataType = '';
  public $commitmentInterval;
  public $isCommitmentPlan;
  public $planName;
  public function setCommitmentInterval(Google_SubscriptionPlanCommitmentInterval $commitmentInterval) {
    $this->commitmentInterval = $commitmentInterval;
  }
  public function getCommitmentInterval() {
    return $this->commitmentInterval;
  }
  public function setIsCommitmentPlan($isCommitmentPlan) {
    $this->isCommitmentPlan = $isCommitmentPlan;
  }
  public function getIsCommitmentPlan() {
    return $this->isCommitmentPlan;
  }
  public function setPlanName($planName) {
    $this->planName = $planName;
  }
  public function getPlanName() {
    return $this->planName;
  }
}

class Google_SubscriptionPlanCommitmentInterval extends Google_Model {
  public $endTime;
  public $startTime;
  public function setEndTime($endTime) {
    $this->endTime = $endTime;
  }
  public function getEndTime() {
    return $this->endTime;
  }
  public function setStartTime($startTime) {
    $this->startTime = $startTime;
  }
  public function getStartTime() {
    return $this->startTime;
  }
}

class Google_SubscriptionTrialSettings extends Google_Model {
  public $isInTrial;
  public $trialEndTime;
  public function setIsInTrial($isInTrial) {
    $this->isInTrial = $isInTrial;
  }
  public function getIsInTrial() {
    return $this->isInTrial;
  }
  public function setTrialEndTime($trialEndTime) {
    $this->trialEndTime = $trialEndTime;
  }
  public function getTrialEndTime() {
    return $this->trialEndTime;
  }
}

class Google_Subscriptions extends Google_Model {
  public $kind;
  public $nextPageToken;
  protected $__subscriptionsType = 'Google_Subscription';
  protected $__subscriptionsDataType = 'array';
  public $subscriptions;
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
  public function setSubscriptions(/* array(Google_Subscription) */ $subscriptions) {
    $this->assertIsArray($subscriptions, 'Google_Subscription', __METHOD__);
    $this->subscriptions = $subscriptions;
  }
  public function getSubscriptions() {
    return $this->subscriptions;
  }
}
