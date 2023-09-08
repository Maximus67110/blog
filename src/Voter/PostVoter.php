<?php

namespace App\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Post $post */
        $post = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($post, $user),
            self::DELETE => $this->canDelete($post, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEdit(Post $post, User $user): bool
    {
        return $user === $post->getUser() || in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    }

    private function canDelete(Post $post, User $user): bool
    {
        return $user === $post->getUser() || in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    }
}