<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Type;

use Symfony\Component\Validator\ExecutionContextInterface;

/**
 *
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 *
 */
abstract class BaseType implements TypeInterface
{
    abstract public function getType();
    abstract public function isValid($value);
    abstract public function getFormattedValue($value);
    abstract public function getFormType();
    abstract public function getFormOptions();

    public function verifyForm($value, ExecutionContextInterface $context)
    {
        if (!$this->isValid($value)) {
            $context->addViolation(sprintf("received value `%s` does not match `%s` type", $value, $this->getType()));
        }
    }
}
