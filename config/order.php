<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Order Registration Invite Code
    |--------------------------------------------------------------------------
    |
    | Parents register through a single shared link that carries this code as
    | `?invite=...`. Registration is refused unless the code matches, which
    | keeps random visitors from creating accounts that could browse children's
    | photos. Leave empty to disable the gate (registration open to anyone).
    |
    */

    'invite_code' => env('ORDER_INVITE_CODE'),
];
