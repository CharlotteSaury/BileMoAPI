<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Customer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomerVoter extends Voter
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
            && $subject instanceof \App\Entity\Customer;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $client = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$client instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Customer $customer */
        $customer = $subject;

        switch ($attribute) {
            case 'MANAGE':
                return $this->canManage($subject, $client);
                break;
        }

        return false;
    }

    public function canManage(Customer $customer, Client $client)
    {
        return $customer->getClients()->contains($client);
    }
}
