<?php

namespace Floe\Robo\Pantheon\Task;

use Floe\Robo\Pantheon\ExecOneCommand;
use Robo\Contract\CommandInterface;
use Robo\Contract\PrintedInterface;
use Robo\Task\BaseTask;

/**
 * Task to execute one Terminus command.
 *
 * @package Floe\Robo\Pantheon\Task
 */
class Terminus extends BaseTask implements CommandInterface, PrintedInterface {

  use ExecOneCommand;

  protected $params;

  protected $options;

  public function __construct($command, $terminusPath = 'terminus') {
    if (preg_match('/\s/', $command)) {
      throw new \InvalidArgumentException(__CLASS__ . ' command must be a single word.');
    }
    $this->command = $terminusPath;
    $this->arg($command);
  }

  public function run() {
    $this->printTaskInfo('Running terminus {arguments}', ['arguments' => $this->arguments]);
    return $this->executeCommand($this->getCommand());
  }

  /**
   * {@inheritdoc}
   */
  public function getCommand() {
    return trim($this->command . $this->arguments);
  }

  /**
   * Pass parameter to terminus. Its value will be automatically escaped.
   *
   * @param string $param
   *
   * @return $this
   */
  public function param($param) {
    return $this->params($param);
  }

  /**
   * Pass methods parameters as parameters to terminus. Parameter values
   * are automatically escaped.
   *
   * @param string|string[] $args
   *
   * @return $this
   */
  public function params($params) {
    if (!is_array($params)) {
      $params = func_get_args();
    }
    return $this->args($params);
  }

  /**
   * Pass option to terminus. Options are prefixed with `--` , value can be provided in second parameter. Boolean values
   * are used as option toggles (the option will only be present of the boolean is true).
   *
   * @param string $option
   * @param string $value
   *
   * @return $this
   */
  public function option($option, $value = null) {
    if ($value !== FALSE) {
      if ($option !== null and strpos($option, '-') !== 0) {
        $option = "--$option";
      }
      $this->arguments .= null == $option ? '' : " " . $option;
      if (!is_bool($value)) {
        $this->arguments .= null == $value ? '' : "=" . static::escape($value);
      }
    }
    return $this;
  }

  /**
   * @param $options
   * @return $this
   */
  public function options($options) {
    foreach ($options as $option => $value) {
      $this->option($option, $value);
    }
    return $this;
  }
}
