<?php


namespace App\Controller;


use App\Entity\Users\Owner;
use App\Entity\Users\Tenant;
use App\Form\OwnerType;
use App\Form\TenantType;
use App\Security\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/register")
 */
class RegisterController extends AbstractController
{
    public function __construct() { }

    /**
     * @Route("/choice", name="register_choice")
     */
    public function registerViews(){
        return $this->render('Authentication/register-views.html.twig');
    }

    /**
     * @Route("/tenant", name="tenant_register")
     */
    public function registerTenant(UserPasswordEncoderInterface $passwordEncoder,
                             Request $request,
                             TokenGenerator $tokenGenerator)
    {
        $tenant = new Tenant();

        $form = $this->createForm(TenantType::class, $tenant);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password  = $passwordEncoder->encodePassword($tenant, $tenant->getPlainPassword());
            $tenant->setPassword($password);
            $tenant->setConfirmationToken($tokenGenerator->getRandomSecureToken(25));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tenant);
            $entityManager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('Authentication/register-tenant.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/owner", name="owner_register")
     */
    public function registerOwner(UserPasswordEncoderInterface $passwordEncoder,
                                     Request $request,
                                     TokenGenerator $tokenGenerator)
    {
        $owner = new Owner();

        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password  = $passwordEncoder->encodePassword($owner, $owner->getPlainPassword());
            $owner->setPassword($password);
            $owner->setConfirmationToken($tokenGenerator->getRandomSecureToken(25));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($owner);
            $entityManager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('Authentication/register-owner.html.twig',[
            'form' => $form->createView()
        ]);
    }
}