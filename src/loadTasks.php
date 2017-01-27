<?php

namespace Floe\Robo\Pantheon;


use Floe\Robo\Pantheon\Task\Terminus;
use Floe\Robo\Pantheon\Task\TerminusStack;

trait loadTasks {

  /**
   * Execute a single Terminus command.
   *
   * @param string $command
   *   The Terminus command to execute.
   *
   * @return Terminus
   */
  protected function taskTerminus($command) {
    return new $this->task(Terminus::class);
  }

  /**
   * Run a Terminus command.
   *
   * @param string $command
   *   The Terminus command to execute.
   * @param string[] $params
   *   The parameter for the command to execute.
   * @param string[] $options
   *   The options for the command to execute.
   *
   * @return \Robo\Result
   */
  protected function _terminus($command, $params, $options) {
    return $this->taskTerminus($command)
      ->params($params)
      ->options($options)
      ->run();
  }

  /**
   * Execute a stack of Terminus commands.
   *
   * @return TerminusStack
   */
  protected function taskTerminusStack() {
    return $this->task(TerminusStack::class);
  }

}