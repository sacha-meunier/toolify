<x-mail::message>
# You're invited to {{ $invitation->team->name ?? $invitation->workspace->name }}

{{ $invitation->invitedBy->name }} ({{ $invitation->invitedBy->email }}) has invited you to join
@if ($invitation->team)
the **{{ $invitation->team->name }}** team in the **{{ $invitation->workspace->name }}** workspace
@else
the **{{ $invitation->workspace->name }}** workspace
@endif
on Toolify.

Click below to get started. You'll create your account and be added to the right place in one flow, no need to sign up first.

<x-mail::button :url="$acceptUrl">
Join {{ $invitation->team->name ?? $invitation->workspace->name }}
</x-mail::button>

Use Toolify to organize your tools, run surveys, and keep your team's stack in one place.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
