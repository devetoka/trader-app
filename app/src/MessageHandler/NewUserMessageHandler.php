<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\NewUserMessage;
use App\Service\UserEmailVerificationHandler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class NewUserMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private UserEmailVerificationHandler $emailVerificationHandler
    )
    {
    }

    /**
     * @throws EntityNotFoundException
     * @throws TransportExceptionInterface
     */
    public function __invoke(NewUserMessage $message)
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($message->userId);

        if ($user === null) {
            throw new EntityNotFoundException(sprintf('User with  id %s not found', $message->userId));
        }

        $this->sendEmail($user);

    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendEmail(User $user)
    {
        $verifyEmail = $this->getEmail($user, 'Please verify your email', template: 'email/verify.html.twig');
        $verifyEmail->context([
            'tokenUrl' => $this->emailVerificationHandler->encode($user)
        ]);
        $welcomeEmail = $this->getEmail($user, 'Welcome','Welcome to our company');

        $this->mailer->send($welcomeEmail);
        $this->mailer->send($verifyEmail);
    }

    /**
     * @param User $user
     * @param string $subject
     * @param string $message
     * @param string|null $template
     * @return Email
     */
    private function getEmail(User $user, string $subject  = 'Hello', string $message = '', string $template = null): Email
    {
        $email = $template !== null ?
            (new TemplatedEmail())->htmlTemplate($template) :
            (new Email())->html($message);

        return $email
            ->to($user->getEmail())
            ->subject($subject);

    }


}