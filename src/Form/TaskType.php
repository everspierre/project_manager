<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    /**
     * Initialise les composants du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom',
            ])
            ->add('description', TextType::class, [
                'required' => true,
                'label' => 'Description',
            ])
            ->add('startingDate', DateType::class, [
                'required' => true,
                'label' => 'Date de début',
            ])
            ->add('endingDate', DateType::class, [
                'required' => false,
                'label' => 'Date de fin',
            ])
            ->add('pendingFiles', FileType::class, [
                'multiple' => true,
                'required' => false,
                'label' => 'Fichier(s)',
            ])
        ;

        if (!$options['disable_state']) {
            $builder->add('state', ChoiceType::class, [
                'required' => true,
                'label' => 'Statut',
                'choices' => [
                    'En attente' => Task::STATE_WAITING,
                    'En cours' => Task::STATE_RUNNING,
                    'Terminée' => Task::STATE_COMPLETED,
                    'Annulée' => Task::STATE_CANCELLED,
                    'Supprimée' => Task::STATE_REMOVED,
                ],
            ]);
        }
    }

    /**
     * Initialise la configuration du formulaire.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'disable_state' => false,
        ]);

        $resolver->setAllowedTypes('disable_state', 'bool');
    }
}
