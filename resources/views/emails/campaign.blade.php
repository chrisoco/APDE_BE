@component('mail::message')
# Hello {{ $prospect->gender === 'male' ? 'Mr.' : 'Ms.' }} {{ $prospect->first_name }} {{ $prospect->last_name }}

We have an exciting offer from Hotel Grand Pilatus just for you!

{{ $campaign->description ?? 'Discover our exclusive offers and experiences designed with you in mind.' }}

@component('mail::button', ['url' => $trackingUrl])
View Exclusive Offer
@endcomponent

We look forward to welcoming you to Hotel Grand Pilatus.

Best regards,<br>
Your Hotel Grand Pilatus Team
@endcomponent