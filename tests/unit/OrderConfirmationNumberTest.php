<?php

use App\OrderConfirmationNumber;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderConfirmationNumberTest extends TestCase
{
    // can only contain uppercase letters and numbers
    // cannot contain ambiguous characters (1, I, 0, O)
    // must be 16 characters long
    // must be unique

    // ABCDEFGHJKLMNPQRTSUVWXYZ
    // 23456789

    /** @test */
    function confirmation_numbers_must_be_16_characters_long()
    {
        $confirmationNumber = (new OrderConfirmationNumber)->generate();

        $this->assertEquals(16, strlen($confirmationNumber));
    }

    /** @test */
    function confirmation_numbers_can_only_container_uppercase_letters_and_numbers()
    {
        $confirmationNumber = (new OrderConfirmationNumber)->generate();

        $this->assertRegexp('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    /** @test */
    function confirmation_numbers_cannot_contain_ambiguous_characters()
    {
        $confirmationNumber = (new OrderConfirmationNumber)->generate();

        $this->assertNotContains('I', $confirmationNumber);
        $this->assertNotContains('1', $confirmationNumber);
        $this->assertNotContains('O', $confirmationNumber);
        $this->assertNotContains('0', $confirmationNumber);
    }

    /** @test */
    function confirmation_numbers_must_be_unique()
    {
        $orderConfirmationNumber = new OrderConfirmationNumber;
        $confirmationNumbers = collect(range(1, 50))->map(function () use (&$orderConfirmationNumber) {
            return $orderConfirmationNumber->generate();
        });

        $this->assertCount(50, $confirmationNumbers->unique());
    }
}
