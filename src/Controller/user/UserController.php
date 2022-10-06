<?php

namespace App\Controller\user;

use App\Entity\Order;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\ChangePassType;
use App\Form\ProfileType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(Request $request,UserRepository $repository,FileUploader $upload,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form_profile  = null;
        /** @var User $user */
        $user = $this->getUser();
        $form_password = $this->createForm(ChangePassType::class,$user);
        $form_password->handleRequest($request);
        if ($form_password->isSubmitted() && $form_password->isValid()) {
            if ($userPasswordHasher->isPasswordValid($user,$form_password->get('courant')->getData()))
            {
                $user->setPassword($userPasswordHasher->hashPassword($user,$form_password->get('newPassword')->getData()));
                $repository->save($user, true);
                return $this->redirectToRoute('app_logout');
            }
            else{
                $this->addFlash('danger','Courant Password Not Mach ?');
                return $this->redirectToRoute('app_user_profile');
            }
        }

        if ($user->getProfile())
        {
            $profile = $user->getProfile();
            $photo = $profile->getPhoto();
            $form_profile = $this->createForm(ProfileType::class,$profile);
            $form_profile->handleRequest($request);
            if ($form_profile->isSubmitted() && $form_profile->isValid()) {
                if ($request->files->get('profile')['photo'])
                {
                    $profile->setPhoto($upload->update($form_profile->get('photo')->getData(),$this->getParameter('upload_directory'),'user',$photo));
                }
                $user->setProfile($profile);
                $repository->save($user, true);
                return $this->redirectToRoute('app_user_profile');
            }
        }
        else
        {
            $profile = new Profile();
            $form_profile = $this->createForm(ProfileType::class, $profile);
            $form_profile->handleRequest($request);
            if ($form_profile->isSubmitted() && $form_profile->isValid()) {
                if ($request->files->get('profile')['photo'])
                {
                    $profile->setPhoto($upload->upload($form_profile->get('photo')->getData(),$this->getParameter('upload_directory'),'user'));
                }
                $user->setProfile($profile);
                $repository->save($user,true);
                return $this->redirectToRoute('app_user_profile');
            }
        }

        return $this->renderForm('user/profile/index.html.twig',['form_profile' => $form_profile,'form_password' => $form_password]);
    }

}
