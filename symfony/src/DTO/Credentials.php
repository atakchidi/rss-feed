<?php


namespace App\DTO;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class Credentials
{
    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    public $email;

    /**
     * @Assert\Length(min=6)
     * @Assert\NotBlank()
     */
    public $password;

    public function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
        $this->password = $request->request->get('password');
    }

}