<?php 

declare(strict_types=1);

namespace Manager\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Manager\Controller\AuthController;
use Laminas\Db\Adapter\Adapter;
use Manager\Model\Table\UsersTable;
use Manager\Model\Table\AuthenticationTable;
use Manager\Model\Table\RolesTable;

class AuthControllerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		return new AuthController(
				$container->get(Adapter::class),
				$container->get(UsersTable::class),
				$container->get(AuthenticationTable::class),
				$container->get(RolesTable::class)
		);
	}
}