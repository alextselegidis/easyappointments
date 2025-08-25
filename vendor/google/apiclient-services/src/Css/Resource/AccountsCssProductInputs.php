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

namespace Google\Service\Css\Resource;

use Google\Service\Css\CssEmpty;
use Google\Service\Css\CssProductInput;

/**
 * The "cssProductInputs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cssService = new Google\Service\Css(...);
 *   $cssProductInputs = $cssService->accounts_cssProductInputs;
 *  </code>
 */
class AccountsCssProductInputs extends \Google\Service\Resource
{
  /**
   * Deletes a CSS Product input from your CSS Center account. After a delete it
   * may take several minutes until the input is no longer available.
   * (cssProductInputs.delete)
   *
   * @param string $name Required. The name of the CSS product input resource to
   * delete. Format: accounts/{account}/cssProductInputs/{css_product_input}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string supplementalFeedId The Content API Supplemental Feed ID.
   * The field must not be set if the action applies to a primary feed. If the
   * field is set, then product action applies to a supplemental feed instead of
   * primary Content API feed.
   * @return CssEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], CssEmpty::class);
  }
  /**
   * Uploads a CssProductInput to your CSS Center account. If an input with the
   * same contentLanguage, identity, feedLabel and feedId already exists, this
   * method replaces that entry. After inserting, updating, or deleting a CSS
   * Product input, it may take several minutes before the processed CSS Product
   * can be retrieved. (cssProductInputs.insert)
   *
   * @param string $parent Required. The account where this CSS Product will be
   * inserted. Format: accounts/{account}
   * @param CssProductInput $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string feedId Optional. The primary or supplemental feed id. If
   * CSS Product already exists and feed id provided is different, then the CSS
   * Product will be moved to a new feed. Note: For now, CSSs do not need to
   * provide feed ids as we create feeds on the fly. We do not have supplemental
   * feed support for CSS Products yet.
   * @return CssProductInput
   * @throws \Google\Service\Exception
   */
  public function insert($parent, CssProductInput $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], CssProductInput::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsCssProductInputs::class, 'Google_Service_Css_Resource_AccountsCssProductInputs');
