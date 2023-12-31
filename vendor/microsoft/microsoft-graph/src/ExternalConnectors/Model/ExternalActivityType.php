<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* ExternalActivityType File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
namespace Microsoft\Graph\ExternalConnectors\Model;

use Microsoft\Graph\Core\Enum;

/**
* ExternalActivityType class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class ExternalActivityType extends Enum
{
    /**
    * The Enum ExternalActivityType
    */
    const VIEWED = "viewed";
    const MODIFIED = "modified";
    const CREATED = "created";
    const COMMENTED = "commented";
    const UNKNOWN_FUTURE_VALUE = "unknownFutureValue";
}
