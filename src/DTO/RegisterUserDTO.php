<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    #[Assert\NotBlank(message: "Email обязателен")]
    #[Assert\Email(message: "Введите корректный email")]
    public string $email = '';

    #[Assert\NotBlank(message: "Пароль обязателен")]
    #[Assert\Length(
        min: 6,
        minMessage: "Пароль должен быть не менее {{ limit }} символов"
    )]
    public string $password = '';
}
