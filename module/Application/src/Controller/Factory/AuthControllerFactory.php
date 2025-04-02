<?php 

declare(strict_types=1);

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Controller\AuthController;
use Laminas\Db\Adapter\Adapter;
use Application\Model\UsersTable;
use Application\Model\AuthenticationTable;

class AuthControllerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		return new AuthController(
				$container->get(Adapter::class),
				$container->get(UsersTable::class),
				$container->get(AuthenticationTable::class)
		);
	}
}