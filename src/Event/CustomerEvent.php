<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\RobinHqLib\Event;


use Emico\RobinHqLib\Model\Customer;

final class CustomerEvent implements EventInterface
{
    /**
     * @param Customer $customer
     */
    public function __construct(protected Customer $customer)
    {
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return 'customer';
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->customer;
    }
}