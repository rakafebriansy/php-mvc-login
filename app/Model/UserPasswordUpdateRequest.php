<?php

namespace rakafebriansy\phpmvc\Model;

class UserPasswordUpdateRequest
{
    public ?string $id = null;
    public ?string $old_password = null;
    public ?string $new_password = null;
}

?>