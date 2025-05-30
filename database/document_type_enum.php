<?php
enum type_document
{
    case Book;
    case Disk;

    public static function fromName(string $name): type_document
    {
        foreach (self::cases() as $status) {
            if( $name === $status->name ){
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class );
    }
}