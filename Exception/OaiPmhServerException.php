<?php

namespace Ftven\Bundle\OaiPmhServerBundle\Exception;

use Symfony\Component\DependencyInjection\Exception\ExceptionInterface;

abstract class OaiPmhServerException extends \Exception implements ExceptionInterface
{
}
