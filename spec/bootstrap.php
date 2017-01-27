<?php

namespace spec\Floe;

interface CallableInterface {
 public function __invoke();
}

$container = \Robo\Robo::createDefaultContainer();