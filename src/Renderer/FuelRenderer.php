<?php

/*
 * This file is part of the Fuel Menu package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Menu\Renderer;

use Fuel\Display\ViewManager;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;

/**
 * Render menu using ViewManager
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FuelRenderer implements RendererInterface
{
	/**
	 * @var ViewManager
	 */
	private $viewManager;

	/**
	 * @var MatcherInterface
	 */
	private $matcher;

	/**
	 * @var array
	 */
	private $defaultOptions = [
		'depth'             => null,
		'matchingDepth'     => null,
		'currentAsLink'     => true,
		'currentClass'      => 'current',
		'ancestorClass'     => 'current_ancestor',
		'firstClass'        => 'first',
		'lastClass'         => 'last',
		'template'          => null,
		'compressed'        => false,
		'allow_safe_labels' => false,
		'clear_matcher'     => true,
		'leaf_class'        => null,
		'branch_class'      => null,
	];

	/**
	 * @param string           $template
	 * @param MatcherInterface $matcher
	 * @param array            $defaultOptions
	 */
	public function __construct(
		MatcherInterface $matcher,
		array $defaultOptions = []
	) {
		$this->matcher = $matcher;
		$this->defaultOptions = array_merge($this->defaultOptions, $defaultOptions);
	}

	/**
	 * Sets a ViewManager
	 *
	 * @param ViewManager $viewManager
	 */
	public function setViewManager(ViewManager $viewManager)
	{
		$this->viewManager = $viewManager;
	}

	/**
	 * Sets the default template
	 *
	 * @param string $template
	 */
	public function setDefaultTemplate($template)
	{
		$this->defaultOptions['template'] = $template;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(ItemInterface $item, array $options = [])
	{
		$options = array_merge($this->defaultOptions, $options);

		$view = $this->viewManager->forge(
			$options['template'],
			[
				'item'    => $item,
				'options' => $options,
				'matcher' => $this->matcher
			]
		);

		if ($options['clear_matcher']) {
			$this->matcher->clear();
		}

		return $view->render();
	}
}
