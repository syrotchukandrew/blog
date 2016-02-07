<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Registry;


class CommentVoter extends Voter
{
    const CREATE = 'create';
    const EDIT = 'edit';
    const REMOVE = 'remove';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;
    private $doctrine;

    public function __construct(AccessDecisionManagerInterface $decisionManager, Registry $doctrine)
    {
        $this->decisionManager = $decisionManager;
        $this->doctrine = $doctrine;

    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::CREATE, self::EDIT, self::REMOVE))) {
            return false;
        }

        if (!$subject instanceof Comment) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        /** @var Comment */
        $comment = $subject; // $subject must be a Comment instance, thanks to the supports method

        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute) {
            case self::CREATE:
// if the user is an admin, allow them to create new comments
                if ($this->decisionManager->decide($token, array('ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER'))) {
                    return true;
                }
                break;
            case self::EDIT:
// if the user is the author of the comment or admin or moderator, allow them to edit the comments
                if ($user->getEmail() === $comment->getAuthorEmail() ||
                    $this->decisionManager->decide($token, array('ROLE_ADMIN')) ||
                    ($this->decisionManager->decide($token, array('ROLE_MODERATOR')) &&
                        $this->canYouDoIt($comment, $user))
                ) {
                    return true;
                }
                break;
            case self::REMOVE:
// if the user is the author of the comment or admin or moderator, allow them to edit the posts in the some order
                if ($user->getEmail() === $comment->getAuthorEmail() ||
                    $this->decisionManager->decide($token, array('ROLE_ADMIN')) ||
                    ($this->decisionManager->decide($token, array('ROLE_MODERATOR')) &&
                        $this->canYouDoIt($comment, $user)
                    )) {
                    return true;
                }
                break;
        }
        return false;
    }

    private function canYouDoIt(Comment $comment, User $user)
    {
        $commentOwner = $this->doctrine->getRepository('AppBundle:User')->
        findOneBy(array('email' => $comment->getAuthorEmail()));
        if (in_array("ROLE_ADMIN", $commentOwner->getRoles()) ||
            $comment->getPost()->getAuthorEmail() !== $user->getEmail()) {
            return false;
        }
        return true;
    }
}