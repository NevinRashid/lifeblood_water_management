<?php

namespace Modules\TicketsAndReforms\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\TicketsAndReforms\Models\TroubleTicket;

class TroubleTicketNotRejected implements Rule
{
    /**
     * This rule verify that if the trouble ticket selected for reform assignment has a rejected status,
     * then it is not possible to assign the reform to it
     *
     * @param $attribute, $value
     *
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        $ticket = TroubleTicket::find($value);
        return $ticket && $ticket->status !== 'rejected';
    }

        public function message(): string
    {
        return "This trouble ticket is rejected and cannot be assigned a reform.";
    }
}

