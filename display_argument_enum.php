<?php
enum display_argument
{
    case Update;
    case Borrow;
    case Return;

    public static function fromName(string $name): display_argument
    {
        foreach (self::cases() as $status) {
            if( $name === $status->name ){
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class );
    }
}