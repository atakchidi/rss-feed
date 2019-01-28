<?php


namespace App\ArgumentResolver;


use App\DTO\Credentials;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CredentialsArgumentResolver implements ArgumentValueResolverInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return Credentials::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {

        $credentials = new Credentials($request);
        $errors = $this->validator->validate($credentials);

        // throw bad request exception in case of invalid request data
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        yield $credentials;
    }
}