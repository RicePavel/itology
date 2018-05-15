<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\AddMessageForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Component\MathExpression;
use App\Component\EmailSender;
use App\Settings\MailSettings;

class MessageController extends Controller {
    
    /**
     * @Route("/message/send", name= "send_messages")
     */
    public function sendAction(Request $request, EntityManagerInterface $em, SessionInterface $session) {
        
        $mathExpression = new MathExpression();
        $mathResult = $mathExpression->getResult();
        $mathText = $mathExpression->getText();
        
        $messageFormModel = new AddMessageForm();
        $form = $this->createFormBuilder($messageFormModel)->add('name', TextType::class)
                ->add('email', TextType::class)
                ->add('message_text', TextareaType::class)
                ->add('mathResult', TextType::class, array('label' => $mathText))
                ->add('save', SubmitType::class, array('label' => 'Save Message'))->getForm();
        $form->handleRequest($request);
        
        $test = 'test output';
        $error = '';
        if ($form->isSubmitted() && $form->isValid()) {
             
            $ip = $request->getClientIp();
            $checkedByIp = $this->checkTimeByIp($ip, $em);
            if (!$checkedByIp) {
                $error = 'Слишком много запросов с вашего адреса, пожалуйста подождите минуту';
                return $this->render('send_message_form.html.twig', array(
                    'form' => $form->createView(), 'error' => $error
                ));
            }
            
            $messageFormModel = $form->getData();
            
            $mathResultFromSession = (string) $session->get('mathResult');
            $mathResultFromUser = $messageFormModel->getMathResult();
            if ($mathResultFromSession !== $mathResultFromUser) {
                $error = 'Неправильный результат математического выражения!';
                return $this->render('send_message_form.html.twig', array(
                   'form' => $form->createView(), 'error' => $error
                ));
            }
            
            $email = $messageFormModel->getEmail();
            $repository = $em->getRepository("App:User");
            $user = $repository->findOneBy(array("email" => $email));
            if ($user !== null) {
                $timeChecked = $this->checkTimeByUser($user->getUserId(), $em);
                if (!$timeChecked) {
                    $error = 'Слишком много запросов с вашего email, пожалуйста подождите минуту';
                    return $this->render('send_message_form.html.twig', array(
                        'form' => $form->createView(), 'error' => $error
                    ));
                }
            }
            if ($user === null) {
                $user = new User();
                $user->setEmail($email);
                $user->setName($messageFormModel->getName());
                $em->persist($user);
            }
            $message = new Message();
            $message->setMessageText($messageFormModel->getMessageText());
            $message->setUser($user);
            $message->setInsertDate(new \DateTime());
            $message->setIp($ip);
            $em->persist($message);
            $em->flush();
            
            $this->sendEmails($email, $user->getName(), $user->getUserId(), $message->getMessageText(), $request->getHost());
            
            $test = 'form submitted';
            return $this->redirectToRoute("send_messages");
        }
        
        $session->set('mathResult', $mathResult);
        
        return $this->render('send_message_form.html.twig', array(
            'form' => $form->createView(), 'error' => $error
        ));
    }
    
    private function checkTimeByIp($ip, EntityManagerInterface $em) {
        $query = $em->createQuery(
                'Select m
                 From App:Message m
                 Where m.ip = :ip
                 Order by m.insert_date Desc
                ')->setParameter('ip', $ip);
        $messages = $query->getResult();
        if (count($messages) > 0) {
            $message = $messages[0];
            $insertDate = $message->getInsertDate();
            if ($insertDate !== null) {
                $nowDate = new \DateTime();
                $interval = $nowDate->diff($insertDate);
                if ($interval->i < 1) {
                    return false;
                }
            }
        }
        return true;
    }
    
    private function checkTimeByUser($userId, EntityManagerInterface $em) {
        $query = $em->createQuery(
                'Select m
                 From App:Message m 
                 Where m.user = :user
                 Order by m.insert_date Desc
                ')->setParameter('user', $userId);
        $messages = $query->getResult();
        if (count($messages) > 0) {
            $message = $messages[0];
            $insertDate = $message->getInsertDate();
            if ($insertDate !== null) {
                $nowDate = new \DateTime();
                $interval = $nowDate->diff($insertDate);
                if ($interval->i < 1) {
                    return false;
                }
            }
        }
        return true;
    }
    
    private function sendEmails($email, $userName, $userId, $messageText, $host) {
        $subject = 'Новое обращение';
        $bodyToUser = "<div>Вы добавили новое обращение: " . $messageText . "</div>";
            
        $href = $host . "/itology/public/index.php/messages/" . $userId;
        
        $linkToMessages = "<a href='" . $href . "'>Сообщения пользователя</a>";
        $bodyToAdmin = "<div>" . $userName . " добавил новое обращение: " . $messageText . ". " . $linkToMessages . " </div>";
 
        $sender = new EmailSender(MailSettings::OUR_ADDRESS, MailSettings::SMTP_HOST, MailSettings::SMTP_PORT, MailSettings::SMTP_USERNAME, MailSettings::SMTP_PASSWORD);
        $sender->sendEmail($email, $bodyToUser, $subject);
        $sender->sendEmail(MailSettings::ADMIN_EMAIL, $bodyToAdmin, $subject);
    }
     
    /**
     * @Route("/messages/{userId}")
     */
    public function searchByUser($userId, EntityManagerInterface $em) {
        $repository = $em->getRepository("App:Message");
        $messages = $repository->findBy(array("user" => $userId));
        return $this->render('messages.html.twig', array(
            'messages' => $messages
        ));
    }
    
}

