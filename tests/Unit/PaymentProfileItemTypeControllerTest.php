<?php

namespace Tests\Unit;

use App\PaymentProfileItemType;
use Tests\Helpers\Auth\SetupUser;
use Tests\TestCase;

class PaymentProfileItemTypeControllerTest extends TestCase
{
  use SetupUser;
  /**
   * A basic unit test example.
   *
   * @return void
   */
  public function testGetsListOfDefaultPaymentProfileItemTypes()
  {
    $response = $this->actingAs($this->user)
      ->getJson('api/v1/payment_profile_item_types');

    $response->assertStatus(200)
      ->assertJson([
        ['name' => 'administrative'],
        ['name' => 'tuition'],
      ]);
  }
}
