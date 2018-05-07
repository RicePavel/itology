<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 */
class AddMessageForm {
    
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(max = 4000)
     */
    protected $message_text;

    /**
     * @ORM\Column(type = "string", length=256)
     * @Assert\NotBlank()
     * @Assert\Length(max = 80)
     */
    protected $name;
    
    /**
     * @ORM\Column(type = "string", length=256)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;
  
    public function getMessageText() {
        return $this->message_text;
    }
    
    public function setMessageText($text) {
        $this->message_text = $text;
    }
 
     function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }
    
    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }
    
}



