<?php
/**
 * Created by PhpStorm.
 * User: buyle
 * Date: 27/01/17
 * Time: 3:17 PM
 */

namespace Floe\Robo\Pantheon;

use Robo\Common\CommandArguments;

/**
 * This task specifies exactly one shell command.
 * It can take additional arguments and options as config parameters.
 */
trait ExecOneCommand {
  use ExecCommand;
  use CommandArguments;
}