<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Validator\Constraints
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Homepage extends Constraint
{
    /**
     * @var string
     */
    public $invalidPosition = 'The position of the cells cannot be greater to the number of cells: {{ maximum }}';

    /**
     * @var string
     */
    public $duplicateSlug = 'The cell slug {{ slug }} is already taken';

    /**
     * Returns the name of the class that validates this constraint
     *
     * @return string
     */
    public function validatedBy()
    {
        return HomepageValidator::class;
    }

    /**
     * Get the class name to be validated
     *
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
