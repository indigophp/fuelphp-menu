<?php

/*
 * This file is part of the FuelPHP Menu package.
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
	public $provides = true;

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

		$this->register('menu.provider', function($dic)
		{
			return $dic->resolve('Knp\\Menu\\Provider\\FuelProvider', [$this->container]);
		});

		$this->register('menu.renderer_provider', function($dic, array $renderers = [], $default = null)
		{
			$renderers = array_merge(['fuel' => 'menu.renderer.fuel', 'list' => 'menu.renderer.list'], $renderers);

			return $dic->resolve('Knp\\Menu\\Renderer\\FuelProvider', [$this->container, $renderers, $default]);
		});

		$this->register('menu.renderer.list', function($dic)
		{
			$matcher = $dic->resolve('menu.matcher');

			return $dic->resolve('Knp\\Menu\\Renderer\\ListRenderer', [$matcher]);
		});

		$this->register('menu.renderer.fuel', function($dic)
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

			$viewManager = $app->getViewManager();

			$matcher = $dic->resolve('menu.matcher');

			return $dic->resolve('Knp\\Menu\\Renderer\\FuelRenderer', [$viewManager, 'knp_menu.html.twig', $matcher]);
		});

		$this->extend('parser.twig', function($dic, $instance)
		{
			$rendererProvider = $dic->resolve('menu.renderer_provider');
			$menuProvider = $dic->resolve('menu.provider');

			$helper = $dic->resolve('Knp\\Menu\\Twig\\Helper', [$rendererProvider, $menuProvider]);

			$extension = $dic->resolve('Knp\\Menu\\Twig\\MenuExtension', [$helper]);

			$twig = $instance->getTwig();

			$twig->addExtension($extension);

			return $instance;
		});
	}
}
