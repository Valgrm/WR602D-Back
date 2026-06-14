<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route('/logout', name: 'admin_logout')]
    public function logout(): never
    {
        throw new \LogicException('Interceptée par le firewall.');
    }

    #[Route('', name: 'admin_dashboard')]
    public function dashboard(UserRepository $users, GameRepository $games): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'user_count' => count($users->findAll()),
            'game_count' => count($games->findAll()),
        ]);
    }

    // ── Users ──────────────────────────────────────────────────────────────

    #[Route('/users', name: 'admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/users/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function userNew(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('username'));
            $user->setRoles($request->request->get('role') === 'admin' ? ['ROLE_ADMIN'] : []);
            $user->setPassword($hasher->hashPassword($user, $request->request->get('password')));
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', ['user' => null]);
    }

    #[Route('/users/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function userEdit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod('POST')) {
            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('username'));
            $user->setRoles($request->request->get('role') === 'admin' ? ['ROLE_ADMIN'] : []);
            if ($plain = $request->request->get('password')) {
                $user->setPassword($hasher->hashPassword($user, $plain));
            }
            $em->flush();
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', ['user' => $user]);
    }

    #[Route('/users/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function userDelete(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('admin_users');
    }

    // ── Games ──────────────────────────────────────────────────────────────

    #[Route('/games', name: 'admin_games')]
    public function games(GameRepository $gameRepository): Response
    {
        return $this->render('admin/games.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    #[Route('/games/new', name: 'admin_game_new', methods: ['GET', 'POST'])]
    public function gameNew(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        if ($request->isMethod('POST')) {
            $game = new Game();
            $game->setScore((int) $request->request->get('score'));
            if ($userId = $request->request->get('user_id')) {
                $game->setUser($userRepository->find($userId));
            }
            $em->persist($game);
            $em->flush();
            return $this->redirectToRoute('admin_games');
        }

        return $this->render('admin/game_form.html.twig', [
            'game' => null,
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/games/{id}/edit', name: 'admin_game_edit', methods: ['GET', 'POST'])]
    public function gameEdit(Game $game, Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        if ($request->isMethod('POST')) {
            $game->setScore((int) $request->request->get('score'));
            $game->setUser($userRepository->find($request->request->get('user_id')));
            $em->flush();
            return $this->redirectToRoute('admin_games');
        }

        return $this->render('admin/game_form.html.twig', [
            'game' => $game,
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/games/{id}/delete', name: 'admin_game_delete', methods: ['POST'])]
    public function gameDelete(Game $game, EntityManagerInterface $em): Response
    {
        $em->remove($game);
        $em->flush();
        return $this->redirectToRoute('admin_games');
    }
}
