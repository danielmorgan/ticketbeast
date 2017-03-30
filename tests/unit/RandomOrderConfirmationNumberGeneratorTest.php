<?php

use App\RandomOrderConfirmationNumberGenerator;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderConfirmationNumberGeneratorTest extends TestCase
{
    // can only contain uppercase letters and numbers
    // cannot contain ambiguous characters (1, I, 0, O)
    // must be 24 characters long
    // must be unique
    //
    // ABCDEFGHJKLMNPQRTSUVWXYZ
    // 23456789

    /**
     * @var \App\RandomOrderConfirmationNumberGenerator
     */
    private $generator;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    function setUp()
    {
        parent::setUp();

        $this->generator = new RandomOrderConfirmationNumberGenerator;
    }


    /** @test */
    function confirmation_numbers_must_be_24_characters_long()
    {
        $confirmationNumber = $this->generator->generate();

        $this->assertEquals(24, strlen($confirmationNumber));
    }

    /** @test */
    function confirmation_numbers_can_only_container_uppercase_letters_and_numbers()
    {
        $confirmationNumber = $this->generator->generate();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    /** @test */
    function confirmation_numbers_cannot_contain_ambiguous_characters()
    {
        $confirmationNumber = $this->generator->generate();

        $this->assertNotContains('I', $confirmationNumber);
        $this->assertNotContains('1', $confirmationNumber);
        $this->assertNotContains('O', $confirmationNumber);
        $this->assertNotContains('0', $confirmationNumber);
    }

    /** @test */
    function confirmation_numbers_must_be_unique()
    {
        $confirmationNumbers = collect(range(1, 100))->map(function () {
            return $this->generator->generate();
        });

        $this->assertCount(100, $confirmationNumbers->unique());
    }
}
