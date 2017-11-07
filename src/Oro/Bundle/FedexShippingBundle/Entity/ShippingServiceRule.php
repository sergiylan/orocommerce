<?php

namespace Oro\Bundle\FedexShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oro_fedex_ship_service_rule")
 * @ORM\Entity
 */
class ShippingServiceRule
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="limitation_expression_lbs", type="string", length=250)
     */
    private $limitationExpressionLbs;

    /**
     * @var string
     *
     * @ORM\Column(name="limitation_expression_kg", type="string", length=250)
     */
    private $limitationExpressionKg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="service_type", type="string", length=250, nullable=true)
     */
    private $serviceType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="residential_address", type="boolean")
     */
    private $residentialAddress;

    /**
     * @return integer
     */
    public function getId():int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLimitationExpressionLbs(): string
    {
        return $this->limitationExpressionLbs;
    }

    /**
     * @param string $limitationExpressionLbs
     *
     * @return self
     */
    public function setLimitationExpressionLbs(string $limitationExpressionLbs): self
    {
        $this->limitationExpressionLbs = $limitationExpressionLbs;

        return $this;
    }

    /**
     * @return string
     */
    public function getLimitationExpressionKg(): string
    {
        return $this->limitationExpressionKg;
    }

    /**
     * @param string $limitationExpressionKg
     *
     * @return self
     */
    public function setLimitationExpressionKg(string $limitationExpressionKg): self
    {
        $this->limitationExpressionKg = $limitationExpressionKg;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }

    /**
     * @param null|string $serviceType
     *
     * @return self
     */
    public function setServiceType($serviceType): self
    {
        $this->serviceType = $serviceType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isResidentialAddress(): bool
    {
        return $this->residentialAddress;
    }

    /**
     * @param bool $residentialAddress
     *
     * @return self
     */
    public function setResidentialAddress(bool $residentialAddress): self
    {
        $this->residentialAddress = $residentialAddress;

        return $this;
    }
}
