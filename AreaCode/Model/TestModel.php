<?php

namespace CertificationPrep\AreaCode\Model;

use \Magento\Framework\Api\AbstractSimpleObject;

class TestModel extends AbstractSimpleObject
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->_data;
    }
}
