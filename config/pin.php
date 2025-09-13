<?php

function pin($length = 16)
{
    // Define the characters that can be used in the ID (digits only)
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    // Generate the random ID
    for ($i = 0; $i < $length; $i++) {
        $randomIndex = random_int(0, $charactersLength - 1);
        $randomString .= $characters[$randomIndex];
    }

    return $randomString;
}
