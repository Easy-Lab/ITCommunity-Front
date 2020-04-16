<?php


namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MailService
{
    private $translator;
    private $templating;
    private $container;
    private $logger;

    public function __construct(TranslatorInterface $translator, Environment $templating, ContainerInterface $container, LoggerInterface $logger)
    {
     $this->translator = $translator;
     $this->container = $container;
     $this->templating = $templating;
     $this->logger = $logger;
    }

    public function sendEvaluationMail(array $user, array $contact, string $hash)
    {
            $message = $this->send('contact.evaluation', $contact['email'], [
                'user' => $user,
                'contact' => $contact,
                'hash'=>$hash
            ]);
        return true;
    }

    public function sendInvitationMail(array $user, array $contact, string $hash)
    {
        $message = $this->send('contact.evaluation', $contact['email'], [
            'user' => $user,
            'contact' => $contact,
            'hash'=>$hash
        ]);
        return true;
    }

    public function sendActivationMail(array $user, string $hash)
    {
        $message = $this->send('contact.evaluation', $user['email'], [
            'user' => $user,
            'hash'=>$hash
        ]);
        return true;
    }

    public function sendPrivateMessageMail(array $user, array $message)
    {
        $messageBody = $this->send('contact.evaluation', $user['email'], [
            'user' => $user,
            'message' => $message,
        ], [$message['contact']['email']]);
        return true;
    }

    public function send($slug, $to, array $vars, $replyTo = [], $object=null, $mailAdmin=null)
    {
        $success = false;
        $error = null;
        try {
            if(!is_array($to)) $to = [$to];
            if (!is_array($replyTo)) $replyTo = [$replyTo];
            // $to est désormais forcément un tableau d'adresses mail
            //$to = ['contact@lesambassadeurstryba.fr']; // TODO : delete

            // Objet du mail
            /** @Ignore */

            if (is_null($object)) {
                $subject = $this->translator->trans("mail.$slug.subject");
            }else{
                $subject = $object;
            }
            $mailFrom = null;
            if ($mailAdmin){
                $mailFrom = $this->container->getParameter('mailer_from_contact');
            }else{
                $mailFrom = $this->container->getParameter('mailer_from_address');
            }
            // Chemin du template du mail
            $template = "mails/".str_replace('.', '/', $slug).".html.twig";
            $body = $this->templating->render($template, $vars);
            $message = (new \Swift_Message())
                ->setSubject($subject)
                ->setFrom([
                    $mailFrom => $this->container->getParameter('mailer_from_label')
                ])
                ->setTo($to)
                ->setReplyTo($replyTo)
                ->setBody($body ,'text/html');
            return $message;
        }
        catch(\Throwable $e) {
            $error = $e->getMessage();
            $this->logger->error("MailService: $error");
        }

        return null;
    }

}
