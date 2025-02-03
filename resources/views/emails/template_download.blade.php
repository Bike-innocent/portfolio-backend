@component('mail::message')
# @if($isPaid) Thank You for Your Purchase! @else Your Free Download @endif

Hello,  

@if($isPaid)
Thank you for purchasing our template. Below is your download link:
@else
Thank you for downloading our free template. Below is your download link:
@endif

@component('mail::button', ['url' => $downloadLink, 'color' => 'blue'])
Download Template
@endcomponent

If you have any questions, feel free to [contact us](https://chibuikeinnocent.tech/contact).  

Thanks,  
{{ config('app.name') }}
@endcomponent
