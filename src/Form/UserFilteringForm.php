<?php

namespace App\Form;

use App\Entity\UserFiltering;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilteringForm extends AbstractType
{
    /**
     * Initialise les composants du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
            ])
            ->add('lastname', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email',
                ],
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'required' => false,
                'label' => false,
                'placeholder' => 'Rôle',
            ])
        ;
    }

    /**
     * Initialise la configuration du formulaire.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFiltering::class,
            'method' => 'get',
            'csrf_protection' => false,
        ]);
    }
}
