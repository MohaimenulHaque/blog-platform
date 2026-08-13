<x-mail::message>
# Contact form submission

Someone submitted the contact form on {{ config('app.name') }}.

**Name:** {{ $name }}

**Email:** {{ $email }}

**Subject:** {{ $subject }}

**Message:**

{{ $message }}

<x-mail::button :url="'mailto:'.$email">
Reply to {{ $name }}
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
