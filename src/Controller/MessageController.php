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

class MessageController extends Controller {
    
    /**
     * @Route("/message/send", name= "send_messages")
     */
    public function sendAction(Request $request, EntityManagerInterface $em) {
        //return new Response('<html><body>Test 12345</body></html>');
        
        $messageFormModel = new AddMessageForm();
        $form = $this->createFormBuilder($messageFormModel)->add('name', TextType::class)
                ->add('email', TextType::class)
                ->add('message_text', TextareaType::class)
                ->add('save', SubmitType::class, array('label' => 'Save Message'))->getForm();
        $form->handleRequest($request);
        
        $test = 'test output';
        if ($form->isSubmitted() && $form->isValid()) {
            $messageFormModel = $form->getData();
            $email = $messageFormModel->getEmail();

            $repository = $em->getRepository("App:User");
            $user = $repository->findOneBy(array("email" => $email));
            if ($user === null) {
                $user = new User();
                $user->setEmail($email);
                $user->setName($messageFormModel->getName());
                $em->persist($user);
            }
            $message = new Message();
            $message->setMessageText($messageFormModel->getMessageText());
            $message->setUser($user);
            $em->persist($message);
            $em->flush();
            
            $test = 'form submitted';
            return $this->redirectToRoute("send_messages");
        }
        
        return $this->render('send_message_form.html.twig', array(
            'form' => $form->createView(), 'test' => $test
        ));
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

