<?php

namespace Tests\Unit\Traits;

use App\Models\Activity;
use Tests\TestCase;

class AdditionalDataTest extends TestCase
{

    /** @test */
    public function single_additional_data_can_be_set_and_retrieved()
    {
        $activity = Activity::factory()->create();
        $activity->setAdditionalData('demo-data', 'This is some demo data');
        $this->assertEquals('This is some demo data', $activity->getAdditionalData('demo-data'));
    }

    /** @test */
    public function it_loads_additional_data_into_an_array_of_the_objec_by_default()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function additional_data_is_deleted_when_the_model_is_deleted()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function set_additional_data_creates_new_additional_data_if_key_does_not_exist()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function set_additional_data_updates_existing_additional_data_if_key_exists()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function push_to_additional_data_array_creates_a_new_array_item()
    {
        $this->markTestIncomplete();
        // Must check it's actually an array and not one item
    }

    /** @test */
    public function push_to_additional_data_array_adds_an_extra_item_to_an_array()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function set_additional_data_array_creates_a_new_array_of_data()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function set_additional_data_array_overwrites_any_old_array()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function get_additional_data_gets_a_single_value()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function get_additional_data_gets_an_array_of_data()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function get_additional_data_returns_the_default_if_key_not_set()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function has_additional_data_returns_false_if_key_does_not_exist()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function has_additional_data_returns_true_if_key_does_exist()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function get_all_additional_data_returns_an_array_of_all_additional_data()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function where_has_additional_data_scopes_to_activities_which_have_the_additional_data()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function where_additional_data_scopes_to_matching_additional_data()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function where_additional_data_array_contains_scopes_to_an_additional_data_array_element()
    {
        $this->markTestIncomplete();
    }
}
