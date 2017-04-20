<?php

use App\HashidsTicketCodeGenerator;
use App\Ticket;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HashidsTicketCodeGeneratorTest extends TestCase
{
    /** @test */
    function ticket_codes_are_at_least_6_characters_long()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertGreaterThanOrEqual(6, strlen($code));
    }

    /** @test */
    function ticket_codes_can_only_container_uppercase_letters()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertRegexp('/^[A-Z]+$/', $code);
    }

    /** @test */
    function ticket_codes_for_the_same_ticket_id_are_the_same()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt');
        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }

    /** @test */
    function ticket_codes_for_different_ticket_ids_are_different()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt');
        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    function ticket_codes_generated_wth_different_salts_are_different()
    {
        $ticketCodeGenerator1 = new HashidsTicketCodeGenerator('testsalt1');
        $ticketCodeGenerator2 = new HashidsTicketCodeGenerator('testsalt2');

        $code1 = $ticketCodeGenerator1->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator2->generateFor(new Ticket(['id' => 1]));
        
        $this->assertNotEquals($code1, $code2);
    }
}
