<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Post;

class PostVoter extends Voter
{
    const CREATE = 'create';
    const EDIT = 'edit';
    const REMOVE = 'remove';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::CREATE, self::EDIT, self::REMOVE))) {
            return false;
        }

        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        /** @var Post */
        $post = $subject; // $subject must be a Post instance, thanks to the supports method

        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute) {
            case self::CREATE:
// if the user is an admin, allow them to create new posts
                if ($this->decisionManager->decide($token, array('ROLE_ADMIN', 'ROLE_MODERATOR'))) {
                    return true;
                }
                break;
            case self::EDIT:
// if the user is the author of the post, allow them to edit the posts
                if ($user->getEmail() === $post->getAuthorEmail() ||
                    $this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
                    return true;
                }
                break;
            case self::REMOVE:
// if the user is the author of the post, allow them to edit the posts
                if ($user->getEmail() === $post->getAuthorEmail() ||
                $this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
                    return true;
                }
                break;
        }
        return false;
    }
}