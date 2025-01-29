@component('mail::message')
# Review Verification

Hello {{ $reviewerName }},

Thank you for submitting your review! Please verify your review by clicking one of the buttons below.

@component('mail::button', ['url' => $approveUrl, 'color' => 'success'])
Approve Review
@endcomponent

@component('mail::button', ['url' => $deleteUrl, 'color' => 'error'])
Delete Review
@endcomponent

Thank you for helping us keep our platform trustworthy!

Regards,  
{{ config('app.name') }}
@endcomponent
