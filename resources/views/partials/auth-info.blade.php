{{-- You make me cry with your lack of autocompletion, PHPStorm :( --}}
{{-- See \Korobi\Authentication\UserAuthenticationInterface --}}
@if ($user->isAuthenticated())
    <h2>Hi {{{ $user->getUsername() }}}!</h2>

    <p>Your unique id is {{{ $user->getUniqueIdentifier() }}}.</p>
    @if ($user->isAdmin())
        <p>I think you're an administrator.</p>
    @else
        <p>You have no special permissions, sorry :(.</p>
    @endif
    <p>You can now <a href="/auth/logout">logout</a>, if you wish.</p>
@else
    <p>I don't think you're logged in.</p>
@endif