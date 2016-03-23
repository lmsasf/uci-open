<?php

namespace Asf\Service;

interface IService
{

    static function getType();

    static function factory(array $options = array());

    function getOptions();
}
