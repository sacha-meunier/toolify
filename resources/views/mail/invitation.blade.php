<x-mail::message>
# {{ __('mail.invitation.heading', ['target' => $invitation->team->name ?? $invitation->workspace->name]) }}

{{ __('mail.invitation.invited_by_intro', ['name' => $invitation->invitedBy->name, 'email' => $invitation->invitedBy->email]) }}
@if ($invitation->team)
{{ __('mail.invitation.invited_to_team', ['team' => '**'.$invitation->team->name.'**', 'workspace' => '**'.$invitation->workspace->name.'**']) }}
@else
{{ __('mail.invitation.invited_to_workspace', ['workspace' => '**'.$invitation->workspace->name.'**']) }}
@endif
{{ __('mail.invitation.on_toolify') }}

{{ __('mail.invitation.intro') }}

<x-mail::button :url="$acceptUrl">
{{ __('mail.invitation.accept_button', ['target' => $invitation->team->name ?? $invitation->workspace->name]) }}
</x-mail::button>

{{ __('mail.invitation.outro') }}

{{ __('mail.invitation.signature') }}<br>
{{ config('app.name') }}
</x-mail::message>
