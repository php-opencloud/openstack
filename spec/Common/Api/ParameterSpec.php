<?php

namespace spec\OpenStack\Common\Api;

use OpenStack\Compute\v2\Api as ComputeV2Api;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParameterSpec extends ObjectBehavior
{
    const PARAMETER_CLASS = 'OpenStack\Common\Api\Parameter';

    private $data;

    function let()
    {
        $this->data = ComputeV2Api::postServer()['params']['name'] + ['name' => 'name'];

        $this->beConstructedWith($this->data);
    }

    function it_should_provide_access_to_a_name()
    {
        $this->getName()->shouldReturn($this->data['name']);
    }

    function it_should_use_sentAs_alias_for_name_if_one_is_set()
    {
        $data = $this->data + ['sentAs' => 'foo'];
        $this->beConstructedWith($data);

        $this->getName()->shouldReturn($data['sentAs']);
    }

    function it_indicates_whether_it_is_required_or_not()
    {
        $this->shouldBeRequired();
    }

    function it_indicates_its_item_schema()
    {
        $this->beConstructedWith(ComputeV2Api::postServer()['params']['networks'] + ['name' => 'networks']);
        $this->getItemSchema()->shouldReturnAnInstanceOf(self::PARAMETER_CLASS);
    }

    function it_allows_property_retrieval()
    {
        $definition = ComputeV2Api::postServer()['params']['networks']['items'] + ['name' => 'network'];
        $this->beConstructedWith($definition);

        $this->getProperty('uuid')->shouldReturnAnInstanceOf(self::PARAMETER_CLASS);
    }

    function it_indicates_its_path()
    {
        $path = 'foo.bar.baz';

        $this->beConstructedWith($this->data + ['path' => $path]);

        $this->getPath()->shouldReturn($path);
    }

    function it_verifies_a_given_location_with_a_boolean()
    {
        $this->hasLocation('foo')->shouldReturn(false);
        $this->hasLocation('json')->shouldReturn(true);
    }

    function it_should_return_true_when_required_attributes_are_provided_and_match_their_definitions()
    {
        $this->validate('TestName')->shouldReturn(true);
    }

    function it_throws_exception_when_values_do_not_match_their_definition_types()
    {
        $userData = 'a_network!'; // should be an array

        $this->beConstructedWith(ComputeV2Api::postServer()['params']['networks'] + ['name' => 'networks']);

        $this->shouldThrow('\Exception')->duringValidate($userData);
    }

    function it_throws_exception_when_deeply_nested_values_have_wrong_types()
    {
        $userData = [
            'name' => false // should be a string
        ];

        $this->beConstructedWith(ComputeV2Api::postServer()['params']['networks'] + ['name' => 'networks']);

        $this->shouldThrow('\Exception')->duringValidate($userData);
    }
}