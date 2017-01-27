<?php

namespace spec\Floe\Robo\Pantheon\Task;

use Floe\Robo\Pantheon\Task\TerminusStack;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Robo\Result;
use Robo\Task\CommandStack;
use spec\Floe\CallableInterface;

class TerminusStackSpec extends ObjectBehavior {

  function let(LoggerInterface $logger, CallableInterface $executeCommand) {
    $this->beConstructedThrough(function () use ($logger, $executeCommand) {
      $terminusStack = new TerminusStack('terminus');
      $terminusStack->setLogger($logger->getWrappedObject());
      $terminusStack->overrideExecuteCommand($executeCommand->getWrappedObject());
      $executeCommand->__invoke('terminus list --raw')
        ->willReturn(new Result($terminusStack, 0, <<<EOF
foo:bar-baz:qux Foo bar-baz quz
foo:bar         Foo bar
EOF
, ['time' => 0]));
      return $terminusStack;
    });
  }

  function it_is_initializable() {
      $this->shouldHaveType(TerminusStack::class);
  }

  function it_should_be_a_command_stack() {
    $this->shouldBeAnInstanceOf(CommandStack::class);
  }

  function it_should_run(CallableInterface $executeCommand) {
    $executeCommand->__invoke('terminus foo')->shouldBeCalled();
    $this->exec('foo')
      ->run();
  }

  function it_should_accept_path_to_executable_in_constructor(LoggerInterface $logger, CallableInterface $executeCommand) {
    $this->beConstructedThrough(function () use ($logger, $executeCommand) {
      $terminusStack = new TerminusStack('path/to/my/terminus');
      $terminusStack->setLogger($logger->getWrappedObject());
      $terminusStack->overrideExecuteCommand($executeCommand->getWrappedObject());
      return $terminusStack;
    });
    $executeCommand->__invoke('path/to/my/terminus foo')->shouldBeCalled();
    $this->exec('foo')
      ->run();
  }

  function it_should_accept_exec_parameters_and_options() {
    $this->exec('foo', ['param1', 'param 2'], ['foo' => 'bar', 'bar' => true, 'baz' => false])
      ->getCommand()
      ->shouldBe("terminus foo param1 'param 2' --foo=bar --bar");
  }

  function it_should_map_methods_to_commands(CallableInterface $executeCommand) {
    $this->fooBarBazQux(['param1', 'param2'], ['foo' => 'bar', 'bar' => true, 'baz' => false])
      ->fooBar()
      ->getCommand()
      ->shouldBe('terminus foo:bar-baz:qux param1 param2 --foo=bar --bar && terminus foo:bar');
  }

  function it_should_trigger_an_error_when_command_is_unknwon() {
    $this->shouldTrigger(E_USER_ERROR)
      ->duringBarFoo();
  }
}
