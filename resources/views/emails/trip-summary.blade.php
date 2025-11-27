@component('mail::message')
# Trip Completed Successfully! 🚗

Dear {{ $booking->user->name }},

Your trip has ended. Here are the details:

**Car:** {{ $booking->car->brand }} {{ $booking->car->model }}
**Duration:** {{ $booking->start_time->diffForHumans($booking->end_time) }}
**Distance Traveled:** {{ $booking->distance_traveled }} km
**Total Amount:** {{ $booking->total_price }} EGP

Thank you for using SmartGo!
We hope to see you again soon.

@component('mail::button', ['url' => url('/my-bookings')])
View My Bookings
@endcomponent

Best regards,
SmartGo Team
@endcomponent
