<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

trait Labelable
{
    /**
     * @var string[]|null
     */
    protected $labels;

    /**
     * @var string[]|null
     */
    protected $labelsFr;

    /**
     * @var string[]|null
     */
    protected $labelsEs;

    /**
     * @var string[]|null
     */
    protected $labelsIt;

    /**
     * @var string[]|null
     */
    protected $labelsZh;

    /**
     * @return string[]|null
     */
    public function getLabels(): ?array
    {
        return $this->labels;
    }

    /**
     * @param string[]|null $labels
     *
     * @return self
     */
    public function setLabels(?array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLabelsFr(): ?array
    {
        return $this->labelsFr;
    }

    /**
     * @param string[]|null $labelsFr
     *
     * @return self
     */
    public function setLabelsFr(?array $labelsFr): self
    {
        $this->labelsFr = $labelsFr;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLabelsEs(): ?array
    {
        return $this->labelsEs;
    }

    /**
     * @param string[]|null $labelsEs
     *
     * @return self
     */
    public function setLabelsEs(?array $labelsEs): self
    {
        $this->labelsEs = $labelsEs;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLabelsIt(): ?array
    {
        return $this->labelsIt;
    }

    /**
     * @param string[]|null $labelsIt
     *
     * @return self
     */
    public function setLabelsIt(?array $labelsIt): self
    {
        $this->labelsIt = $labelsIt;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLabelsZh(): ?array
    {
        return $this->labelsZh;
    }

    /**
     * @param string[]|null $labelsZh
     *
     * @return self
     */
    public function setLabelsZh(?array $labelsZh): self
    {
        $this->labelsZh = $labelsZh;

        return $this;
    }
}
