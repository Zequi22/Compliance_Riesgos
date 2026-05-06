<?php

namespace Tests\Unit;

use App\Models\Action;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_identifies_overdue_actions()
    {
        // Case 1: Commitment date is in the past and status is not closed
        $action = new Action([
            'commitment_date' => Carbon::yesterday(),
            'status' => Action::STATUS_PENDIENTE,
        ]);
        $this->assertTrue($action->isOverdue());

        // Case 2: Commitment date is today and status is not closed (not overdue yet)
        $action->commitment_date = Carbon::today();
        $this->assertFalse($action->isOverdue());

        // Case 3: Commitment date is in the future
        $action->commitment_date = Carbon::tomorrow();
        $this->assertFalse($action->isOverdue());

        // Case 4: Commitment date is in the past but status is closed
        $action->commitment_date = Carbon::yesterday();
        $action->status = Action::STATUS_CERRADA;
        $this->assertFalse($action->isOverdue());

        // Case 5: No commitment date
        $action->commitment_date = null;
        $action->status = Action::STATUS_PENDIENTE;
        $this->assertFalse($action->isOverdue());
    }
}
