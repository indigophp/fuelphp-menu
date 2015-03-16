<?php

/*
 * This file is part of the Fuel Menu package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Menu\Providers;

use League\Container\ServiceProvider;
use Knp\Menu;

/**
 * Provides menu services
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FuelServiceProvider extends ServiceProvider
{
	/**
	 * @var array
	 */
	protected $provides = [
		'menu',
		'menu.factory',
		'menu.matcher',
		'menu.loader.array',
		'menu.provider',
		'menu.renderer_provider',
		'menu.renderer.list',
		'menu.renderer.fuel',
		'menu.twig.helper',
		'menu.twig.extension',
	];

	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		$this->container->add('menu', function($title = null, array $options = [])
		{
			$factory = $this->container->get('menu.factory');

			return $factory->createItem($title, $options);
		});

		$this->container->singleton('menu.factory', 'Knp\\Menu\\MenuFactory');
		$this->container->add('menu.matcher', 'Knp\\Menu\\Matcher\\Matcher');

		$this->container->add('menu.loader.array', 'Knp\\Menu\\Loader\\ArrayLoader')
			->withArgument('menu.factory');

		$this->container->add('menu.provider', function(array $menus = [])
		{
			$config = $this->container->get('configInstance', [false]);
			$config->load('menu', true);

			$menus = array_merge($config->get('menu.menus', []), $menus);

			return new Menu\Provider\FuelProvider($this->container, $menus);
		});

		$this->container->add('menu.renderer_provider', function(array $renderers = [], $default = null)
		{
			$config = $this->container->get('configInstance', [false]);
			$config->load('menu', true);

			$renderers = array_merge(
				['fuel' => 'menu.renderer.fuel', 'list' => 'menu.renderer.list'],
				$config->get('menu.renderers', []),
				$renderers
			);

			$default = $default ?: $config->get('menu.default_renderer');

			return new Menu\Renderer\FuelProvider($this->container, $renderers, $default);
		});

		$this->container->add('menu.renderer.list', 'Knp\\Menu\\Renderer\\ListRenderer')
			->withArgument('menu.matcher');

		$this->container->add('menu.renderer.fuel', function(array $defaultOptions = [])
		{
			$matcher = $this->container->get('menu.matcher');

			return new Menu\Renderer\FuelRenderer($matcher, $defaultOptions);
		});

		$this->container->add('menu.twig.helper', 'Knp\\Menu\\Twig\\Helper')
			->withArgument('menu.renderer_provider')
			->withArgument('menu.provider');

		$this->container->add('menu.twig.extension', 'Knp\\Menu\\Twig\\MenuExtension')
			->withArgument('menu.twig.helper');
	}
}
