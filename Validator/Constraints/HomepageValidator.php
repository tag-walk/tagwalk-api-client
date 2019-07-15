<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Tagwalk\ApiClientBundle\Model\Homepage;

/**
 * Validate the Homepage class.
 */
class HomepageValidator extends ConstraintValidator
{
    /**
     * Validate the homepage properties.
     *
     * {@inheritdoc}
     *
     * @param Homepage                                                $homepage
     * @param \Tagwalk\ApiClientBundle\Validator\Constraints\Homepage $constraint
     */
    public function validate($homepage, Constraint $constraint)
    {
        $this->validateCellsPositions($homepage, $constraint);
        $this->validateCellsSlug($homepage, $constraint);
    }

    /**
     * Validate that the homepage cells positions are not greater than the number of cells.
     *
     * @param Homepage                                                           $homepage
     * @param Constraint|\Tagwalk\ApiClientBundle\Validator\Constraints\Homepage $constraint
     */
    private function validateCellsPositions(Homepage $homepage, Constraint $constraint)
    {
        if (null === $cells = $homepage->getCells()) {
            return;
        }
        $max = count($cells) - 1;
        foreach ($cells as $cell) {
            if ($cell->getPosition() > $max) {
                $this
                    ->context
                    ->buildViolation($constraint->invalidPosition, ['{{ maximum }}' => $max])
                    ->atPath('cells')
                    ->addViolation();
            }
        }
    }

    /**
     * Validate that the homepage cells positions are not greater than the number of cells.
     *
     * @param Homepage                                                           $homepage
     * @param Constraint|\Tagwalk\ApiClientBundle\Validator\Constraints\Homepage $constraint
     */
    private function validateCellsSlug(Homepage $homepage, Constraint $constraint)
    {
        if (null === $cells = $homepage->getCells()) {
            return;
        }
        $slugs = [];
        foreach ($cells as $cell) {
            if (in_array($cell->getSlug(), $slugs)) {
                $this
                    ->context
                    ->buildViolation($constraint->duplicateSlug, ['{{ slug }}' => $cell->getSlug()])
                    ->atPath('cells')
                    ->addViolation();
            }
            $slugs[] = $cell->getSlug();
        }
    }
}
