<?php

namespace Pbxg33k\FlareSolverrBundle\Enum;

enum CommandEnum: string
{
    case SESSION_CREATE = 'sessions.create';
    case SESSION_LIST = 'sessions.list';
    case SESSION_DESTROY = 'sessions.destroy';
    case REQUEST_GET = 'request.get';
    case REQUEST_POST = 'request.post';
}
