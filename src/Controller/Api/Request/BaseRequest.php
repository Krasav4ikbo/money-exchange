<?php
namespace App\Controller\Api\Request;

use Symfony\Component\HttpFoundation\Request;

abstract class BaseRequest
{
    public function __construct()
    {
        $this->populate();
    }

    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }

    protected function populate(): void
    {
        foreach ($this->getRequest()->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }
}
