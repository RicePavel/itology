<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User {
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $user_id;
    
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
    
    /**
     * @ORM\OneToMany(targetEntity = "Message", mappedBy="user")
     */
    private $messages;
    
    public function __construct() {
        $this->messages = new ArrayCollection();
    }
    
    function getUserId() {
        return $this->user_id;
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function getMessages() {
        return $this->messages;
    }

    function setMessages($messages) {
        $this->messages = $messages;
    }


    
}

