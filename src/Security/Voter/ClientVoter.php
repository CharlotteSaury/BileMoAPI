<?php

namespace App\Security\Voter;

use App\Entity\Client;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientVoter extends Voter
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['MANAGE'])
            && $subject instanceof \App\Entity\Client;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $currentUser = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Client $client */
        $client = $subject;

        switch ($attribute) {
            case 'MANAGE':
                return $this->canManage($client, $currentUser);
                break;
        }

        return false;
    }

    public function canManage(Client $client, Client $currentUser)
    {
        return $client === $currentUser;
    }
}
