<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FormulaireCreationProfilType extends AbstractType
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = $this->roleRepository->findAll();

        $choices = [];
        foreach ($roles as $role) {
            $choices[$role->getNom()] = $role;
        }

        $builder
            ->add('email', EmailType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de remplir une adresse email valide',
                    ]),
                    new Assert\Email([
                        'message' => 'L\'adresse n\'est pas une adresse valide',
                        'mode' => 'strict'
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'Les mots de passes ne sont pas identiques',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de remplir votre mot de passe',
                    ]),
                    new Assert\Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe est trop court',
                        'max' => 4096,
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).*$/',
                        'message' => 'Votre mot de passe doit contenir au moins 12 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un nom d\'utilisateur',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z\s]+$/',
                        'message' => 'Veuillez saisir un nom d\'utilisateur correct',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un prénom d\'utilisateur',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z\s]+$/',
                        'message' => 'Veuillez saisir un prénom d\'utilisateur correct',
                    ]),
                ],
            ])
            ->add('role', ChoiceType::class, [
                'choices' => $choices,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => false, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
