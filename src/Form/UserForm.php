<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType
{
    /**
     * Initialise les composants du formulaire.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
        ;

        if (!$options['disable_email']) {
            $builder->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ]);
        }

        if (!$options['disable_roles']) {
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'expanded' => true,
                'multiple' => true,
            ]);
        }

        if (!$options['disable_password']) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
            ]);
        }
    }

    /**
     * Initialise la configuration du formulaire.
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'disable_email' => false,
            'disable_roles' => false,
            'disable_password' => false,
        ]);

        $resolver->setAllowedTypes('disable_email', 'bool');
        $resolver->setAllowedTypes('disable_roles', 'bool');
        $resolver->setAllowedTypes('disable_password', 'bool');
    }
}
