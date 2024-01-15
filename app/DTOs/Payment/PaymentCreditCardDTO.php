<?php

namespace App\DTOs\Payment;

class PaymentCreditCardDTO extends BasePaymentDTO implements PaymentDTOInterface
{
    public CreditCardDTO $creditCard;
    public CreditCardHolderInfoDTO $holderInfo;

    public function __construct($customer_id, $amount, $due_date, $method, $creditCard, $holderInfo)
    {
        parent::__construct($customer_id, $amount, $due_date, $method);
        $this->creditCard = $creditCard;
        $this->holderInfo = $holderInfo;
    }
}
