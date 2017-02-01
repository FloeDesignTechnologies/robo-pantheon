<?php

namespace Floe\Robo\Pantheon\Task;

use Floe\Robo\Pantheon\ExecCommand;
use Robo\Result;
use Robo\Task\CommandStack;
use Symfony\Component\Process\ProcessUtils;

/**
 * Task to execute a stack of Terminus commands.
 *
 * @package Floe\Robo\Pantheon\Task
 */
class TerminusStack extends CommandStack {

  use ExecCommand;

  /**
   * TerminusStack constructor.
   * @param string[] $knownCommands
   * @param string $pathToTerminus
   */
  public function __construct($pathToTerminus = 'terminus') {
    $this->executable = $pathToTerminus;
  }

  /**
   * Returns the map from method name to command
   *
   * The available command are dynamically discovered using `terminus list --raw`.
   *
   * @return array
   *   A map of method names to terminus commands.
   */
  protected function getCommandsMap() {
    static $commandMaps;
    if (!isset($commandMaps)) {
      /** @var Result $result */
      $printed = $this->isPrinted;
      $this->printed(false);
      $result = $this->executeCommand("{$this->executable} list --raw");
      $this->printed($printed);
      $knownCommands = array_map(function ($command) {
        return explode(' ', $command, 2)[0];
      }, explode(PHP_EOL, $result->getOutputData()));
      $commandMaps = array_combine(preg_replace('/(?::|-)([[:alpha:]])/', '$1', $knownCommands), $knownCommands);
    }
    return $commandMaps;
  }

  protected static function escapeArgument($argument) {
    if (!preg_match('/^[a-zA-Z0-9\/\.@~_-]+$/', $argument)) {
      $argument = ProcessUtils::escapeArgument($argument);
    }
    return $argument;
  }

  /**
   * Enqueue a Terminus command to the Stack.
   *
   * Parameter and options values are automatically escaped
   *
   * Options are prefixed with `--` , value can be provided in second parameter. Boolean values* are used as option
   * toggles (the option will only be present of the boolean is true).
   *
   * @param string $command
   *   A terminus command.
   * @param string[] $params
   *   An array of command parameters
   * @param string[] $options
   *   An array of option value, indexed by name.
   *
   * @return $this
   */
  public function exec($command, $params = [], $options = []) {
    return parent::exec(array_merge((array) $command, array_filter(array_map([$this, 'escapeArgument'], (array) $params)), array_filter(array_map(function ($option, $value) {
      if ($value === FALSE) {
        return FALSE;
      }
      if ($option !== null and strpos($option, '-') !== 0) {
        $option = "--$option";
      }
      if (!is_bool($value)) {
        $option .= null == $value ? '' : "=" . static::escapeArgument($value);
      }
      return $option;
    }, array_keys($options), $options))));
  }

  public function __call($name, $arguments) {
    $name = strtolower($name);
    $commandsMaps = $this->getCommandsMap();
    if (!empty($commandsMaps[$name])) {
      $params = isset($arguments[0]) ? $arguments[0] : [];
      $options = isset($arguments[1]) ? $arguments[1] : [];
      return $this->exec($commandsMaps[$name], $params, $options);
    }
    else {
      trigger_error('Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR);
    }
  }


}
