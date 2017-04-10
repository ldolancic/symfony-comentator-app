<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Comment;
use AppBundle\Form\UserCommentType;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * @Route("/", name="show_user_list")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findBy(array(), array('username' => 'ASC'));

        return $this->render('user/index.html.twig', compact('users'));
    }

    /**
     * @Route("/add-comment", name="add_comment")
     */
    public function addCommentAction(Request $request)
    {
        $form = $this->createForm(UserCommentType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();

            // check if user with this username already exists in db
            $user = $em->getRepository('AppBundle:User')->findOneByUsername($data['username']);

            if (!$user) {
                $user = User::resolveFromArray($data);
                $em->persist($user);
            }

            $comment = Comment::resolveFromArray($data);
            $comment->setUser($user);

            $em->persist($comment);
            $em->flush();

            $this->addFlash(
                'notification',
                'Your comment has been saved!'
            );

            return $this->redirect(
                $this->generateUrl('show_user_comments', [
                    'username' => $user->getUsername(),
                    'page' => 1
                ])
            );
        }

        return $this->render('user/addComment.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * User comments paginated and ordered in descending order by creation time
     *
     * Matches both /{username}/1 and /{username}/
     *
     * @Route("/{username}/{page}", name="show_user_comments", requirements={"page": "\d+"})
     */
    public function showAction(User $user, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $resultsPerPage = $this->container->getParameter('results_per_page');

        $totalComments = $em->getRepository('AppBundle:Comment')->countPerUser($user);

        $currentPageComments = $em
            ->getRepository('AppBundle:Comment')
            ->paginatedPerUser(
                $user,
                $page,
                $resultsPerPage
            );

        if (!$currentPageComments) {
            throw new NotFoundHttpException();
        }

        return $this->render('user/show.html.twig', compact('currentPageComments', 'user', 'totalComments', 'resultsPerPage'));
    }
}
