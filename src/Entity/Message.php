<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 */
class Message {
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $message_id;
    
    /**
     * @ORM\ManyToOne(targetEntity = "User", inversedBy="messages")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName="user_id")
     */
    protected $user;
    
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(max = 4000)
     */
    protected $message_text;

    function getMessageId() {
        return $this->message_id;
    }

    function setMessageId($message_id) {
        $this->message_id = $message_id;
    }

    function getUser() {
        return $this->user;
    }

    function setUser($user) {
        $this->user = $user;
    }
        
    public function getMessageText() {
        return $this->message_text;
    }
    
    public function setMessageText($text) {
        $this->message_text = $text;
    }
    
}

