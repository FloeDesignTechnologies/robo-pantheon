<?php

namespace spec\Floe\Robo\Pantheon\Task;

use Floe\Robo\Pantheon\Task\Terminus;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use spec\Floe\CallableInterface;

class TerminusSpec extends ObjectBehavior {

  function let(LoggerInterface $logger, CallableInterface $executeCommand) {
    $this->beConstructedThrough(function () use ($logger, $executeCommand) {
      $terminus = new Terminus('foo');
      $terminus->setLogger($logger->getWrappedObject());
      $terminus->overrideExecuteCommand($executeCommand->getWrappedObject());
      return $terminus;
    });
  }

  function it_is_initializable() {
    $this->shouldHaveType(Terminus::class);
  }

  function it_should_run(CallableInterface $executeCommand) {
    $executeCommand->__invoke('terminus foo')->shouldBeCalled();
    $this->run();
  }

  function it_should_execute_terminus_command_from_constructor() {
    $this->getCommand()
      ->shouldBe('terminus foo');
  }

  function it_should_only_accept_a_single_word_as_command() {
    $this->beConstructedWith('foo bar');
    $this->shouldThrow('\InvalidArgumentException')
      ->duringInstantiation();
  }

  function it_should_pass_parameter_to_terminus() {
    $this->param('param1')
      ->param('param 2')
      ->getCommand()->shouldBe("terminus foo param1 'param 2'");
  }

  function it_should_pass_multiple_parameters_to_terminus() {
    $this->params('param1', 'param 2')
      ->params(['param3', 'param 4'])
      ->getCommand()->shouldBe("terminus foo param1 'param 2' param3 'param 4'");
  }

  function it_should_pass_option_to_terminus() {
    $this->option('foo', 'bar')
      ->getCommand()
      ->shouldBe('terminus foo --foo=bar');
  }

  function it_should_use_boolean_option_value_as_toggle() {
    $this->option('bar', true)
      ->option('baz', false)
      ->getCommand()
      ->shouldBe('terminus foo --bar');
  }

  function it_should_pass_multiple_options_to_terminus() {
    $this->options(['foo' => 'bar', 'bar' => true, 'baz' => false])
      ->getCommand()
      ->shouldBe('terminus foo --foo=bar --bar');
  }

  function it_should_mix_options_and_parameters() {
    $this->option('foo', 'bar')
      ->params('param1', 'param2')
      ->option('bar', 'foo')
      ->param('param3')
      ->getCommand()
      ->shouldBe('terminus foo --foo=bar param1 param2 --bar=foo param3');
  }

  function it_should_accept_path_to_terminus_in_constructor() {
    $this->beConstructedWith('foo', 'path/to/my/terminus');
    $this->getCommand()
      ->shouldBe('path/to/my/terminus foo');

  }
}
