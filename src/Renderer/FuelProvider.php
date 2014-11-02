<?php

/*
 * This file is part of the FuelPHP Menu package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Menu\Renderer;

use Fuel\Dependency\Container;

/**
 * Fuel Renderer Provider using Dependency Container
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FuelProvider implements RendererProviderInterface
{
	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var string
	 */
	private $default;

	/**
	 * @var string[]
	 */
	private $renderers = [];

	/**
	 * @param Container $container
	 * @param array     $renderers
	 * @param string    $default
	 */
	public function __construct(
		Container $container,
		array $renderers = [],
		$default = null
	) {
		$this->container = $container;
		$this->renderers = $renderers;
		$this->default = $default;
	}

	/**
	 * Adds a renderer to the list
	 *
	 * @param string $renderer
	 * @param string $name
	 */
	public function add($renderer, $name = null)
	{
		if (is_null($name))
		{
			$name = $renderer;
		}

		$this->renderers[$name] = $renderer;

		return $this;
	}

	/**
	 * Returns the default renderer
	 *
	 * @return string
	 */
	public function getDefault()
	{
		if (is_null($this->default))
		{
			$this->default = reset($this->renderers);
		}

		return $this->default;
	}

	/**
	 * Sets the default renderer
	 *
	 * @param string $default
	 */
	public function setDefault($default)
	{
		$this->default = $default;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($name = null)
	{
		// if no name given
		if (is_null($name))
		{
			$name = $this->getDefault();
		}

		if ($this->has($name)) {
			$name = $this->renderers[$name];
		}

		return $this->container->resolve($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has($name)
	{
		return isset($this->renderers[$name]);
	}
}
