<?php

namespace Spotler;

class Loader {
	protected $actions = [];
	protected $filters = [];

	/**
	 * @param    string $action         The name of the WordPress action that is being registered.
	 * @param    object $component      A reference to the instance of the object on which the action is defined.
	 * @param    string $callback       The name of the function definition on the $component.
	 * @param    int    $priority       Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $amountArgs     Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function addAction( string $action, $component, string $callback, int $priority = 10, int $amountArgs = 1 ): void {
		$this->actions = $this->addHook( $this->actions, $action, $component, $callback, $priority, $amountArgs );
	}

	/**
	 * @param    string $filter         The name of the WordPress filter that is being registered.
	 * @param    object $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback       The name of the function definition on the $component.
	 * @param    int    $priority       Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $amountArgs     Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function addFilter( $filter, $component, string $callback, int $priority = 10, int $amountArgs = 1 ): void {
		$this->filters = $this->addHook( $this->filters, $filter, $component, $callback, $priority, $amountArgs );
	}

	public function runHooks(): void {
		foreach( $this->actions as $action ) {
			\add_action( $action['hook'], [ $action['component'], $action['callback'] ], $action['priority'], $action['amount_args'] );
		}
		foreach( $this->filters as $filter ) {
			\add_action( $filter['hook'], [ $filter['component'], $filter['callback'] ], $filter['priority'], $filter['amount_args'] );
		}
	}

	private function addHook( array $hooks, string $hook, $component, string $callback, int $priority, int $amountArgs ): array {
		$hooks[] = [ 'hook' => $hook, 'component' => $component, 'callback' => $callback, 'priority' => $priority, 'amount_args' => $amountArgs ];

		return $hooks;
	}
}