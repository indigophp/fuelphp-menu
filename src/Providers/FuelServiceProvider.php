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

use Fuel\Dependency\ServiceProvider;

/**
 * Provides menu services
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FuelServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritdoc}
	 */
	public $provides = [
		'menu',
		'menu.factory',
		'menu.matcher',
		'menu.loader.array',
		'menu.provider',
		'menu.renderer_provider',
		'menu.renderer.list',
		'menu.renderer.fuel'
	];

	/**
	 * {@inheritdoc}
	 */
	public function provide()
	{
		$this->register('menu', function($dic, $title = null, array $options = [])
		{
			$factory = $dic->resolve('menu.factory');

			return $factory->createItem($title, $options);
		});

		$this->registerSingleton('menu.factory', 'Knp\\Menu\\MenuFactory');
		$this->register('menu.matcher', 'Knp\\Menu\\Matcher\\Matcher');

		$this->register('menu.loader.array', function($dic)
		{
			$factory = $dic->resolve('menu.factory');

			return $dic->resolve('Knp\\Menu\\Loader\\ArrayLoader', [$factory]);
		});

		$this->register('menu.provider', function($dic, array $menus = [])
		{
			$config = $this->getApp()->getConfig();
			$config->load('menu', true);
			$menus = array_merge($config->get('menu.menus', []), $menus);

			return $dic->resolve('Knp\\Menu\\Provider\\FuelProvider', [$this->container, $menus]);
		});

		$this->register('menu.renderer_provider', function($dic, array $renderers = [], $default = null)
		{
			$config = $this->getApp()->getConfig();
			$config->load('menu', true);
			$renderers = array_merge(
				['fuel' => 'menu.renderer.fuel', 'list' => 'menu.renderer.list'],
				$config->get('menu.renderers', []),
				$renderers
			);

			$default = $default ?: $config->get('menu.default_renderer');

			return $dic->resolve('Knp\\Menu\\Renderer\\FuelProvider', [$this->container, $renderers, $default]);
		});

		$this->register('menu.renderer.list', function($dic)
		{
			$matcher = $dic->resolve('menu.matcher');

			return $dic->resolve('Knp\\Menu\\Renderer\\ListRenderer', [$matcher]);
		});

		$this->register('menu.renderer.fuel', function($dic, array $defaultOptions = [])
		{
			$matcher = $dic->resolve('menu.matcher');

			return $dic->resolve('Knp\\Menu\\Renderer\\FuelRenderer', [$matcher, $defaultOptions]);
		});

		$this->extend('menu.renderer.fuel', 'getViewManagerInstance');
	}

	/**
	 * Returns the application
	 *
	 * @return \Fuel\Foundation\Application
	 */
	private function getApp()
	{
		$stack = $container->resolve('requeststack');

		if ($request = $stack->top())
		{
			$app = $request->getComponent()->getApplication();
		}
		else
		{
			$app = $container->resolve('application::__main');
		}

		return $app;
	}
}
