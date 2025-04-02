<?php 

declare(strict_types=1);

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Controller\IndexController;
use Application\Model\HRMTable;
use Application\Model\AuthenticationTable;

class IndexControllerFactory implements FactoryInterface
{
	private $containerUser;
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		return new IndexController(
			$container->get(HRMTable::class),
			$container->get(AuthenticationTable::class),
		);
	}
}
