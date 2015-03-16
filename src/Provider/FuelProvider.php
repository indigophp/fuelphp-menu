<?php

/*
 * This file is part of the Fuel Menu package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Menu\Provider;

use Fuel\Dependency\Container;

/**
 * Fuel Menu Provider using Dependency Container
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FuelProvider implements MenuProviderInterface
{
	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var array
	 */
	private $menus = [];

	/**
	 * @param Container $container
	 */
	public function __construct(Container $container, array $menus = [])
	{
		$this->container = $container;
		$this->menus = $menus;
	}

	/**
	 * Adds a menu
	 *
	 * @param string $menu
	 * @param string $name
	 */
	public function add($menu, $name)
	{
		$this->menus[$menu] = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($name, array $options = [])
	{
		if (isset($this->menus[$name]))
		{
			$name = $this->menus[$name];
		}

		return $this->container->multiton('menu', $name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has($name, array $options = [])
	{
		return isset($this->menus[$name]) or $this->container->isInstance('menu', $name);
	}
}
