<?php

return [
    'accepted' => 'Pole :attribute musi zostać zaakceptowane.',
    'after' => 'Pole :attribute musi zawierać datę późniejszą niż :date.',
    'after_or_equal' => 'Pole :attribute musi zawierać datę równą lub późniejszą niż :date.',
    'before' => 'Pole :attribute musi zawierać datę wcześniejszą niż :date.',
    'before_or_equal' => 'Pole :attribute musi zawierać datę równą lub wcześniejszą niż :date.',
    'boolean' => 'Pole :attribute musi mieć wartość prawda albo fałsz.',
    'confirmed' => 'Potwierdzenie pola :attribute nie jest zgodne.',
    'date' => 'Pole :attribute nie jest prawidłową datą.',
    'email' => 'Pole :attribute musi zawierać prawidłowy adres e-mail.',
    'in' => 'Wybrana wartość pola :attribute jest nieprawidłowa.',
    'integer' => 'Pole :attribute musi być liczbą całkowitą.',
    'max' => [
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'string' => 'Pole :attribute nie może mieć więcej niż :max znaków.',
    ],
    'min' => [
        'numeric' => 'Pole :attribute musi mieć wartość co najmniej :min.',
        'string' => 'Pole :attribute musi mieć co najmniej :min znaków.',
    ],
    'required' => 'Pole :attribute jest wymagane.',
    'string' => 'Pole :attribute musi być tekstem.',
    'unique' => 'Taka wartość pola :attribute już istnieje.',

    'password' => [
        'letters' => 'Hasło musi zawierać co najmniej jedną literę.',
        'mixed' => 'Hasło musi zawierać co najmniej jedną małą i jedną wielką literę.',
        'numbers' => 'Hasło musi zawierać co najmniej jedną cyfrę.',
        'symbols' => 'Hasło musi zawierać co najmniej jeden znak specjalny, np. ! @ # $ %.',
        'uncompromised' => 'To hasło pojawiło się w publicznym wycieku danych. Wybierz inne hasło.',
    ],

    'attributes' => [
        'name' => 'imię i nazwisko',
        'email' => 'adres e-mail',
        'password' => 'hasło',
        'password_confirmation' => 'potwierdzenie hasła',
        'access_level' => 'poziom dostępu',
        'authorization_number' => 'numer upoważnienia',
        'person_name' => 'osoba',
        'person_identifier' => 'identyfikator pracownika',
        'position' => 'stanowisko',
        'scope' => 'zakres upoważnienia',
        'issued_at' => 'data wydania',
        'valid_until' => 'data ważności',
        'revocation_reason' => 'powód odwołania',
    ],
];
