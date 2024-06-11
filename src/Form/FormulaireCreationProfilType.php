<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('email')
            ->add('password', PasswordType::class,)
            ->add('nom')
            ->add('prenom')
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
