<?php
/**
 * Created by PhpStorm.
 * User: buyle
 * Date: 27/01/17
 * Time: 3:13 PM
 */

namespace Floe\Robo\Pantheon;

/**
 * This task is supposed to be executed as shell command.
 * You can specify working directory and if output is printed.
 */
trait ExecCommand {

  use \Robo\Common\ExecCommand {
    executeCommand as protected defaultExecuteCommand;
  }

  /**
   * {@inheritdoc}
   */
  public function executeCommand($command) {
    return isset($this->executeCommandOverride) ? call_user_func($this->executeCommandOverride, $command) : $this->defaultExecuteCommand($command);
  }

  /**
   * Override shell command execution.
   *
   * @param callable $executeCommand
   *   A callable with the same signature as executeCommand.
   *
   * @see executeCommand
   */
  public function overrideExecuteCommand(callable $executeCommand) {
    $this->executeCommandOverride = $executeCommand;
  }

}